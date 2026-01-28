<?php

return [
    'brand' => 'SA QR Menu — Admin',

    'ui' => [
        'admin' => 'Admin',
        'language' => 'Language',
    ],

    'common' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'change' => 'Change',
        'admin' => 'Admin',
    ],

    'fields' => [
        'id' => 'ID',
        'name' => 'Name',
        'slug' => 'Slug',
        'template' => 'Template',
        'languages' => 'Languages',
        'status' => 'Status',
        'actions' => 'Actions',
        'phone' => 'Phone',
        'city' => 'City',
        'street' => 'Street',
        'house_number' => 'House',
        'postal_code' => 'ZIP',
        'user_name' => 'User name',
        'user_email' => 'User e-mail',
        'email' => 'E-mail',
        'password' => 'Password',
    ],

    'templates' => [
        'classic' => 'Classic',
        'fastfood' => 'Fastfood',
        'bar' => 'Bar',
        'services' => 'Services',
    ],

    'status' => [
        'active' => 'ACTIVE',
        'inactive' => 'INACTIVE',
    ],

    'actions' => [
        'add' => 'Add',
        'edit' => 'Edit',
        'save' => 'Save',
        'select' => 'Select',
        'open' => 'Open',
        'activate' => 'Activate',
        'deactivate' => 'Deactivate',
        'logout' => 'Logout',
        'cancel' => 'Cancel',
        'back' => 'Back',
        'create_restaurant' => 'Create restaurant',
    ],

    'auth' => [
        // legacy keys
        'login_title' => 'Login',
        'subtitle' => 'Sign in to manage restaurants',
        'signin' => 'Sign in',

        // ✅ alias keys used in some blades: admin.auth.login.*
        'login' => [
            'h2' => 'Login',
            'subtitle' => 'Sign in to manage restaurants',
            'submit' => 'Sign in',
        ],
    ],

    'dashboard' => [
        'super_admin' => 'Super admin',
        'restaurant_admin' => 'Restaurant admin',
        'current_context' => 'Current context',
        'no_selected' => 'No restaurant selected.',
        'open_editor' => 'Open restaurant editor',
        'pick_restaurant' => 'Pick a restaurant',
        'select_placeholder' => 'Select…',
        'all_restaurants' => 'All restaurants',
        'next_steps' => 'Next steps (MVP)',
        'dashboard' => 'Dashboard',
        'home' => 'Dashboard',
    ],

    'breadcrumbs' => [
        'dashboard' => 'Dashboard',
        'home' => 'Dashboard',
    ],

    'restaurants' => [
        'index' => [
            'h1' => 'Restaurants',
            'subtitle' => 'Manage all restaurants (super admin)',
            'add' => '+ Add restaurant',
        ],
        'create' => [
            // ✅ alias for breadcrumbs: admin.restaurants.create.title
            'title' => 'Create restaurant',

            'h2' => 'Create restaurant',
            'subtitle' => 'Create a new restaurant',
            'sections' => [
                'restaurant' => 'Restaurant',
                'user' => 'Restaurant user',
            ],
        ],
        'edit' => [
            'h2' => 'Edit restaurant',
            'subtitle' => 'Restaurant settings',
        ],
        'brand' => [
                            'h2' => 'Logo',
                            'logo_label' => 'Upload logo (PNG/JPG/WEBP, up to 2 MB)',
                            'logo_saved' => 'Logo saved.',
                        ],
    ],

    'languages' => [
        'h2' => 'Languages',
        'add_h3' => 'Add language',
        'default_h3' => 'Default language',

        // ✅ alias used in some blades: admin.languages.default
        'default' => 'Default language',

        'locale_label' => 'Language code (e.g. en, ru)',
        'file_label' => 'Menu JSON file',
        'set_default_checkbox' => 'Set as default language',
        'upload_import' => 'Upload & import',
        'default_select_label' => 'Default language',
        'save_default' => 'Save',
        'note_de_default' => 'Note: if no default language is selected, DE is used.',
    ],

    'permissions' => [
        'h2' => 'User permissions',
        'user' => 'User: :name (:email)',
        'languages' => 'Languages',
        'sections' => 'Categories / Sections',
        'items' => 'Items / Dishes',
        'banners' => 'Banners',
        'socials' => 'Socials',
        'theme' => 'Theme',
        'import' => 'Import',
        'save' => 'Save permissions',
    ],

    'uploads' => [
        'block_title' => 'Uploads storage',
        'path_hint' => 'For this restaurant the files are stored in:',
        'folders_hint' => 'Folders are created automatically:',
    ],

    'sections' => [
        'block_title' => 'Categories and subcategories',
        'block_hint' => 'Manage categories (sections) and subcategories.',
        'open_manager' => 'Open sections manager',
        'categories' => [
                'h2' => 'Categories',
                'hint' => 'Create a new top-level category. Title up to 50 characters.',
                'title' => 'Title',
                'font' => 'Font',
                'color' => 'Color',
                'create_btn' => 'Create category',
                'created' => 'Category created.',
            ],
    ],

    'profile' => [
        'title' => 'Profile',
        'subtitle' => 'Profile',
        'h2' => 'Profile',
        'saved' => 'Profile saved.',
        'change_email_btn' => 'Change e-mail',
        'change_password_btn' => 'Change password',

        'restaurant' => [
            'h2' => 'Restaurant details',
            'restaurant_name' => 'Restaurant name',
            'contact_name' => 'Contact name',
            'email' => 'Restaurant e-mail',
            'address_h3' => 'Address',
            'saved' => 'Restaurant details saved.',
            'no_restaurant_context' => 'No restaurant selected in the current admin context.',

        ],

        'permissions' => [
            'h2' => 'Your permissions',
            'super_admin' => 'Super admin: full access',
            'no_permissions' => 'No permissions assigned.',
        ],

        'change_email' => [
            'h2' => 'Change e-mail',
            'current_email' => 'Current e-mail',
            'current_password' => 'Current password',
            'new_email' => 'New e-mail',
        ],

        'change_password' => [
            'h2' => 'Change password',
            'current_email' => 'Current e-mail',
            'current_password' => 'Current password',
            'new_password' => 'New password',
            'confirm_new_password' => 'Confirm new password',
        ],
    ],

    'items' => [
        'created' => 'Item created.',
        'updated' => 'Item updated.',
        'deleted' => 'Item deleted.',
    ],

    'menu_builder' => [
      'h2' => 'Menu builder',
      'hint' => 'Create categories, subcategories and items. Drag to reorder items.',
      'add_category' => 'Add category',
      'add_subcategory' => 'Add subcategory',
      'add_item' => 'Add item',
      'flags' => 'Flags',
      'spicy' => 'Spicy (0-5)',
      'image' => 'Image',
      'styles_hint' => 'Choose font/color/size — fields update instantly.',
      'empty' => 'No categories yet.',
    ],

    'sections' => [
      'deleted' => 'Section deleted.',
      'toggled' => 'Section status changed.',
      'subcategories' => [
        'created' => 'Subcategory created.',
      ],
    ],

    'sidebar' => [
        'about' => 'About me',
        'profile' => 'My profile',
        'my_menu' => 'My menu',
        'restaurants_select' => 'Choose restaurant',
        'logo' => 'LOGO'
    ],






];
