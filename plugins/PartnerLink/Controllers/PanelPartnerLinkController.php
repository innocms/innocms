<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\PartnerLink\Controllers;

use Illuminate\Http\Request;
use InnoCMS\Panel\Controllers\BaseController;
use Plugin\PartnerLink\Models\PartnerLink;
use Plugin\PartnerLink\Repositories\PartnerLinkRepo;

class PanelPartnerLinkController extends BaseController
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $data = [
            'items' => PartnerLink::query()->paginate(),
        ];

        return view('PartnerLink::panel.index', $data);
    }

    /**
     * @return mixed
     */
    public function create(): mixed
    {
        $data = [
            'item' => new PartnerLink,
        ];

        return view('PartnerLink::panel.form', $data);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request): mixed
    {
        $data = $request->all();
        PartnerLinkRepo::getInstance()->create($data);

        return redirect(panel_route('partner_links.index'));
    }

    /**
     * @param PartnerLink $partnerLink
     * @return mixed
     */
    public function edit(PartnerLink $partnerLink): mixed
    {
        $data = [
            'item' => $partnerLink,
        ];

        return view('PartnerLink::panel.form', $data);
    }

    /**
     * @param Request $request
     * @param PartnerLink $partnerLink
     * @return mixed
     */
    public function update(Request $request, PartnerLink $partnerLink): mixed
    {
        $data = $request->all();
        PartnerLinkRepo::getInstance()->update($partnerLink, $data);

        return redirect(panel_route('partner_links.index'));
    }

    /**
     * @param PartnerLink $partnerLink
     * @return mixed
     */
    public function destroy(PartnerLink $partnerLink): mixed
    {
        $partnerLink->delete();

        return redirect(panel_route('partner_links.index'));
    }
}
