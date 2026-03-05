<?php

namespace App\Support\PublicMenu;

use App\Models\Restaurant;

class TemplateResolver
{
    public function resolve(Restaurant $restaurant): string
    {
        $slug = optional($restaurant->template)->slug;

        if (!$slug) {
            return 'public.templates.united.index'; // fallback
        }

        $view = "public.templates.{$slug}.index";

        if (!view()->exists($view)) {
            return 'public.templates.united.index';
        }

        return $view;
    }
}
