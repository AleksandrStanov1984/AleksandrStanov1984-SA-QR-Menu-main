<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeMenuTemplate extends Command
{
    protected $signature = 'make:menu-template {name}';

    protected $description = 'Create QR menu template structure (production-ready)';

    public function handle()
    {
        $name = $this->argument('name');

        $base = resource_path("views/public/templates/{$name}");

        /*
        |------------------------------------------------------------------
        | FOLDERS
        |------------------------------------------------------------------
        */
        $folders = [

            "",
            "layout",

            "blocks/header",
            "blocks/footer",
            "blocks/categories",
            "blocks/drawer",
            "blocks/modal",
            "blocks/menu",
            "blocks/banners",

        ];

        foreach ($folders as $folder) {
            File::ensureDirectoryExists("{$base}/{$folder}");
        }

        /*
        |------------------------------------------------------------------
        | FILES
        |------------------------------------------------------------------
        */
        $files = [

            /*
            |--------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------
            */
            "index.blade.php" => <<<BLADE
<!DOCTYPE html>
<html lang="{{ \$vm->locale }}">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ \$vm->merchant->name }}</title>

@include('public.templates.{$name}.layout.styles')

</head>

<body>

@include('public.templates.{$name}.blocks.header.header')

@include('public.templates.{$name}.blocks.drawer.mobile-drawer')

@include('public.templates.{$name}.blocks.categories.category-nav')

@include('public.templates.{$name}.blocks.banners.index')

<main id="menuContainer">
@include('public.templates.{$name}.blocks.menu.menu-section')
</main>

@include('public.templates.{$name}.blocks.footer.footer')

@include('public.templates.{$name}.blocks.modal.item-modal')
@include('public.templates.{$name}.blocks.modal.hours-modal')

@include('public.templates.{$name}.layout.scripts')

</body>
</html>
BLADE
            ,

            /*
            |--------------------------------------------------------------
            | LAYOUT
            |--------------------------------------------------------------
            */
            "layout/styles.blade.php" => <<<BLADE
@vite('resources/css/app.css')
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
BLADE
            ,

            "layout/scripts.blade.php" => <<<BLADE
@vite('resources/js/app.js')
BLADE
            ,

            /*
            |--------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------
            */
            "blocks/header/header.blade.php" => <<<BLADE
<header class="site-header">

<div class="header-inner">

<div class="header-logo">
{{ \$vm->merchant->name }}
</div>

<button id="drawerOpen" class="drawer-btn">
<i class="ri-menu-line"></i>
</button>

</div>

</header>
BLADE
            ,

            "blocks/header/courusel-header.blade.php" => <<<BLADE
<div class="header-carousel">
{{-- carousel placeholder --}}
</div>
BLADE
            ,

            /*
            |--------------------------------------------------------------
            | FOOTER
            |--------------------------------------------------------------
            */
            "blocks/footer/footer.blade.php" => <<<BLADE
<footer class="site-footer">
<div class="container">
SA Digital Menus
</div>
</footer>
BLADE
            ,

            /*
            |--------------------------------------------------------------
            | CATEGORIES
            |--------------------------------------------------------------
            */
            "blocks/categories/category-nav.blade.php" => <<<BLADE
<nav id="categoryNav" class="category-nav">

@foreach(\$vm->categories as \$cat)

<a href="#section-{{ \$cat['id'] }}" class="category-link">
{{ \$cat['title'] }}
</a>

@endforeach

</nav>
BLADE
            ,

            /*
            |--------------------------------------------------------------
            | DRAWER
            |--------------------------------------------------------------
            */
            "blocks/drawer/mobile-drawer.blade.php" => <<<BLADE
<div id="mobileDrawer" class="mobile-drawer">

<div class="drawer-header">
    <div id="drawerClose" class="drawer-close">✕</div>
</div>

<nav class="drawer-nav">

@foreach(\$vm->categories as \$cat)
    <a href="#section-{{ \$cat['id'] }}" class="drawer-link">
        {{ \$cat['title'] }}
    </a>
@endforeach

</nav>

</div>
BLADE
            ,

            /*
            |--------------------------------------------------------------
            | BANNERS
            |--------------------------------------------------------------
            */
            "blocks/banners/index.blade.php" => <<<BLADE
<div class="banners">
{{-- banners placeholder --}}
</div>
BLADE
            ,

            /*
            |--------------------------------------------------------------
            | MODALS
            |--------------------------------------------------------------
            */
            "blocks/modal/item-modal.blade.php" => <<<BLADE
<div id="itemModal" class="modal">
<div class="modal-box">
<button data-close-modal>Close</button>
<div id="modalContent"></div>
</div>
</div>
BLADE
            ,

            "blocks/modal/hours-modal.blade.php" => <<<BLADE
<div id="hoursModal" class="modal">
<div class="modal-box">
<button data-close-modal>Close</button>
<div class="hours-content"></div>
</div>
</div>
BLADE
            ,

            /*
            |--------------------------------------------------------------
            | MENU
            |--------------------------------------------------------------
            */
            "blocks/menu/item-card.blade.php" => <<<BLADE
<div class="menu-item">

@if(\$item['image'])
<img src="{{ \$item['image'] }}" alt="{{ \$item['title'] }}">
@endif

<h3>{{ \$item['title'] }}</h3>

@if(\$item['price'])
<span>{{ number_format(\$item['price'], 2) }} €</span>
@endif

</div>
BLADE
            ,

            "blocks/menu/menu-section.blade.php" => <<<BLADE
@foreach(\$vm->categories as \$section)

<section id="section-{{ \$section['id'] }}">

<h2>{{ \$section['title'] }}</h2>

@foreach(\$section['items'] as \$item)

@include('public.templates.{$name}.blocks.menu.item-card')

@endforeach

</section>

@endforeach
BLADE
        ];

        /*
        |------------------------------------------------------------------
        | CREATE FILES
        |------------------------------------------------------------------
        */
        foreach ($files as $file => $content) {

            $path = "{$base}/{$file}";

            if (!File::exists($path)) {
                File::put($path, $content);
            }
        }

        $this->info("Template [{$name}] created successfully.");

        return Command::SUCCESS;
    }
}
