<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Components\Forms;

use Illuminate\View\Component;

class SwitchRadio extends Component
{
    public string $name;

    public bool $value;

    public string $title;

    public function __construct(string $name, ?bool $value, string $title)
    {
        $this->name  = $name;
        $this->title = $title;
        $this->value = (bool) $value;
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view('panel::components.form.switch-radio');
    }
}
