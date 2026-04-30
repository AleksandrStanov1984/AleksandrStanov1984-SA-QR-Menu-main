<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;

use App\ViewModels\PublicMenu\MenuViewModel;
use App\ViewModels\AuthorViewModel;

class AuthorController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        $locale = request('lang')
            ?? request('locale')
            ?? app()->getLocale();

        return view('public.templates.united.author', [
            'vm' => new MenuViewModel($restaurant, $locale),
            'authorVm' => new AuthorViewModel($restaurant, $locale)
        ]);
    }
}
