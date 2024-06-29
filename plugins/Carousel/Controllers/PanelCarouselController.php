<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     XING GUI YU <xingguiyu@foxmail.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\Carousel\Controllers;

use Illuminate\Http\Request;
use InnoCMS\Panel\Controllers\BaseController;
use Plugin\Carousel\Models\Carousel;
use Plugin\Carousel\Models\CarouselImage;
use Plugin\Carousel\Repositories\CarouselRepo;

class PanelCarouselController extends BaseController
{
    protected $positions = [
        [
            'label' => '顶部',
            'key'   => 'top',
            'value' => 'top',
        ],
        [
            'label' => '底部',
            'key'   => 'bottom',
            'value' => 'bottom',
        ],
    ];

    protected $styles = [
        [
            'label' => '响应式宽度',
            'key'   => 'container',
            'value' => 'container',
        ],
        [
            'label' => '100%宽度',
            'key'   => 'container-fluid',
            'value' => 'container-fluid',
        ],
    ];

    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $data = [
            'items' => Carousel::query()->paginate(),
        ];

        return view('Carousel::panel.index', $data);
    }

    /**
     * @return mixed
     */
    public function create(): mixed
    {
        $pages = $this->getPagesOptions();
        $data  = [
            'item'      => new Carousel,
            'pages'     => $pages,
            'positions' => $this->positions,
            'styles'    => $this->styles,
        ];

        return view('Carousel::panel.form', $data);
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function store(Request $request): mixed
    {
        $data = $request->all();
        $carousel=CarouselRepo::getInstance()->create($data);

        return redirect(panel_route('carousels.edit',$carousel))->with('success','创建成功，可以在下方配置轮播图图片');
    }

    /**
     * @param  Carousel  $carousel
     * @return mixed
     */
    public function edit(Carousel $carousel): mixed
    {
        $pages = $this->getPagesOptions();
        $data  = [
            'item'           => $carousel,
            'pages'          => $pages,
            'positions'      => $this->positions,
            'styles'         => $this->styles,
            'carouselImages' => $carousel->images()->orderBy('position','asc')->get(),
        ];

        return view('Carousel::panel.form', $data);
    }

    /**
     * @param  Request  $request
     * @param  Carousel  $carousel
     * @return mixed
     */
    public function update(Request $request, Carousel $carousel): mixed
    {
        $data = $request->all();
        CarouselRepo::getInstance()->update($carousel, $data);

        return redirect()->back()->with('success','更新成功');
    }

    /**
     * @param  Carousel  $carousel
     * @return mixed
     */
    public function destroy(Carousel $carousel): mixed
    {
        $carousel->delete();

        return redirect(panel_route('carousel.index'))->with('success','删除成功');
    }

    /**
     * @return array
     */
    protected function getPagesOptions()
    {
        $options = [
            [
                'label' => 'home',
                'key'   => 'home',
                'value' => 0,
            ],
        ];
        $pages = \Plugin\Carousel\Models\Page::all();
        foreach ($pages as $page) {
            $option = [
                'label' => $page->slug,
                'key'   => $page->slug,
                'value' => $page->id,
            ];
            array_push($options, $option);
        }

        return $options;
    }
}
