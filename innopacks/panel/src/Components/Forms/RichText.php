<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Components\Forms;

use Illuminate\View\Component;

class RichText extends Component
{
    public ?string $name;

    public ?string $title;

    public ?string $value;

    public bool $required;

    public bool $multiple;

    public function __construct(string $name, string $title, $value = '', bool $required = false, bool $multiple = false)
    {
        $value = html_entity_decode($value, ENT_QUOTES);

        if ($multiple) {
            $value = json_decode($value, true);
        }

        $this->name     = $name;
        $this->title    = $title;
        $this->value    = $value;
        $this->required = $required;
        $this->multiple = $multiple;
    }

    public function render()
    {
        return view('panel::components.form.rich-text');
    }
}
