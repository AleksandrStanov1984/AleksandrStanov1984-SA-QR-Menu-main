<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use App\Models\Item;
use App\Models\ItemTranslation;

use App\Support\Permissions;
use App\Support\Import\MenuPatchValidator;
use App\Support\Import\MenuPatchApplier;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use ZipArchive;

class MenuImportController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        return view('admin.restaurants.import', [
            'restaurant' => $restaurant,
        ]);
    }

    public function images(Restaurant $restaurant)
    {
        return view('admin.restaurants.import-images', [
            'restaurant' => $restaurant,
        ]);
    }

    public function importJson(Request $request, Restaurant $restaurant)
        {
            // доступ к импорту (UI может быть скрыт, но backend обязателен)
            Permissions::abortUnless($request->user(), 'import.menu_json');

            $request->validate([
                'menu_json' => ['required', 'file', 'mimetypes:application/json,text/plain', 'max:5120'], // 5MB
            ]);

            $raw = file_get_contents($request->file('menu_json')->getRealPath());
            $data = json_decode($raw, true);

            if (!is_array($data)) {
                return $this->failWithLog(
                    $restaurant,
                    [
                        [
                            'path' => 'root',
                            'message_key' => 'admin.import.errors.invalid_json',
                            'params' => [],
                        ],
                    ]
                );
            }

            // validate patch + permissions + values (strict)
            $validator = new MenuPatchValidator();
            $res = $validator->validate($data, $request->user(), $restaurant);

            $errors = $res['errors'] ?? [];
            $plan = $res['plan'] ?? ['dry_run' => false, 'ops' => [], 'summary' => []];

            if (!empty($errors)) {
                return $this->failWithLog($restaurant, $errors);
            }

            // dry run: only report summary, do not apply
            if (!empty($plan['dry_run'])) {
                $summary = $plan['summary'] ?? ['create' => 0, 'update' => 0, 'delete' => 0];

                return back()
                    ->with('import_status', 'ok')
                    ->with('import_log_level', 'success')
                    ->with('import_dry_run', true)
                    ->with('import_summary', $summary)
                    ->with('success', __('admin.import.success.dry_run_done', $summary));
            }

            // apply (atomic)
            $applier = new MenuPatchApplier();
            $result = $applier->apply($restaurant, $plan);

            return back()
                ->with('import_status', 'ok')
                ->with('import_log_level', 'success')
                ->with('import_result', $result)
                ->with('success', __('admin.import.success.import_done'));
        }

        public function downloadLog(Request $request, Restaurant $restaurant, string $token)
        {
            Permissions::abortUnless($request->user(), 'import.menu_json');

            $path = "tmp/import-logs/{$token}.txt";
            abort_unless(Storage::disk('local')->exists($path), 404);

            return Storage::disk('local')->download($path, "import-errors-{$restaurant->id}-{$token}.txt");
        }

        /**
         * Writes error log into storage and flashes structured errors into session for modal.
         * Errors are arrays: ['path','message_key','params'] (no raw text).
         */
        private function failWithLog(Restaurant $restaurant, array $errors)
        {
            $token = (string) Str::uuid();

            // Build TXT (human readable) using translation keys (not translated here)
            // We'll store key+params; UI will translate.
            $lines = [];
            $lines[] = 'IMPORT FAILED';
            $lines[] = 'time=' . now()->toDateTimeString();
            $lines[] = 'restaurant_id=' . $restaurant->id;
            $lines[] = 'errors=' . count($errors);
            $lines[] = str_repeat('-', 70);

            foreach ($errors as $e) {
                $path = $e['path'] ?? '-';
                $key  = $e['message_key'] ?? 'admin.import.errors.unknown';
                $params = $e['params'] ?? [];

                $lines[] = $path . ' | ' . $key . ' | ' . json_encode($params, JSON_UNESCAPED_UNICODE);
            }

            Storage::disk('local')->put("tmp/import-logs/{$token}.txt", implode("\n", $lines));

            return back()
                ->with('import_status', 'error')
                ->with('import_log_level', 'error')
                ->with('import_log_token', $token)
                ->with('import_log_errors', $errors)
                ->withErrors(['menu_json' => __('admin.import.errors.import_failed_open_log')]);
        }

    public function importZip(Request $request, Restaurant $restaurant)
    {
        if (!Permissions::can($request->user(), 'import.images_zip')) {
            abort(403);
        }

        Permissions::abortUnless($request->user(), 'import.images_zip');

        $zip = $request->file('assets_zip');

        $extractor = new SafeZipExtractor();

        try {
            $result = $extractor->extract($zip, $restaurant->id);
        } catch (\RuntimeException $e) {
            return back()->withErrors([
                'assets_zip' => __($e->getMessage())
            ]);
        }


        $request->validate([
            'assets_zip' => ['required','file','mimetypes:application/zip,application/x-zip-compressed','max:51200'], // 50MB
        ]);

        $zipPath = $request->file('assets_zip')->getRealPath();

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return back()->withErrors(['assets_zip' => __('admin.import.errors.zip_open_failed')]);
        }

        $allowedExt = ['jpg','jpeg','png','webp','svg'];
        $maxFiles = 300;

        if ($zip->numFiles > $maxFiles) {
            $zip->close();
            return back()->withErrors(['assets_zip' => __('admin.import.errors.zip_too_many_files')]);
        }

        $finalDir = storage_path("app/public/restaurants/{$restaurant->id}/import");
        if (!is_dir($finalDir)) @mkdir($finalDir, 0775, true);

        $tmpDir = storage_path("app/tmp/import_{$restaurant->id}_" . Str::random(8));
        @mkdir($tmpDir, 0775, true);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $name = $stat['name'] ?? '';

            if (!$name || str_ends_with($name, '/')) continue;

            // Zip Slip protection
            if (str_contains($name, '..') || str_starts_with($name, '/') || str_contains($name, '\\')) {
                $zip->close();
                $this->recursiveDelete($tmpDir);
                return back()->withErrors(['assets_zip' => __('admin.import.errors.zip_unsafe_path')]);
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) {
                $zip->close();
                $this->recursiveDelete($tmpDir);
                return back()->withErrors(['assets_zip' => __('admin.import.errors.zip_type_not_allowed') . ': ' . $name]);
            }

            $target = $tmpDir . DIRECTORY_SEPARATOR . $name;
            $targetDir = dirname($target);
            if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);

            // extract single file
            if (!copy("zip://{$zipPath}#{$name}", $target)) {
                $zip->close();
                $this->recursiveDelete($tmpDir);
                return back()->withErrors(['assets_zip' => __('admin.import.errors.zip_extract_failed') . ': ' . $name]);
            }
        }

        $zip->close();

        $this->recursiveCopy($tmpDir, $finalDir);
        $this->recursiveDelete($tmpDir);

        return back()->with('success', __('admin.import.success.zip_imported'));
    }

    private function recursiveCopy(string $src, string $dst): void
    {
        @mkdir($dst, 0775, true);
        $dir = opendir($src);
        if (!$dir) return;

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') continue;

            $srcPath = $src . DIRECTORY_SEPARATOR . $file;
            $dstPath = $dst . DIRECTORY_SEPARATOR . $file;

            if (is_dir($srcPath)) {
                $this->recursiveCopy($srcPath, $dstPath);
            } else {
                @mkdir(dirname($dstPath), 0775, true);
                copy($srcPath, $dstPath);
            }
        }

        closedir($dir);
    }

    private function recursiveDelete(string $dir): void
    {
        if (!is_dir($dir)) return;

        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') continue;

            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) $this->recursiveDelete($path);
            else @unlink($path);
        }

        @rmdir($dir);
    }

