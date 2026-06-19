<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected string $modelClass;

    public function __construct()
    {
        if (empty($this->modelClass)) {
            $this->modelClass = $this->getModelByController();
        }
    }

    /**
     * @return string
     */
    private function getModelByController(): string
    {
        $class     = class_basename($this);
        $modelName = str_replace('Controller', '', $class);

        return "InnoCMS\\Common\\Models\\$modelName";
    }

    /**
     * @return void
     * @throws Exception
     */
    private function checkModel(): void
    {
        if (empty($this->modelClass)) {
            throw new Exception('Please define model class first!');
        }
        if (! class_exists($this->modelClass)) {
            throw new Exception("Class $this->modelClass doesn't exit!");
        }
    }

    /**
     * @throws Exception
     */
    public function active(Request $request, int $id): mixed
    {
        try {
            $this->checkModel();
            $item = $this->modelClass::query()->findOrFail($id);

            $item->active = $request->get('status');
            $item->saveOrFail();

            return json_success(trans('common/base.updated_success'));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
