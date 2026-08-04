<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Traits;

/**
 * Provides the primary (admin panel) locale code for validation purposes.
 *
 * Mirrors the sorting logic in the locale-input Blade component:
 * the primary locale is the admin's own locale, falling back to the
 * system default locale.
 */
trait PanelLocaleHelper
{
    /**
     * @return string
     */
    protected function primaryLocaleCode(): string
    {
        $adminLocale = current_admin()->locale ?? null;
        if ($adminLocale) {
            return $adminLocale;
        }

        $panelLocale = session('panel_locale', null);
        if ($panelLocale) {
            return $panelLocale;
        }

        return setting_locale_code();
    }
}
