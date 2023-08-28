<?php
/**
 * Copyright (c) Since 2023 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class AdminMenu
{
    public static function getInnoMenus(): array
    {
        return [];
    }


    public static function getOriginMenus(): array
    {
        return [
            Menu::make('Get Started')
                ->icon('bs.book')
                ->title('Navigation')
                ->route(config('platform.index')),

            Menu::make('Example Screen')
                ->icon('bs.collection')
                ->route('platform.example')
                ->badge(fn () => 6),

            Menu::make('Form Elements')
                ->icon('bs.journal')
                ->route('platform.example.fields')
                ->active('*/form/examples/*'),

            Menu::make('Overview Layouts')
                ->icon('bs.columns-gap')
                ->route('platform.example.layouts')
                ->active('*/layout/examples/*'),

            Menu::make('Charts')
                ->icon('bs.bar-chart')
                ->route('platform.example.charts'),

            Menu::make('Cards')
                ->icon('bs.card-text')
                ->route('platform.example.cards')
                ->divider(),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.lock')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),

            Menu::make('Documentation')
                ->title('Docs')
                ->icon('bs.box-arrow-up-right')
                ->url('https://orchid.software/en/docs')
                ->target('_blank'),

            Menu::make('Changelog')
                ->icon('bs.box-arrow-up-right')
                ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
                ->target('_blank')
                ->badge(fn () => Dashboard::version(), Color::DARK),
        ];
    }
}
