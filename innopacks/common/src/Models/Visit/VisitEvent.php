<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models\Visit;

use InnoCMS\Common\Models\BaseModel;

class VisitEvent extends BaseModel
{
    protected $table = 'visit_events';

    protected $fillable = [
        'session_id',
        'event_type',
        'event_data',
        'customer_id',
        'ip_address',
        'page_url',
        'referrer',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    public const TYPE_PAGE_VIEW = 'page_view';

    public const TYPE_ARTICLE_VIEW = 'article_view';

    public const TYPE_HOME_VIEW = 'home_view';

    public const TYPE_CATALOG_VIEW = 'catalog_view';

    public const TYPE_SEARCH = 'search';

    // E-commerce compat (unused in CMS but referenced by services)
    public const TYPE_PRODUCT_VIEW = 'product_view';

    public const TYPE_ADD_TO_CART = 'add_to_cart';

    public const TYPE_CHECKOUT_START = 'checkout_start';

    public const TYPE_ORDER_PLACED = 'order_placed';

    public const TYPE_PAYMENT_COMPLETED = 'payment_completed';

    public const TYPE_REGISTER = 'register';

    public const TYPE_CART_VIEW = 'cart_view';

    public const TYPE_ORDER_CANCELLED = 'order_cancelled';

    public const TYPE_CATEGORY_VIEW = 'category_view';
}
