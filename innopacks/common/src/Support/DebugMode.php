<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Support;

use Throwable;

class DebugMode
{
    /**
     * Apply the panel debug switch to app.debug.
     *
     * Must run before any provider boots: laravel-debugbar calls isEnabled() in its
     * own boot() and caches the result, so overriding app.debug at a later stage
     * cannot turn it off. Explicit values ('0'/'1') always win; when the setting
     * was never saved, the .env value is kept untouched.
     *
     * @param  mixed  $app
     * @return void
     */
    public function apply($app): void
    {
        if (! installed()) {
            return;
        }

        try {
            // Raw query builder on purpose: Eloquent models are not usable at
            // booting time because Model::setConnectionResolver() only runs in
            // DatabaseServiceProvider::boot(). Table prefix is applied by the builder.
            $value = $app['db']->connection()->table('settings')
                ->where('space', 'system')->where('name', 'debug')->value('value');
        } catch (Throwable $e) {
            logger()->warning('Failed to read debug setting, keep app.debug from env.', ['error' => $e->getMessage()]);

            return;
        }

        if ($value !== null) {
            $app['config']->set('app.debug', filter_var($value, FILTER_VALIDATE_BOOL));
        }
    }
}
