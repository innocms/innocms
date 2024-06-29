@pushonce('header')
    <style>
        .carousel-banner{
            padding-top: 80px;
        }
    </style>
@endpushonce
@if($carousels->count())
    @foreach($carousels as $carousel)
        @php
            $carouselImages=$carousel->images()->where('active',true)->orderBy('position','asc')->get();
        @endphp
        @if($carouselImages->count())
            <section class="carousel-banner mb-4">
                <div class="{{$carousel->style}} px-0">
                    <div id="{{'carousel'. $carousel->id}}" class="carousel slide {{$carousel->cross_fade ? 'carousel-fade' : ''}} {{$carousel->dark_variant? 'carousel-dark' : ''}}"
                         @if($carousel->auto_play) data-bs-ride="carousel" @endif
                         data-bs-touch="{{$carousel->touch_swiping ? "true" : "false"}}"
                    >
                        @if($carousel->with_indicators)
                            <div class="carousel-indicators">
                                @foreach($carouselImages as $carouselImage)
                                    <button type="button" data-bs-target="{{'#carousel'. $carousel->id}}" data-bs-slide-to="{{$loop->index}}" class="{{$loop->first ? 'active' : ''}}" aria-current="true" aria-label="Slide {{$loop->index}}"></button>
                                @endforeach
                            </div>
                        @endif
                        <div class="carousel-inner">
                            @foreach($carouselImages as $carouselImage)
                                <a href="{{$carouselImage->target_url}}">
                                    <div class="carousel-item {{$loop->first ? 'active' : ''}}" data-bs-interval="{{$carouselImage->item_interval}}">
                                        <img src="{{$carouselImage->image_url}}" class="d-block w-100" alt="{{$carouselImage->title}}"
                                             @if(!$agent->isMobile())
                                                 @if($carousel->height)
                                                     style="{{'height: '. $carousel->height .'px !important; object-fit: cover' }}"
                                                 @else
                                                     style="{{$carousel->style=='container' ? "height: 400px !important; object-fit: cover" : "height: 600px !important; object-fit: cover" }}"
                                                @endif
                                             @else
                                                 style="width: 100% !important; height: 180px !important;  object-fit: cover"
                                             @endif

                                        >
                                        @if($carousel->with_captions)
                                            <div class="carousel-caption d-none d-md-block">
                                                <h5>{{$carouselImage->title}}</h5>
                                                <p>{{$carouselImage->description}}</p>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @if($carousel->with_controls)
                            <button class="carousel-control-prev" type="button" data-bs-target="{{'#carousel'. $carousel->id}}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="{{'#carousel'. $carousel->id}}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>
                </div>
            </section>
        @endif
    @endforeach
@endif
