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
use InnoCMS\Common\Models\Contact;
use InnoCMS\Common\Repositories\ContactRepo;

class ContactController extends BaseController
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();
        $data    = [
            'searchFields'  => ContactRepo::getSearchFieldOptions(),
            'filterButtons' => ContactRepo::getFilterButtonOptions(),
            'contacts'      => ContactRepo::getInstance()->list($filters),
        ];

        return view('panel::contacts.index', $data);
    }

    /**
     * @param  Contact  $contact
     * @return mixed
     */
    public function show(Contact $contact): mixed
    {
        if (! $contact->status) {
            ContactRepo::getInstance()->update($contact, ['status' => true]);
            $contact->refresh();
        }

        return view('panel::contacts.show', compact('contact'));
    }

    /**
     * @param  Contact  $contact
     * @return RedirectResponse
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        try {
            ContactRepo::getInstance()->destroy($contact);

            return back()->with('success', trans('panel/common.deleted_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