public function downloadMenuJson(Request $request, Restaurant $restaurant)
{
    // Права на экспорт (пока используем import.menu_json, потом можно завести export.menu_json)
    Permissions::abortUnless($request->user(), 'import.menu_json');

    $payload = [
        'mode' => 'snapshot',
        'exported_at' => now()->toIso8601String(),
        'restaurant' => [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'slug' => $restaurant->slug,
            'template_key' => $restaurant->template_key,
            'default_locale' => $restaurant->default_locale,
            'enabled_locales' => $restaurant->enabled_locales,
            'theme_tokens' => $restaurant->theme_tokens,
            'logo' => $restaurant->logo_path,
            'background' => $restaurant->background_path,
            'custom_css' => $restaurant->custom_css,
            'meta' => $restaurant->meta,
        ],
        'categories' => [],
    ];

    // Категории = sections with parent_id null
    $categories = Section::query()
        ->where('restaurant_id', $restaurant->id)
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->get();

    foreach ($categories as $cat) {
        $catTranslations = SectionTranslation::query()
            ->where('section_id', $cat->id)
            ->get()
            ->mapWithKeys(fn($t) => [$t->locale => [
                'title' => $t->title,
                'description' => $t->description,
            ]])
            ->toArray();

        // Подкатегории = sections where parent_id = category.id
        $subs = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('parent_id', $cat->id)
            ->orderBy('sort_order')
            ->get();

        $subArr = [];
        foreach ($subs as $sub) {
            $subTranslations = SectionTranslation::query()
                ->where('section_id', $sub->id)
                ->get()
                ->mapWithKeys(fn($t) => [$t->locale => [
                    'title' => $t->title,
                    'description' => $t->description,
                ]])
                ->toArray();

            // Items в подкатегории
            $items = Item::query()
                ->where('section_id', $sub->id)
                ->orderBy('sort_order')
                ->get();

            $itemsArr = [];
            foreach ($items as $it) {
                $itTranslations = ItemTranslation::query()
                    ->where('item_id', $it->id)
                    ->get()
                    ->mapWithKeys(fn($t) => [$t->locale => [
                        'title' => $t->title,
                        'description' => $t->description,
                        'details' => $t->details,
                    ]])
                    ->toArray();

                $itemsArr[] = [
                    'key' => $it->key,
                    'is_active' => (bool)$it->is_active,
                    'sort_order' => (int)$it->sort_order,
                    'price' => $it->price !== null ? (string)$it->price : null,
                    'currency' => $it->currency,
                    'image' => $it->image_path,
                    'meta' => $it->meta,
                    'translations' => $itTranslations,
                ];
            }

            $subArr[] = [
                'key' => $sub->key,
                'type' => $sub->type,
                'is_active' => (bool)$sub->is_active,
                'sort_order' => (int)$sub->sort_order,
                'style' => [
                    'title_font' => $sub->title_font,
                    'title_color' => $sub->title_color,
                ],
                'translations' => $subTranslations,
                'items' => $itemsArr,
            ];
        }

        // Items напрямую в категории (если ты используешь такой кейс)
        $catItems = Item::query()
            ->where('section_id', $cat->id)
            ->orderBy('sort_order')
            ->get();

        $catItemsArr = [];
        foreach ($catItems as $it) {
            $itTranslations = ItemTranslation::query()
                ->where('item_id', $it->id)
                ->get()
                ->mapWithKeys(fn($t) => [$t->locale => [
                    'title' => $t->title,
                    'description' => $t->description,
                    'details' => $t->details,
                ]])
                ->toArray();

            $catItemsArr[] = [
                'key' => $it->key,
                'is_active' => (bool)$it->is_active,
                'sort_order' => (int)$it->sort_order,
                'price' => $it->price !== null ? (string)$it->price : null,
                'currency' => $it->currency,
                'image' => $it->image_path,
                'meta' => $it->meta,
                'translations' => $itTranslations,
            ];
        }

        $payload['categories'][] = [
            'key' => $cat->key,
            'type' => $cat->type,
            'is_active' => (bool)$cat->is_active,
            'sort_order' => (int)$cat->sort_order,
            'style' => [
                'title_font' => $cat->title_font,
                'title_color' => $cat->title_color,
            ],
            'translations' => $catTranslations,
            'subcategories' => $subArr,
            'items' => $catItemsArr,
        ];
    }

    $filename = 'menu-' . $restaurant->slug . '-' . now()->format('Ymd-His') . '.json';

    return response()->streamDownload(function () use ($payload) {
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }, $filename, [
        'Content-Type' => 'application/json; charset=utf-8',
    ]);
}

}
