<?php

namespace App\Http\Controllers;

class PlatformLegalController extends Controller
{
    public function impressum()
    {
        app()->setLocale('de');

        $legal = $this->buildPlatformLegal('impressum');

        return view('platform.legal.impressum', compact('legal'));
    }

    public function datenschutz()
    {
        app()->setLocale('de');

        $legal = $this->buildPlatformLegal('datenschutz');

        return view('platform.legal.datenschutz', compact('legal'));
    }

    private function buildPlatformLegal(string $type): array
    {
        $data = __("platform_legal.{$type}");
        $owner = __("legal_owner");

        if (!is_array($data) || !is_array($owner)) {
            return [];
        }

        $search = [
            ':platform_name',
            ':platform_address_line_1',
            ':platform_address_line_2',
            ':platform_country',
            ':platform_email',
            ':platform_phone',
        ];

        $replace = [
            e($owner['name']),
            e($owner['address_line_1']),
            e($owner['address_line_2']),
            e($owner['country']),
            e($owner['email']),
            e($owner['phone']),
        ];

        array_walk_recursive($data, function (&$value) use ($search, $replace) {

            if (!is_string($value)) {
                return;
            }

            $value = str_replace($search, $replace, $value);

            $value = str_replace(
                'mailto::platform_email',
                'mailto:' . $replace[4],
                $value
            );

            $value = str_replace(
                'tel::platform_phone',
                'tel:' . preg_replace('/\s+/', '', $replace[5]),
                $value
            );
        });

        return $data;
    }
}
