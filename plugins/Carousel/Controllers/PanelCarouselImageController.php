<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <xingguiyu@foxmail.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\Carousel\Controllers;

use Illuminate\Http\Request;
use InnoCMS\Panel\Controllers\BaseController;
use Plugin\Carousel\Models\Carousel;
use Plugin\Carousel\Models\CarouselImage;
use Plugin\Carousel\Repositories\CarouselImageRepo;

class PanelCarouselImageController extends BaseController
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $data = [
            'items' => CarouselImage::query()->paginate(),
        ];

        return view('CarouselImage::panel.index', $data);
    }

    /**
     * @return mixed
     */
    public function create(): mixed
    {
        $data = [
            'item' => new CarouselImage,
        ];

        return view('CarouselImage::panel.form', $data);
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function store(Request $request): mixed
    {
        $data = $request->all();
        CarouselImageRepo::getInstance()->create($data);

        return redirect(panel_route('carousel_image.index'));
    }

    /**
     * @param  CarouselImage  $carouselImage
     * @return mixed
     */
    public function edit(CarouselImage $carouselImage): mixed
    {
        $data = [
            'item' => $carouselImage,
        ];

        return view('Carousel::panel.image_form', $data);
    }

    /**
     * @param  Request  $request
     * @param  Carousel  $carousel
     * @param  $carouselImageId
     * @return mixed
     */
    public function update(Request $request, Carousel $carousel, $carouselImageId): mixed
    {
        $carouselImage = CarouselImage::find($carouselImageId);
        $data          = $request->all();
        CarouselImageRepo::getInstance()->update($carouselImage, $data);

        //        return redirect(panel_route('carousel_image.index'));
        return redirect()->back()->with('success', '更新成功');
    }

    /**
     * @param  Carousel  $carousel
     * @param  $carouselImageId
     * @return mixed
     */
    public function destroy(Carousel $carousel, $carouselImageId): mixed
    {
        $carouselImage = CarouselImage::find($carouselImageId);
        $carouselImage->delete();

        return response()->json([
            'msg'    => '删除成功',
            'status' => '200',
            'code'   => 0,
        ], 200);
    }
}
