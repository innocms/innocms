# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

InnoCMS is a lightweight enterprise CMS built on Laravel 11 (PHP 8.2+). It uses a modular "innopacks" architecture with a hook-based plugin system for extensibility.

## Commands

```bash
# Install dependencies
composer install
npm install

# Database setup
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link

# Frontend assets (Laravel Mix)
npm run dev      # Development build with watch
npm run prod     # Production build

# Testing
php artisan test

# Code formatting (Laravel Pint)
composer pint
```

## Architecture

### Core Packages (innopacks/)

The system is divided into 5 packages that will become independent Composer packages after v1.0:

- **common/** - Shared models (Article, Catalog, Page, Tag, Locale, Setting, Admin), repositories, services, middleware, and helper functions
- **panel/** - Admin panel backend (controllers, views, routes at `/panel`)
- **front/** - Public website frontend (controllers, views, routes)
- **install/** - Installation wizard
- **plugin/** - Plugin system core (PluginManager, Blade directives, hook system)

**CRITICAL:** Do not directly edit files in `/innopacks`. All customization should be done via plugins.

### Plugin System

Plugins extend functionality using hooks. Located in `/plugins/`.

**Plugin Structure:**
```
plugins/PluginName/
├── config.json       # Required: code, name, description, version, author
├── Boot.php          # Entry point with init() method
├── Controllers/
├── Models/
├── Routes/
│   ├── panel.php     # Admin routes
│   └── front.php     # Frontend routes
├── Views/
├── Lang/
├── Migrations/
└── Static/
```

**Hook Functions (from innopacks/plugin/helpers.php):**
```php
// Action hooks (no return value)
fire_hook_action($hookName, $request);
listen_hook_action($hookName, $callback);

// Filter hooks (transform data)
fire_hook_filter($hookName, $data);
listen_hook_filter($hookName, $callback);

// Blade hooks (inject/modify views)
listen_blade_insert($hookName, $callback);
listen_blade_update($hookName, $callback);
```

**Blade Directives:**
```blade
@hookinsert('hook_name')           <!-- Insert content at hook point -->
@hookupdate('hook_name')...@endhookupdate  <!-- Wrap content with hook -->
```

**Common Hook Points:**
- `component.sidebar.plugin.routes` - Add items to admin sidebar
- `layouts.header.bottom` - Header extensions
- `layouts.footer.top` - Footer extensions

### Key Helper Functions

```php
// Settings
setting($key)              // Get setting value
system_setting($key)       // Get system setting

// Panel (innopacks/panel/helpers.php)
panel_route($name)         // Generate panel route URL
current_admin()            // Get current admin user
is_admin()                 // Check if in admin panel

// Plugin
plugin($code)              // Get plugin instance
plugin_setting($code, $key) // Get plugin setting

// Other
installed()                // Check if system is installed
locales()                  // Get available locales
front_route($name)         // Generate frontend route URL
image_resize($image, $w, $h) // Resize image
```

### Namespaces

- `InnoCMS\Common\` - Common package (innopacks/common/src/)
- `InnoCMS\Panel\` - Panel package (innopacks/panel/src/)
- `InnoCMS\Front\` - Front package (innopacks/front/src/)
- `InnoShop\Plugin\` - Plugin core (innopacks/plugin/src/)
- `Plugin\` - Plugins directory (plugins/)

### View Namespacing

- `common::view-name` - Common views
- `panel::view-name` - Panel views
- `front::view-name` - Front views
- `PluginCode::view-name` - Plugin views

## Secondary Development

### Frontend Customization
```bash
php artisan inno:publish-theme
# Creates templates in /resources/views/vendor
```

### Backend Customization
```bash
php artisan vendor:publish --provider="InnoCMS\Panel\PanelServiceProvider" --tag=views
# Creates templates in /resources/views/vendor
```

### Plugin Development

All new features should be implemented as plugins. See `/plugins/PartnerLink` for a complete example.

Example Boot.php:
```php
<?php
namespace Plugin\YourPlugin;

class Boot
{
    public function init(): void
    {
        // Add sidebar menu
        listen_hook_filter('component.sidebar.plugin.routes', function ($data) {
            $data[] = ['route' => 'your_plugin.index', 'title' => 'Your Plugin'];
            return $data;
        });

        // Insert content at blade hook
        listen_blade_insert('layouts.footer.top', function () {
            return view('YourPluginName::component');
        });
    }
}
```

## Database Models

Models with translations use separate tables (e.g., `article_translations`). The translation relationship is handled in the model and repositories.

Key models in `innopacks/common/src/Models/`:
- `Article` - Blog articles with translations
- `Catalog` - Categories with translations
- `Page` - Static pages with translations
- `Tag` - Tags with translations
- `Locale` - Available languages
- `Setting` - System settings
- `Admin` - Administrator users

## Middleware Stack

- `EventActionHook` - Fires action hooks for controller methods
- `ContentFilterHook` - Applies filter hooks to response content
- `AdminAuthenticate` - Admin authentication (panel only)
- `GlobalDataMiddleware` - Shares global frontend data
