<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeMenuTemplate extends Command
{
    protected $signature = 'make:menu-template {name}';

    protected $description = 'Create QR menu template structure';

    public function handle()
    {
        $name = $this->argument('name');

        $base = resource_path("views/public/templates/$name");

        /*
        |--------------------------------------------------------------------------
        | Folders
        |--------------------------------------------------------------------------
        */

        $folders = [

            "",
            "layout",

            "blocks/header",
            "blocks/footer",
            "blocks/categories",
            "blocks/drawer",
            "blocks/modal",
            "blocks/menu"

        ];

        foreach ($folders as $folder) {

            File::ensureDirectoryExists("$base/$folder");

        }

        /*
        |--------------------------------------------------------------------------
        | Files with default content
        |--------------------------------------------------------------------------
        */

        $files = [

            "index.blade.php" => <<<BLADE
<!DOCTYPE html>
<html lang="{{ \$vm->locale }}">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ \$vm->restaurant['name'] }}</title>

@include('public.templates.$name.layout.styles')

</head>

<body class="theme-light">

@include('public.templates.$name.blocks.header.header')

@include('public.templates.$name.blocks.categories.category-nav')

<main id="menuContainer">

@include('public.templates.$name.blocks.menu.menu-section')

</main>

@include('public.templates.$name.blocks.footer.footer')

@include('public.templates.$name.layout.scripts')

</body>
</html>
BLADE
            ,

            "layout/styles.blade.php" => <<<BLADE
@vite('resources/css/app.css')
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
BLADE
            ,

            "layout/scripts.blade.php" => <<<BLADE
@vite('resources/js/app.js')
BLADE
            ,

            "blocks/header/header.blade.php" => <<<BLADE
<header class="site-header">

<div class="header-inner">

<div class="header-logo">
{{ \$vm->restaurant['name'] }}
</div>

<button id="drawerOpen" class="drawer-btn">
<i class="ri-menu-line"></i>
</button>

</div>

</header>
BLADE
            ,

            "blocks/footer/footer.blade.php" => <<<BLADE
<footer class="site-footer">

<div class="container">

SA Digital Menus

</div>

</footer>
BLADE
            ,

            "blocks/categories/category-nav.blade.php" => <<<BLADE
<nav id="categoryNav" class="category-nav">

@foreach(\$vm->sections as \$section)

<a href="#section-{{ \$section['id'] }}" class="category-link">
{{ \$section['title'] }}
</a>

@endforeach

</nav>
BLADE
            ,

            "blocks/drawer/mobile-drawer.blade.php" => <<<BLADE
<div id="mobileDrawer" class="mobile-drawer">

<div class="drawer-close" id="drawerClose">
✕
</div>

</div>
BLADE
            ,

            "blocks/modal/item-modal.blade.php" => <<<BLADE
<div id="itemModal" class="modal">

<div class="modal-box">

<button data-close-modal>Close</button>

<div id="modalContent">

</div>

</div>

</div>
BLADE
            ,

            "blocks/menu/item-card.blade.php" => <<<BLADE
<div class="menu-item">

@if(\$item['image'])
<img
class="menu-item-image"
src="{{ \$item['image'] }}"
alt="{{ \$item['title'] }}"
>
@endif

<div class="menu-item-content">

<h3 class="menu-item-title">
{{ \$item['title'] }}
</h3>

@if(\$item['description'])
<p class="menu-item-description">
{{ \$item['description'] }}
</p>
@endif

@if(\$item['price'])
<span class="menu-item-price">
{{ number_format(\$item['price'],2) }} €
</span>
@endif

</div>

</div>
BLADE
            ,

            "blocks/menu/menu-section.blade.php" => <<<BLADE
@foreach(\$vm->sections as \$section)

<section
id="section-{{ \$section['id'] }}"
class="menu-section"
>

<h2 class="menu-section-title">
{{ \$section['title'] }}
</h2>

<div class="menu-grid">

@foreach(\$section['items'] as \$item)

@include('public.templates.$name.blocks.menu.item-card')

@endforeach

</div>

</section>

@endforeach
BLADE

        ];

        /*
        |--------------------------------------------------------------------------
        | Create files
        |--------------------------------------------------------------------------
        */

        foreach ($files as $file => $content) {

            $path = "$base/$file";

            if (!File::exists($path)) {

                File::put($path, $content);

            }

        }

        $this->info("Template [$name] created successfully.");
    }
}
