<p align="center">
    <a href="https://www.innocms.com"><img src="https://www.innocms.com/images/logo.png" alt="InnoCMS"></a>
</p>

---

<p align="center">
    <a href="https://www.innocms.com"><img src="https://img.shields.io/badge/License-OSL%203.0-green.svg" alt="License"></a>
    <a href="https://www.php.net"><img src="https://img.shields.io/badge/Language-PHP%208.3-blue.svg" alt="PHP"></a>
    <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12-orange" alt="Laravel"></a>
</p>

<p align="center">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/us.svg">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/cn.svg">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/de.svg">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/fr.svg">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/jp.svg">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/kr.svg">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/ru.svg">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/es.svg">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/pt.svg">
    <img class="flag-img" width="32px" height="24px" src="https://flagicons.lipis.dev/flags/4x3/it.svg">
</p>

<p align="center">
    <a href="https://github.com/innocommerce/innoshop">
        <img src="https://img.shields.io/badge/🔥_Looking_for_an_e--commerce_system%3F-Check_out_InnoShop-8B5CF6?style=for-the-badge" alt="InnoShop">
    </a>
</p>

---

# InnoCMS
InnoCMS - Lightweight Enterprise CMS

InnoCMS is a lightweight enterprise CMS based on Laravel 12, featuring a modular architecture with hook-based plugin system, multi-language support, visitor tracking with GeoIP2, and theme development capabilities.

## Introduction
- Lightweight enterprise CMS designed for rapid development and deployment.
- Modular `innopacks` architecture with hook-based plugin extensibility.
- Multi-language support with built-in locale management.
- Visitor tracking with GeoIP2 geographic location integration.
- Theme system with live preview and one-click import.
- Vite-powered frontend build pipeline.

## Documentation
- https://docs.innoshop.cn

InnoCMS shares the same underlying architecture as [InnoShop](https://github.com/innocommerce/innoshop), so the development workflow and plugin system are largely identical.

## Demo
- Frontend: https://demo.innocms.com/
- Backend: https://demo.innocms.com/panel
- Account: admin@innocms.com
- Password: 123456

### Frontend Screenshot
<p align="center">
    <a href="https://www.innocms.com" target="_blank">
        <img src="https://www.innocms.com/images/readme/front.jpg?" alt="Front">
    </a>
</p>

### Backend Screenshot
<p align="center">
    <a href="https://www.innocms.com" target="_blank">
        <img src="https://www.innocms.com/images/readme/panel.jpg?" alt="Panel">
    </a>
</p>

## Requirements
- PHP >= 8.3
- MySQL >= 5.7 or 8.0
- Node.js >= 18
- Composer >= 2.0

## Installation

```bash
git clone https://github.com/innocms/innocms.git
cd innocms
composer install
npm install
cp .env.example .env
php artisan key:generate
# Edit .env with your database credentials
php artisan migrate
php artisan db:seed
npm run prod
```

Visit `your-domain.com/panel` to access the admin panel.

- Email: admin@innocms.com
- Password: 123456

## Plugin Development

All features should be implemented as plugins using the hook system. See `/plugins/PartnerLink` for a complete example.

```php
// plugins/YourPlugin/Boot.php
class Boot
{
    public function init(): void
    {
        listen_hook_filter('component.sidebar.plugin.routes', function ($data) {
            $data[] = ['route' => 'your_plugin.index', 'title' => 'Your Plugin'];
            return $data;
        });
    }
}
```

- If you find `InnoCMS` helpful, please do not hesitate to give us a star.
- Every like from you is the driving force for our continuous improvement.

## Contributors

Thanks to the [Contributors](https://github.com/innocms/innocms/graphs/contributors)

<a href="https://github.com/yushine"><img class="avatar-img" width="32px" height="32px" src="https://github.com/yushine.png"/></a>
<a href="https://github.com/liuweixxx"><img class="avatar-img" width="32px" height="32px" src="https://github.com/liuweixxx.png"/></a>
<a href="https://github.com/qxsclass"><img class="avatar-img" width="32px" height="32px" src="https://github.com/qxsclass.png"/></a>
<a href="https://github.com/what123"><img class="avatar-img" width="32px" height="32px" src="https://github.com/what123.png"/></a>
