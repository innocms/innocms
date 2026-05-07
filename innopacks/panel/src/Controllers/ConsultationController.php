<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Consultation;
use InnoCMS\Common\Repositories\ConsultationRepo;

class ConsultationController extends BaseController
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();
        $data    = [
            'searchFields'  => ConsultationRepo::getSearchFieldOptions(),
            'filterButtons' => ConsultationRepo::getFilterButtonOptions(),
            'consultations' => ConsultationRepo::getInstance()->list($filters),
        ];

        return view('panel::consultations.index', $data);
    }

    /**
     * @param  Consultation  $consultation
     * @return mixed
     */
    public function show(Consultation $consultation): mixed
    {
        if (! $consultation->status) {
            ConsultationRepo::getInstance()->update($consultation, ['status' => true]);
            $consultation->refresh();
        }

        return view('panel::consultations.show', compact('consultation'));
    }

    /**
     * @param  Consultation  $consultation
     * @return RedirectResponse
     */
    public function destroy(Consultation $consultation): RedirectResponse
    {
        try {
            ConsultationRepo::getInstance()->destroy($consultation);

            return back()->with('success', trans('panel::common.deleted_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
