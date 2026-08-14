# InnoCMS 主题开发指南

InnoCMS 主题 = 纯前端视图层，以独立 git 仓库放在 innocms 根目录 `themes/{code}/`。
**不 fork innocms core** —— 升级核心时主题直接跟着跑，无需合并上游。

主题仓库统一托管在 Gitea `cmsthemes` 组织：`git@innoshop.work:cmsthemes/{code}.git`。

参考实现：`themes/gallery`（主题模板库，Product=主题 / Category=行业，见 [cmsthemes/gallery](https://innoshop.work/cmsthemes/gallery)）。

---

## 1. 目录结构

```
themes/{code}/
├── config.json          # 主题清单（必须）
├── views/               # Blade 视图，同名覆盖 front 默认视图（必须）
│   ├── layouts/         #   app.blade.php / header / footer
│   ├── home.blade.php   #   首页
│   ├── products/        #   index / show（Product 内容）
│   ├── categories/      #   index / show（Category 内容）
│   ├── articles/        #   index / show（Article 内容）
│   ├── pages/           #   静态页
│   └── partials/        #   复用片段
├── public/              # 静态资源，用 theme_asset() 访问
│   └── css|js|images
├── lang/{locale}/       # 主题翻译，用 theme_trans() 访问
├── assets/              # 构建源（可选）
├── demo/Seeder.php      # 演示数据（可选）
├── setup/boot.php       # 启动钩子（可选，require 返回 callable）
└── routes/              # 自定义路由（可选）
    ├── root.php         #   无 locale 前缀
    └── front.php        #   自动按 locale 前缀注册
```

**config.json 最小示例：**

```json
{
  "code": "mytheme",
  "name": {"zh-cn": "我的主题", "en": "My Theme"},
  "description": {"zh-cn": "描述", "en": "Description"},
  "version": "v1.0.0",
  "icon": "/images/favicon.png",
  "author": {"name": "InnoShop", "email": "team@innoshop.com"}
}
```

> `code` 必须与目录名一致。

---

## 2. 主题如何被加载

- 激活主题：`system_setting('theme')` 返回主题 code（如 `gallery`）。
- `FrontServiceProvider` 把视图路径按 **主题 views > front 包默认 views** 顺序 prepend，
  主题里同名 Blade 文件即覆盖默认。
- 主题翻译以 `theme-{code}` 命名空间加载；主题路由、boot 钩子按需加载。

**切换主题：**

```php
\InnoCMS\Common\Repositories\SettingRepo::getInstance()->updateSystemValue('theme', 'gallery');
```

---

## 3. 可覆盖的视图与可用数据

| 路由 | 控制器 | 视图 | 传入数据 |
|---|---|---|---|
| `/` | `HomeController@index` | `home` | serviceCatalogs, featuredProducts, industryArticles, latestNews, productsCatalog, newsCatalog, softwareProducts |
| `/products` | `ProductController@index` | `products.index` | products（全部激活）, category=null |
| `/product-{slug}` | `ProductController@slugShow` | `products.show` | product, related |
| `/categories` | `CategoryController@index` | `categories.index` | categories |
| `/category-{slug}` | `CategoryController@slugShow` | `categories.show` | category, products |
| `/articles` / `/articles/{id}` | `ArticleController@…` | `articles.index` / `show` | articles / article |
| `/pages/{slug}` | `PageController@…` | `pages.show` | page |
| `/contacts` | `ContactController@…` | `contacts.index` | — |

视图内可直接用 Eloquent 查数据（画廊首页示例）：

```blade
@php
  $themes = \InnoCMS\Common\Models\Product::with(['translations', 'categories.translations'])
    ->where('active', true)->orderBy('position')->orderBy('id')->get();
@endphp
```

---

## 4. 内容数据模型（Product / Category / Article）

- **Product**（产品 / 项目 / 主题）：`slug, images[], video, price, link, spu_code, position, viewed, active`
  - `$product->image` = 首图 URL；`$product->url` = 详情页路由；`$product->link` = 外链（demo）
- **Category**（分类 / 行业）：`parent_id, slug, image, position, active`，与 Product 多对多（`product_categories`）
- **Article**（文章）：挂 `catalog_id`（Catalog = 文章分类）
- 多语言：`$model->translations` 集合、`$model->translation` 当前语言

---

## 5. 主题辅助函数

| 函数 | 作用 |
|---|---|
| `theme_asset('css/app.css')` | 主题 `public/` 资源 URL |
| `theme_trans('front.xxx')` | 主题翻译（`lang/{locale}/front.php`，命名空间 `theme-{code}`），注意 `$replace` 是第 3 参 |
| `image_resize($path, w, h)` | 本地图缩放缓存（**远程 http URL 原样返回，不缩放**） |
| `image_origin($path)` | 原图 URL |
| `front_route('products.slug_show', ['slug' => …])` | 前端路由，自动带 locale 前缀 |
| `system_setting('theme')` | 当前主题 code |

---

## 6. 校验与发布

```bash
# 校验主题结构
php artisan theme:validate themes/mytheme

# 发布主题为默认视图（vendor:publish 视图模板）
php artisan inno:publish-theme

# 手动校验
php artisan theme:validate themes/gallery
```

> 开发时无需发布；直接建目录 + `updateSystemValue('theme', code)` 即可生效。

---

## 7. 新建主题清单

1. Gitea `cmsthemes` 组织建仓库 `{code}`
2. innocms 根目录 `themes/{code}/` 内 `git init` + 关联 remote
3. 写 `config.json` + `views/`（参考 gallery 或 aurora 主题）
4. 本地 `updateSystemValue('theme', code)` 激活验证
5. `php artisan theme:validate` 校验后提交推送
