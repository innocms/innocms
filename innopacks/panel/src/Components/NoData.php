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

class NoData extends Component
{
    public string $text;

    public function __construct(?string $text = '')
    {
        $this->text = $text;
    }

    public function render()
    {
        return view('panel::components.no-data');
    }
}
