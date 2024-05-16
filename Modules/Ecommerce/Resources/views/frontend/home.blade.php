@extends('ecommerce::frontend.layout.main')

@section('title') {{ $ecommerce_setting->site_title ?? '' }} @endsection

@section('description') {{ '' }} @endsection

@section('content')

@if(isset($sliders))
<!--Home Banner starts -->
<section class="banner-area v3 pt-0">
    <div class="container-fluid">
        <div class="single-banner-item">
            <div class="row">
                <div class="col-md-9 offset-md-3">
                    <div id="hero-slider" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($sliders as $key=>$slider)
                            <a class="carousel-item @if($key == 0) active @endif" href="{{$slider->link}}">
                                <div class="single-carousel-item" style="background-image: url('{{ url('frontend/images/slider/desktop/') }}/{{$slider->image1}}'); background-size: cover; background-position: center;"></div>
                            </a>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-target="#hero-slider" data-slide="prev">
                            <span aria-hidden="true"><i class="material-symbols-outlined">chevron_left</i></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-target="#hero-slider" data-slide="next">
                            <span aria-hidden="true"><i class="material-symbols-outlined">chevron_right</i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Home Banner Area ends-->
@endif

@if(isset($widgets))
@foreach($widgets as $widget)
@if($widget->name == 'category-slider-widget')
@include('ecommerce::frontend.includes.category-slider-widget')
@endif

@if($widget->name == 'brand-slider-widget')
@include('ecommerce::frontend.includes.brand-slider-widget')
@endif

@if($widget->name == 'product-category-widget')
@include('ecommerce::frontend.includes.product-category-widget')
@endif

@if($widget->name == 'product-collection-widget')
@include('ecommerce::frontend.includes.product-collection-widget')
@endif

@if($widget->name == 'text-widget')
@include('ecommerce::frontend.includes.text-widget')
@endif

@if($widget->name == 'tab-product-category-widget')
@include('ecommerce::frontend.includes.tab-product-category-widget')
@endif

@if($widget->name == 'tab-product-collection-widget')
@include('ecommerce::frontend.includes.tab-product-collection-widget')
@endif
@endforeach
@endif

@if(isset($recently_viewed) && count($recently_viewed) > 0)
@include('ecommerce::frontend.includes.recently-viewed-products')
@endif

@endsection

@section('script')
<script src="{{ asset('public/frontend/js/swiper.min.js') }}"></script>
<script type="text/javascript">
    "use strict";

    //category carousel
    if (('.category-slider-wrapper').length > 0) {
        $('.category-slider-wrapper').each(function() {
            var swiper = new Swiper('.category-slider-wrapper', {
                slidesPerView: 6,
                spaceBetween: 30,
                lazy: true,
                //centeredSlides: true,
                loop: $(this).data('loop'),
                navigation: {
                    nextEl: '.category-button-next',
                    prevEl: '.category-button-prev',
                },
                autoplay: {
                    delay: 4000,
                },
                // Responsive breakpoints
                breakpoints: {
                    // when window width is <= 675
                    675: {
                        slidesPerView: 2,
                        spaceBetween: 30
                    },

                    // when window width is <= 991
                    991: {
                        slidesPerView: 4,
                        spaceBetween: 30
                    },
                    // when window width is <= 1024px
                    1024: {
                        slidesPerView: 6,
                        spaceBetween: 15
                    }
                }
            });
        })

        $(document).ready(function(){
            $('.category-img').each(function(){
                var img = $(this).data('src');
                $(this).attr('src', img);
            })
        })
    }

    //product carousel
    if (('.product-slider-wrapper').length > 0) {
        $('.product-slider-wrapper').each(function() {
            var swiper = new Swiper('.product-slider-wrapper', {
                slidesPerView: 5,
                spaceBetween: 0,
                lazy: true,
                observer: true,
                observeParents: true,
                loop: false,
                navigation: {
                    nextEl: '.product-button-next',
                    prevEl: '.product-button-prev',
                },
                autoplay: {
                    delay: 4000,
                },
                // Responsive breakpoints
                breakpoints: {
                    // when window width is <= 675
                    675: {
                        slidesPerView: 2,
                        spaceBetween: 30
                    },

                    // when window width is <= 991
                    991: {
                        slidesPerView: 4,
                        spaceBetween: 30
                    },
                    // when window width is <= 1024px
                    1024: {
                        slidesPerView: 6,
                        spaceBetween: 15
                    }
                }
            });
        })
    }

    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var parent = '#add_to_cart_' + id;

        var qty = $(parent + " input[name=qty]").val();

        var route = "{{ route('addToCart') }}";

        var btn = $(this);

        var btn_text = $(this).html();

        $(this).html('<span class="spinner-border spinner-border-sm" role="status"><span class="sr-only">...</span></span>');

        $.ajax({
            url: route,
            type: "POST",
            data: {
                product_id: id,
                qty: qty,
            },
            success: function(response) {
                if (response) {
                    $('.alert').addClass('alert-custom show');
                    $('.alert-custom .message').html(response.success);
                    $('.cart__menu .cart_qty').html(response.total_qty);
                    $('.cart__menu .total').html('{{$currency->symbol ?? $currency->code}}' + response.subTotal.toFixed(2));
                    $(btn).html(btn_text);
                    setTimeout(function() {
                        $('.alert').removeClass('show');
                    }, 4000);
                }
            },
        });
    })
</script>

@endsection