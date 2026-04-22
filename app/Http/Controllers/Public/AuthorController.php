<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\ViewModels\PublicMenu\MenuViewModel;
use App\Services\ImageService;

class AuthorController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        $locale = request('locale') ?? app()->getLocale();

        $vm = new MenuViewModel($restaurant, $locale);

        $images = app(ImageService::class);

        return view('public.templates.united.author', [
            'vm' => $vm,

            'profileImage' => $images->logo('system/author/oleksandr-stanov.webp'),

            'socials' => [
                [
                    'key' => 'telegram',
                    'url' => 'https://t.me/your_username',
                    'icon' => $images->socialIcon(null, 'telegram'),
                ],
                [
                    'key' => 'whatsapp',
                    'url' => 'https://wa.me/491735141827',
                    'icon' => $images->socialIcon(null, 'whatsapp'),
                ],
                [
                    'key' => 'linkedln',
                    'url' => 'https://linkedln.com/',
                    'icon' => $images->socialIcon(null, 'linkedln'),
                ],
                [
                    'key' => 'github',
                    'url' => 'https://github.com/AleksandrStanov1984',
                    'icon' => $images->socialIcon(null, 'github'),
                ],
            ],
        ]);
    }
}
