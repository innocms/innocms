<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Components;

use Illuminate\View\Component;
use InnoCMS\Common\Libraries\Breadcrumb as BreadcrumbLib;

class Breadcrumb extends Component
{
    public array $breadcrumbs;

    /**
     * @param  string  $type  article|catalog|tag|page|route|static
     * @param  mixed  $value  model instance, route name, or url
     * @param  string  $title  title for route/static types
     * @param  string|null  $parent  optional parent route to insert before current
     */
    public function __construct(
        string $type,
        $value = null,
        string $title = '',
        ?string $parent = null,
    ) {
        $this->breadcrumbs[] = $this->formatBreadcrumb([
            'title' => theme_trans('front.breadcrumb_home'),
            'url'   => front_route('home.index'),
        ]);

        $lib = BreadcrumbLib::getInstance();

        if ($parent) {
            [$parentRoute, $parentTitle] = array_pad(explode('|', $parent, 2), 2, '');
            $this->breadcrumbs[]         = $this->formatBreadcrumb(
                $lib->getTrail('route', $parentRoute, $parentTitle)
            );
        }

        if ($type !== 'route' || $value !== null) {
            $trail = $lib->getTrail($type, $value, $title);
            if (! empty($trail)) {
                $this->breadcrumbs[] = $this->formatBreadcrumb($trail);
            }
        }
    }

    /**
     * Truncate long titles for display, keep the full text for the tooltip.
     */
    private function formatBreadcrumb(array $breadcrumb): array
    {
        $maxLength = 30;
        $title     = $breadcrumb['title'] ?? '';

        if (mb_strlen($title) > $maxLength) {
            $breadcrumb['display_title'] = mb_substr($title, 0, $maxLength).'...';
            $breadcrumb['full_title']    = $title;
        } else {
            $breadcrumb['display_title'] = $title;
        }

        return $breadcrumb;
    }

    public function render(): mixed
    {
        return view('components.breadcrumb');
    }
}
