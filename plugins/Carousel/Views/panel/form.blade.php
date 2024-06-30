@extends('panel::layouts.app')

@section('title', '轮播图配置')

@section('content')
    <div class="card h-min-600">
        <div class="card-body">
            <form class="needs-validation" novalidate
                  action="{{ $item->id ? panel_route('carousels.update', [$item->id]) : panel_route('carousels.store') }}"
                  method="POST">
                @csrf
                @method($item->id ? 'PUT' : 'POST')
                <div class="row">
                    <x-panel-form-switch-radio title="是否启用" name="active" :value="old('active', $item->active ?? true)"
                                               placeholder="是否启用"/>
                    <div class="col-6">
                        <x-panel-form-input title="轮播图名称" name="name" :value="old('name', $item->name ?? '')"
                                            placeholder="轮播图名称" required="required"/>
                        <x-panel-form-select title="所在页面" name="page_id" :value="old('page_id', $item->page_id ?? 0)"
                                             :options="$pages" key="value" label="label"/>
                        <x-panel-form-select title="轮播图所在位置" name="position" :value="old('position', $item->position ?? 'top')"
                                             :options="$positions" key="value" label="label"/>
                        <x-panel-form-select title="轮播图风格" name="style" :value="old('style', $item->style ?? 'container-fluid')"
                                             :options="$styles" key="value" label="label"/>
                        <x-panel-form-input title="轮播图高度" name="height"
                                            :value="old('height', $item->height ? : ($item->style='container-fluid' ? 600 :400))"
                                            placeholder="建议100%全屏600，响应式400"/>
                        <x-panel-form-input title="轮播图排序" name="order_index"
                                            :value="old('order_index', $item->order_index ?? 0)"
                                            placeholder="默认0"/>
                        <x-panel-form-switch-radio title="启用自动播放" name="auto_play" :value="old('auto_play', $item->auto_play ?? true)"
                                                   placeholder="是否启用"/>
                    </div>
                    <div class="col-6">
                        <x-panel-form-switch-radio title="启用导航按钮" name="with_controls" :value="old('with_controls', $item->with_controls ?? true)"
                                                   placeholder="是否启用"/>
                        <x-panel-form-switch-radio title="启用指示器" name="with_indicators" :value="old('with_indicators', $item->with_indicators ?? true)"
                                                   placeholder="是否启用"/>
                        <x-panel-form-switch-radio title="启用文字介绍" name="with_captions" :value="old('with_captions', $item->with_captions ?? false)"
                                                   placeholder="是否启用"/>
                        <x-panel-form-switch-radio title="启用交叉渐变" name="cross_fade" :value="old('cross_fade', $item->cross_fade ?? false)"
                                                   placeholder="是否启用"/>
                        <x-panel-form-switch-radio title="黑暗模式" name="dark_variant" :value="old('dark_variant', $item->dark_variant ?? false)"
                                                   placeholder="是否启用"/>
                        <x-panel-form-switch-radio title="触控滑动" name="touch_swiping" :value="old('touch_swiping', $item->touch_swiping ?? true)"
                                                   placeholder="是否启用"/>
                    </div>
                </div>
                <x-panel::form.bottom-btns/>
            </form>

            @if($item->id)
            <hr>
            <h5>轮播图图片配置</h5>

                @foreach($carouselImages as $carouselImage)
                    <div class="card my-4 shadow-sm">
                        <form action="{{panel_route('images.update',[$item->id, $carouselImage])}}" method="POST">
                            @method('PUT')
                            {{csrf_field()}}
                             <div class="card-body">
                                 <div class="row">
                                     <div class="col-6">
                                         <div class="card-img-top">
                                             <img class="w-100" src="{{$carouselImage->image_url}}" alt="">
                                         </div>
                                     </div>
                                     <div class="col-6">
                                         <input type="hidden" name="carousel_id" value="{{$item->id}}">
                                         <input type="hidden" name="image_url" value="{{old('image_url', $carouselImage->image_url)}}"></input>
                                         <x-panel-form-input title="轮播图标题" name="title" :value="old('title', $carouselImage->title ?? '')"
                                                             placeholder="轮播图标题"/>
                                         <x-panel-form-input title="轮播图描述" name="description" :value="old('description', $carouselImage->description ?? '')"
                                                             placeholder="轮播图描述"/>
                                         <x-panel-form-input title="超链接地址" name="target_url" :value="old('target_url', $carouselImage->target_url ?? '')"
                                                             placeholder="超链接地址"/>
                                         <x-panel-form-input title="排序" name="position" :value="old('position', $carouselImage->position ?? '')"
                                                             placeholder="排序"/>
                                         <x-panel-form-switch-radio title="是否启用" name="active" :value="old('active', $carouselImage->active ?? true)"
                                                                    placeholder="是否启用"/>
                                         <x-panel-form-input title="轮播图过渡时长" name="item_interval" :value="old('item_interval', $carouselImage->item_interval ?? 5000)"
                                                             placeholder="轮播图名称"/>
                                     </div>
                                 </div>
                            </div>
                            <div class="card-footer d-grid gap-2 d-md-flex justify-content-md-end">
                                <button class="btn btn-primary me-md-2" type="submit">更新</button>
                                <button class="btn btn-danger me-md-2 deleteBtn" type="button" data-route-url="{{panel_route('images.destroy',[$item->id, $carouselImage])}}">删除</button>
                            </div>
                        </form>
                    </div>

                @endforeach

                <div class="my-4">
                    @if($carouselImages->count()<10)
                        @include('Carousel::panel._image_uploader')
                    @else
                        <p class="text-danger">
                            最多可以上传10张图，当前已满10张。您可以删除不需要的图片后再次上传。
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>
    </div>
@endsection

@push('footer')
    <script>
        $('.deleteBtn').click(function (e){
            let formData = new FormData();
            formData.append('_token',"{{csrf_token()}}");
            axios.delete($(this).data('routeUrl'),formData,{}).then(function (res){
                layer.msg('删除成功！', {icon: 1});
                location.reload();
            }).catch(function (err) {
                layer.msg(err.response.data.message, {icon: 2});
            }).finally(function () {
                $(this).addClass('disabled');
            });
        })
    </script>
@endpush
