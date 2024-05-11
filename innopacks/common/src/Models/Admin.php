<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Notifications\Notifiable;

class Admin extends AuthUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'locale', 'password', 'active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
