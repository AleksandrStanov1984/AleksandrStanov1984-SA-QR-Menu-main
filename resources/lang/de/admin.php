<?php

return [
    'brand' => 'SA QR Menu — Admin',

    'ui' => [
        'admin' => 'Admin',
        'language' => 'Sprache',
    ],

    'common' => [
        'save' => 'Speichern',
        'cancel' => 'Abbrechen',
        'change' => 'Ändern',
        'admin' => 'Admin',
    ],

    'fields' => [
        'id' => 'ID',
        'name' => 'Name',
        'slug' => 'Slug',
        'template' => 'Template',
        'languages' => 'Sprachen',
        'status' => 'Status',
        'actions' => 'Aktionen',
        'phone' => 'Telefon',
        'city' => 'Stadt',
        'street' => 'Straße',
        'house_number' => 'Hausnr.',
        'postal_code' => 'PLZ',
        'user_name' => 'Benutzername',
        'user_email' => 'Benutzer-E-Mail',
        'email' => 'E-Mail',
        'password' => 'Passwort',
    ],

    'templates' => [
        'classic' => 'Classic',
        'fastfood' => 'Fastfood',
        'bar' => 'Bar',
        'services' => 'Services',
    ],

    'status' => [
        'active' => 'AKTIV',
        'inactive' => 'INAKTIV',
    ],

    'actions' => [
        'add' => 'Hinzufügen',
        'edit' => 'Bearbeiten',
        'save' => 'Speichern',
        'select' => 'Auswählen',
        'open' => 'Öffnen',
        'activate' => 'Aktivieren',
        'deactivate' => 'Deaktivieren',
        'logout' => 'Abmelden',
        'cancel' => 'Abbrechen',
        'back' => 'Zurück',
        'create_restaurant' => 'Restaurant erstellen',
    ],

    'auth' => [
        // legacy keys
        'login_title' => 'Anmelden',
        'subtitle' => 'Melden Sie sich an, um Restaurants zu verwalten',
        'signin' => 'Anmelden',

        // ✅ alias keys used in some blades: admin.auth.login.*
        'login' => [
            'h2' => 'Anmelden',
            'subtitle' => 'Melden Sie sich an, um Restaurants zu verwalten',
            'submit' => 'Anmelden',
        ],
    ],

    'dashboard' => [
        'super_admin' => 'Super-Admin',
        'restaurant_admin' => 'Restaurant-Admin',
        'current_context' => 'Aktueller Kontext',
        'no_selected' => 'Kein Restaurant ausgewählt.',
        'open_editor' => 'Restaurant-Editor öffnen',
        'pick_restaurant' => 'Restaurant auswählen',
        'select_placeholder' => 'Auswählen…',
        'all_restaurants' => 'Alle Restaurants',
        'next_steps' => 'Nächste Schritte (MVP)',
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
            'subtitle' => 'Alle Restaurants verwalten (Super-Admin)',
            'add' => '+ Restaurant hinzufügen',
        ],
        'create' => [
            // ✅ alias for breadcrumbs: admin.restaurants.create.title
            'title' => 'Restaurant erstellen',

            'h2' => 'Restaurant erstellen',
            'subtitle' => 'Restaurant erstellen',
            'sections' => [
                'restaurant' => 'Restaurant',
                'user' => 'Restaurant-Benutzer',
            ],
        ],
        'edit' => [
            'h2' => 'Restaurant bearbeiten',
            'subtitle' => 'Restaurant-Einstellungen',
        ],
        'brand' => [
                            'h2' => 'Logo',
                            'logo_label' => 'Logo hochladen (PNG/JPG/WEBP, bis 2 MB)',
                            'logo_saved' => 'Logo gespeichert.',
                        ],
    ],

    'languages' => [
        'h2' => 'Sprachen',
        'add_h3' => 'Sprache hinzufügen',
        'default_h3' => 'Standardsprache',

        // ✅ alias used in some blades: admin.languages.default
        'default' => 'Standardsprache',

        'locale_label' => 'Sprachcode (z. B. en, ru)',
        'file_label' => 'Menü JSON-Datei',
        'set_default_checkbox' => 'Als Standardsprache setzen',
        'upload_import' => 'Hochladen & importieren',
        'default_select_label' => 'Standardsprache',
        'save_default' => 'Speichern',
        'note_de_default' => 'Hinweis: Wenn keine Standardsprache ausgewählt ist, wird DE verwendet.',
    ],

    'permissions' => [
        'h2' => 'Benutzerrechte',
        'user' => 'Benutzer: :name (:email)',
        'languages' => 'Sprachen',
        'sections' => 'Kategorien / Bereiche',
        'items' => 'Gerichte / Positionen',
        'banners' => 'Banner',
        'socials' => 'Socials',
        'theme' => 'Theme',
        'import' => 'Import',
        'save' => 'Rechte speichern',
    ],

    'uploads' => [
        'block_title' => 'Uploads-Speicher',
        'path_hint' => 'Für dieses Restaurant liegen die Dateien in:',
        'folders_hint' => 'Ordner werden automatisch erstellt:',
    ],

    'sections' => [
        'block_title' => 'Kategorien und Unterkategorien',
        'block_hint' => 'Kategorien (Sections) und Unterkategorien verwalten.',
        'open_manager' => 'Sections-Manager öffnen',
          'categories' => [
                'h2' => 'Kategorien',
                'hint' => 'Neue Hauptkategorie erstellen. Titel bis 50 Zeichen.',
                'title' => 'Titel',
                'font' => 'Schriftart',
                'color' => 'Farbe',
                'create_btn' => 'Kategorie erstellen',
                'created' => 'Kategorie erstellt.',
            ],
    ],

    'profile' => [
        'title' => 'Profil',
        'subtitle' => 'Profil',
        'h2' => 'Profil',
        'saved' => 'Profil gespeichert.',
        'change_email_btn' => 'E-Mail ändern',
        'change_password_btn' => 'Passwort ändern',

        'restaurant' => [
            'h2' => 'Betriebsdaten',
            'restaurant_name' => 'Betriebsname',
            'contact_name' => 'Kontaktname',
            'email' => 'Betriebs-E-Mail',
            'address_h3' => 'Adresse',
            'saved' => 'Betriebsdaten gespeichert.',
            'no_restaurant_context' => 'Im aktuellen Admin-Kontext wurde kein Restaurant ausgewählt.',
        ],

        'permissions' => [
            'h2' => 'Ihre Rechte',
            'super_admin' => 'Super-Admin: Vollzugriff',
            'no_permissions' => 'Keine Rechte zugewiesen.',
        ],

        'change_email' => [
            'h2' => 'E-Mail ändern',
            'current_email' => 'Aktuelle E-Mail',
            'current_password' => 'Aktuelles Passwort',
            'new_email' => 'Neue E-Mail',
        ],

        'change_password' => [
            'h2' => 'Passwort ändern',
            'current_email' => 'Aktuelle E-Mail',
            'current_password' => 'Aktuelles Passwort',
            'new_password' => 'Neues Passwort',
            'confirm_new_password' => 'Neues Passwort bestätigen',
        ],
    ],

    'items' => [
        'created' => 'Gericht erstellt.',
        'updated' => 'Gericht gespeichert.',
        'deleted' => 'Gericht gelöscht.',
    ],

    'menu_builder' => [
        'h2' => 'Menü-Builder',
        'hint' => 'Erstellen Sie Kategorien, Unterkategorien und Gerichte. Ziehen Sie die Gerichte per Drag & Drop, um die Reihenfolge zu ändern.',
        'add_category' => 'Kategorie hinzufügen',
        'add_subcategory' => 'Unterkategorie hinzufügen',
        'add_item' => 'Gericht hinzufügen',
        'flags' => 'Optionen',
        'spicy' => 'Schärfe (0–5)',
        'image' => 'Bild',
        'styles_hint' => 'Schrift/ Farbe/ Größe auswählen — die Felder werden sofort aktualisiert.',
        'empty' => 'Noch keine Kategorien vorhanden.',
    ],

    'sections' => [
        'deleted' => 'Bereich gelöscht.',
        'toggled' => 'Status des Bereichs wurde geändert.',
        'subcategories' => [
            'created' => 'Unterkategorie erstellt.',
        ],
    ],

    'sidebar' => [
        'about' => 'Über mich',
        'profile' => 'Mein Profil',
        'my_menu' => 'Mein Menü',
        'restaurants_select' => 'Restaurant auswählen',
        'logo' => 'LOGO'
    ],






];
