<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessMenuImagesJob;
use App\Models\Restaurant;
use App\Models\Section;
use App\Support\Guards\AccessGuardTrait;
use App\Support\Import\MenuPatchApplier;
use App\Support\Import\MenuPatchValidator;
use App\Support\Import\SafeZipExtractor;
use App\Support\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuImportController extends Controller
{
    use AccessGuardTrait;

    public function index(Restaurant $restaurant)
    {
        return view('admin.restaurants.import', compact('restaurant'));
    }

    public function images(Restaurant $restaurant)
    {
        return view('admin.restaurants.import', compact('restaurant'));
    }

    /**
     * @throws TenantAccessException
     */
    public function importJson(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if ($resp = Permissions::denyRedirect(
            $request->user(),
            'import.menu_json'
        )) {
            return $resp;
        }

        $request->validate([
            'menu_json' => [
                'required',
                'file',
                'mimetypes:application/json,text/plain',
                'max:5120',
            ],

            // =========================
            // IMPORT MODE
            // =========================
            'import_mode' => [
                'nullable',
                'string',
                'in:create,add,update',
            ],
        ]);

        try {

            $raw = file_get_contents(
                $request->file('menu_json')->getRealPath()
            );

            $data = json_decode(
                $raw,
                true,
                512,
                JSON_THROW_ON_ERROR
            );

        } catch (\Throwable $e) {

            return $this->failWithLog($restaurant, [[
                'path' => 'root',
                'message_key' => 'admin.import.errors.invalid_json',
                'params' => [],
            ]]);
        }

        // =========================
        // FORCED MODE
        // =========================
        // IMPORTANT:
        // UI button mode has priority
        //
        // JSON mode becomes fallback only
        //
        // This allows:
        // - same JSON reused
        // - same export reused
        // - UI controls operation type
        $forcedMode = $request->input('import_mode');

        // =========================
        // VALIDATE + BUILD PLAN
        // =========================
        $validator = new MenuPatchValidator();

        $res = $validator->validate(
            $data,
            $request->user(),
            $restaurant,
            $forcedMode
        );

        $errors = $res['errors'] ?? [];
        $plan = $res['plan'] ?? [];

        if (!empty($errors)) {

            return $this->failWithLog(
                $restaurant,
                $errors
            );
        }

        // =========================
        // DRY RUN
        // =========================
        if (!empty($plan['dry_run'])) {

            $summary = $plan['summary'] ?? [
                'create' => 0,
                'add' => 0,
                'update' => 0,
                'delete' => 0,
            ];

            return back()
                ->with('import_status', 'ok')
                ->with('import_dry_run', true)
                ->with('import_summary', $summary)
                ->with(
                    'success',
                    __('admin.import.success.dry_run_done', $summary)
                );
        }

        // =========================
        // APPLY PLAN
        // =========================
        $applier = new MenuPatchApplier();

        $result = $applier->apply(
            $restaurant,
            $plan
        );

        // =========================
        // MODE LABEL
        // =========================
        $mode = $plan['mode'] ?? 'create';

        $modeLabel = match ($mode) {

            'create' => 'CREATE',
            'add' => 'ADD',
            'update' => 'UPDATE',

            // reserved
            'delete' => 'DELETE',

            default => strtoupper($mode),
        };

        return back()
            ->with('import_status', 'ok')
            ->with('import_result', $result)
            ->with('import_mode', $mode)
            ->with(
                'success',
                __('admin.import.success.import_done')
                . ' [' . $modeLabel . ']'
            );
    }

    /**
     * @throws TenantAccessException
     */
    public function downloadLog(
        Request $request,
        Restaurant $restaurant,
        string $token
    ) {
        $this->assertRestaurantAccess($request, $restaurant);

        $path = "tmp/import-logs/{$token}.txt";

        abort_unless(
            Storage::disk('local')->exists($path),
            404
        );

        return Storage::disk('local')->download($path);
    }

    private function failWithLog(
        Restaurant $restaurant,
        array $errors
    ) {

        $token = (string) Str::uuid();

        $lines = [
            'IMPORT FAILED',
            'time=' . now()->toDateTimeString(),
            'restaurant_id=' . $restaurant->id,
            'errors=' . count($errors),
            str_repeat('-', 60),
        ];

        foreach ($errors as $e) {

            $lines[] =
                ($e['path'] ?? '-') .
                ' | ' .
                ($e['message_key'] ?? 'unknown') .
                ' | ' .
                json_encode(
                    $e['params'] ?? [],
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
                );
        }

        Storage::disk('local')->put(
            "tmp/import-logs/{$token}.txt",
            implode("\n", $lines)
        );

        return back()
            ->with('import_status', 'error')
            ->with('import_log_token', $token)
            ->with('import_log_errors', $errors)
            ->withErrors([
                'menu_json' => __(
                    'admin.import.errors.import_failed_open_log'
                ),
            ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function importZip(
        Request $request,
        Restaurant $restaurant
    ) {

        $this->assertRestaurantAccess(
            $request,
            $restaurant
        );

        $this->assertFeature(
            $request,
            $restaurant,
            'images'
        );

        if ($resp = Permissions::denyRedirect(
            $request->user(),
            'import.images_zip'
        )) {
            return $resp;
        }

        Cache::forget("import:status:{$restaurant->id}");
        Cache::forget("import:result:{$restaurant->id}");

        $request->validate([
            'assets_zip' => [
                'required',
                'file',
                'mimes:zip',
            ],
        ]);

        $file = $request->file('assets_zip');

        $isSuper = (bool) $request->user()?->is_super_admin;

        $maxMb = $isSuper
            ? config('import.zip.max_mb.super_admin', 100)
            : config('import.zip.max_mb.user', 50);

        if (
            $file &&
            $file->getSize() > ($maxMb * 1024 * 1024)
        ) {

            return back()->with(
                'warning',
                __('admin.import.errors.zip_too_large', [
                    'size' => $maxMb,
                ])
            );
        }

        try {

            $result = (new SafeZipExtractor())->extract(
                $file,
                $restaurant->id
            );

            Cache::put(
                "import:status:{$restaurant->id}",
                'queued',
                now()->addMinutes(30)
            );

            ProcessMenuImagesJob::dispatch(
                $restaurant->id
            );

        } catch (\Throwable $e) {

            Log::error('ZIP IMPORT FAILED', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Cache::put(
                "import:status:{$restaurant->id}",
                'error',
                now()->addMinutes(30)
            );

            return back()->withErrors([
                'assets_zip' => $e->getMessage(),
            ]);
        }

        return back()->with(
            'success',
            __('admin.import.status.zip')
        );
    }

    /**
     * @throws TenantAccessException
     */
    protected function assertFeature(
        Request $request,
        Restaurant $restaurant,
        string $feature
    ): void {

        $user = $request->user();

        if (!$user) {
            throw new TenantAccessException(
                __('permissions.no_access')
            );
        }

        if (
            !$user->is_super_admin &&
            (int) $user->restaurant_id !== (int) $restaurant->id
        ) {
            throw new TenantAccessException(
                __('permissions.no_access')
            );
        }

        if (!$restaurant->feature($feature)) {
            throw new TenantAccessException(
                __('permissions.no_access')
            );
        }
    }

    public function downloadMenuJson(
        Request $request,
        Restaurant $restaurant
    ) {

        $this->assertRestaurantAccess(
            $request,
            $restaurant
        );

        $categories = Section::with([
            'translations',
            'children.translations',
            'children.items.translations',
            'items.translations',
        ])
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $payload = [

            // =========================
            // EXPORT MODE
            // =========================
            'mode' => 'create',

            'categories' => $categories->map(function ($cat) {

                return [
                    'key' => $cat->key,
                    'type' => $cat->type,
                    'is_active' => (bool) $cat->is_active,

                    'translations' => $cat->translations
                        ->mapWithKeys(fn ($t) => [
                            $t->locale => [
                                'title' => $t->title,
                                'description' => $t->description,
                            ],
                        ]),

                    'items' => $cat->items->map(fn ($it) => [
                        'key' => $it->key,
                        'price' => $it->price !== null
                            ? (string) $it->price
                            : null,
                        'currency' => $it->currency,
                        'is_active' => (bool) $it->is_active,
                        'meta' => $it->meta ?? [],

                        'translations' => $it->translations
                            ->mapWithKeys(fn ($t) => [
                                $t->locale => [
                                    'title' => $t->title,
                                    'description' => $t->description,
                                    'details' => $t->details ?? null,
                                ],
                            ]),
                    ]),

                    'subcategories' => $cat->children->map(
                        fn ($sub) => [
                            'key' => $sub->key,
                            'type' => $sub->type,
                            'is_active' => (bool) $sub->is_active,

                            'translations' => $sub->translations
                                ->mapWithKeys(fn ($t) => [
                                    $t->locale => [
                                        'title' => $t->title,
                                        'description' => $t->description,
                                    ],
                                ]),

                            'items' => $sub->items->map(fn ($it) => [
                                'key' => $it->key,
                                'price' => $it->price !== null
                                    ? (string) $it->price
                                    : null,
                                'currency' => $it->currency,
                                'is_active' => (bool) $it->is_active,
                                'meta' => $it->meta ?? [],

                                'translations' => $it->translations
                                    ->mapWithKeys(fn ($t) => [
                                        $t->locale => [
                                            'title' => $t->title,
                                            'description' => $t->description,
                                            'details' => $t->details ?? null,
                                        ],
                                    ]),
                            ]),
                        ]
                    ),
                ];
            }),
        ];

        $filename =
            'menu_' .
            $restaurant->slug .
            '_' .
            now()->format('Y-m-d_H-i') .
            '.json';

        return response()->streamDownload(
            function () use ($payload) {

                echo json_encode(
                    $payload,
                    JSON_PRETTY_PRINT |
                    JSON_UNESCAPED_UNICODE
                );
            },
            $filename,
            [
                'Content-Type' => 'application/json',
            ]
        );
    }

    /**
     * @throws TenantAccessException
     */
    public function status(
        Request $request,
        Restaurant $restaurant
    ) {

        $this->assertRestaurantAccess(
            $request,
            $restaurant
        );

        return response()->json([
            'status' => Cache::get(
                "import:status:{$restaurant->id}"
            ),

            'result' => Cache::get(
                "import:result:{$restaurant->id}"
            ),
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function downloadUnmatched(
        Request $request,
        Restaurant $restaurant
    ) {

        $this->assertRestaurantAccess(
            $request,
            $restaurant
        );

        $path = "tmp/import-unmatched/{$restaurant->id}.json";

        if (!Storage::disk('local')->exists($path)) {

            return back()->withErrors([
                'import' => __('admin.import.status.unmatched'),
            ]);
        }

        return Storage::disk('local')->download($path);
    }
}
