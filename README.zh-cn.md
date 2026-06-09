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
        <img src="https://img.shields.io/badge/🔥_需要开源电商系统%3F-了解一下_InnoShop-8B5CF6?style=for-the-badge" alt="InnoShop">
    </a>
</p>

---

# InnoCMS
InnoCMS - 轻量级企业官网 CMS

InnoCMS 是一个基于 Laravel 12 的轻量级企业官网 CMS，采用模块化架构，支持 Hook 插件扩展、多语言管理、GeoIP2 访客追踪和主题模板开发。

## 介绍
- 轻量级企业官网 CMS，专为快速开发和上线设计。
- 模块化 `innopacks` 架构，基于 Hook 的插件扩展机制。
- 多语言支持，内置语言管理。
- 访客追踪，集成 GeoIP2 地理位置。
- 主题系统，支持在线预览和一键导入。
- Vite 驱动的前端构建。

## 开发文档
- https://docs.innoshop.cn

InnoCMS 底层架构与 [InnoShop](https://github.com/innocommerce/innoshop) 一致，开发方式和插件机制基本通用。

## Demo 演示站
- 前台: https://demo.innocms.com/
- 后台: https://demo.innocms.com/panel
- 账号: admin@innocms.com
- 密码: 123456

### Demo 前台截图
<p align="center">
    <a href="https://www.innocms.com" target="_blank">
        <img src="https://www.innocms.com/images/readme/front.jpg?" alt="Front">
    </a>
</p>

### Demo 后台截图
<p align="center">
    <a href="https://www.innocms.com" target="_blank">
        <img src="https://www.innocms.com/images/readme/panel.jpg?" alt="Panel">
    </a>
</p>

## 环境要求
- PHP >= 8.3
- MySQL >= 5.7 或 8.0
- Node.js >= 18
- Composer >= 2.0

## 安装说明

```bash
git clone https://github.com/innocms/innocms.git
cd innocms
composer install
npm install
cp .env.example .env
php artisan key:generate
# 编辑 .env 填写数据库配置
php artisan migrate
php artisan db:seed
npm run prod
```

访问 `your-domain.com/panel` 进入后台管理。

- 邮箱: admin@innocms.com
- 密码: 123456

## 插件开发

所有功能应以插件形式实现，通过 Hook 机制扩展。请参考 `/plugins/PartnerLink` 插件示例。

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

- 如果您发现 `InnoCMS` 对您有所帮助，请不吝赐给我们一个星星(star)。
- 您的每一次点赞都是我们不断进步的动力。

## 贡献者

感谢各位开发者的支持与贡献! [Contributors](https://github.com/innocms/innocms/graphs/contributors)

<a href="https://github.com/yushine"><img class="avatar-img" width="32px" height="32px" src="https://github.com/yushine.png"/></a>
<a href="https://github.com/liuweixxx"><img class="avatar-img" width="32px" height="32px" src="https://github.com/liuweixxx.png"/></a>
<a href="https://github.com/qxsclass"><img class="avatar-img" width="32px" height="32px" src="https://github.com/qxsclass.png"/></a>
<a href="https://github.com/what123"><img class="avatar-img" width="32px" height="32px" src="https://github.com/what123.png"/></a>
