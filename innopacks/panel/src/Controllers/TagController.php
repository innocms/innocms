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
use InnoCMS\Common\Models\Tag;
use InnoCMS\Common\Repositories\TagRepo;
use InnoCMS\Panel\Requests\TagRequest;

class TagController extends BaseController
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();
        $data    = [
            'tags' => TagRepo::getInstance()->list($filters),
        ];

        return view('panel::tags.index', $data);
    }

    /**
     * Tag creation tag.
     *
     * @return mixed
     */
    public function create(): mixed
    {
        $data = [
            'tag' => new Tag(),
        ];

        return view('panel::tags.form', $data);
    }

    /**
     * @param  TagRequest  $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(TagRequest $request): RedirectResponse
    {
        try {
            $data = $request->all();
            TagRepo::getInstance()->create($data);

            return back()->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Tag  $tag
     * @return mixed
     */
    public function edit(Tag $tag): mixed
    {
        $data = [
            'tag' => $tag,
        ];

        return view('panel::tags.form', $data);
    }

    /**
     * @param  TagRequest  $request
     * @param  Tag  $tag
     * @return RedirectResponse
     */
    public function update(TagRequest $request, Tag $tag): RedirectResponse
    {
        try {
            $data = $request->all();
            TagRepo::getInstance()->update($tag, $data);

            return back()->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Tag  $tag
     * @return RedirectResponse
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        try {
            TagRepo::getInstance()->destroy($tag);

            return back()->with('success', trans('panel::common.deleted_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
