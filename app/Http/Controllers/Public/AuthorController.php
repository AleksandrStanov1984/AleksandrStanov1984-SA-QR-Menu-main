<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class AuthorController extends Controller
{
    public function index()
    {
        return view('public.author.author', [
            'icons' => [
                [
                    'url' => config('author.github_url'),
                    'icon' => asset('assets/icons/github.svg'),
                ],
                [
                    'url' => config('author.linkedin_url'),
                    'icon' => asset('assets/icons/linkedin.svg'),
                ],
            ],
            'links' => [],
            'profileImage' => asset('assets/author/profile/oleksandr-stanov.webp'),
        ]);
    }
}
