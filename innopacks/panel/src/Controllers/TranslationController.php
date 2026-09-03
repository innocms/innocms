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
use InnoCMS\Common\Support\Registry;
use InnoCMS\Panel\Requests\TranslateRequest;
use InnoCMS\Panel\Services\TranslatorService;
use Throwable;

class TranslationController extends Controller
{
    /**
     * Translate text.
     *
     * @param  TranslateRequest  $request
     * @return mixed
     */
    public function translateText(TranslateRequest $request): mixed
    {
        return $this->translate($request, 'text');
    }

    /**
     * Translate HTML text.
     *
     * @param  TranslateRequest  $request
     * @return mixed
     */
    public function translateHtml(TranslateRequest $request): mixed
    {
        return $this->translate($request, 'html');
    }

    /**
     * Handle translation request.
     *
     * @param  TranslateRequest  $request
     * @param  string  $type
     * @return mixed
     */
    private function translate(TranslateRequest $request, string $type): mixed
    {
        try {
            $source = $request->get('source');
            $target = $request->get('target');
            $text   = $request->get('text');

            Registry::set('translation_type', $type);

            $response = TranslatorService::translate($source, $target, $text);

            return create_json_success($response);
        } catch (Throwable $e) {
            return json_fail($e->getMessage());
        }
    }
}
