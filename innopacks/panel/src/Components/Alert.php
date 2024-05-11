<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public string $type;

    public string $msg;

    public function __construct(?string $type, string $msg)
    {
        $this->type = $type ?? 'success';
        $this->msg  = $msg;
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view('panel::components.alert');
    }
}
