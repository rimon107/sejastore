@php

$all_categories = $categories_list->where('featured',1);

$parents = $all_categories->whereNull('parent_id')->pluck('id')->toArray();

$total_qty = session()->has('total_qty') ? session()->get('total_qty') : 0;
$subTotal = session()->has('subTotal') ? session()->get('subTotal') : 0;

@endphp

<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <!-- Metas -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Document Title -->
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')" />
    <meta name="author" content="LionCoders" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta property="og:url" content="{{Request::url()}}" />
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:description" content="@yield('description')" />
    @if(request()->is('product/*'))
    <meta property="og:image" content="@yield('image')" />
    <meta property="product:image_link" content="@yield('image')">
    <meta property="product:brand" content="@yield('brand')">
    <meta property="product:availability" content="in stock">
    <meta property="product:condition" content="new">
    <meta property="product:price:amount" content="@yield('price')">
    <meta property="product:price:currency" content="{{$currency->code}}">
    <meta property="product:retailer_item_id" content="@yield('id')">
    <meta property="product:item_group_id" content="@yield('category_id')">
    @else
    <meta property="og:image" content="https://www.lion-coders.com/public/frontend/images/slider/slide-2.png" />
    @endif

    @if(!config('database.connections.saleprosaas_landlord'))
    <!-- Links -->
    <link rel="icon" type="image/ico" href="{{ url('frontend/images') }}/{{$ecommerce_setting->favicon ?? ''}}" />
    <!-- Plugins CSS -->
    <link href="{{ asset('frontend/css/plugins.css') }}" rel="stylesheet" />
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    </noscript>
    @else
    <!-- Links -->
    <link rel="icon" type="image/ico" href="{{ url('frontend/images') }}/{{$ecommerce_setting->favicon ?? ''}}" />
    <!-- Plugins CSS -->
    <link href="{{ asset('../../public/frontend/css/plugins.css') }}" rel="stylesheet" />
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('../../public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('../../public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    </noscript>
    @endif

    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" rel="stylesheet" />

    <style>
        :root {
            --theme-color: #fe0103;
        }

        /* Minified style.css */
        {!! str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', preg_replace('/\s*([{}|:;,])\s+/', '$1', file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/css/style.css"))) !!}

        .search_result {background:#FFF;border:1px solid #e4e6fc;border-radius:5px;box-shadow:0 5px 10px rgba(44,44,44.0.5);display:none;overflow-y:scroll;position:absolute;width:calc(100% - 45px);z-index:999999}
        .search_result > a {cursor:pointer;display:flex;align-items:center;padding:10px;position:relative;}
        .search_result > a > img {margin-right:15px;max-width:30px;}
        .search_result > a h4 {font-size:0.85rem;font-weight:500;margin-bottom:0;text-align:left;}
    </style>

    @stack('css')
    
    @if(isset($ecommerce_setting->custom_css))
    <style>
    {{$ecommerce_setting->custom_css}}
    </style>
    @endif

    @if(env('USER_VERIFIED') == false)
    <style>
        #switcher {
            list-style: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        #switcher li {
            float: left;
            width: 30px;
            height: 30px;
            margin: 0 15px 15px 0;
            border-radius: 3px;
        }

        #demo {
            border-right: 1px solid #d5d5d5;
            width: 250px;
            height: 100%;
            left: -250px;
            position: fixed;
            padding: 50px 30px;
            background-color: #fff;
            transition: all 0.3s;
            z-index: 999999;
        }

        #demo.open {
            left: 0;
        }

        .demo-btn {
            background-color: #fff;
            border: 1px solid #d5d5d5;
            border-left: none;
            border-bottom-right-radius: 3px;
            border-top-right-radius: 3px;
            color: var(--theme-color);
            font-size: 30px;
            height: 40px;
            position: absolute;
            right: -40px;
            text-align: center;
            top: 45%;
            width: 40px;
        }
    </style>
    @endif
</head>

<body>
    @if(env('USER_VERIFIED') == false)
    <div id="demo">
        <h6>Theme Colors</h6>
        <ul id="switcher" class="p-0">
            <li class="color-change-theme" data-color="#0071df" style="background-color:#0071df"></li>
            <li class="color-change-theme" data-color="#f51e46" style="background-color:#f51e46"></li>
            <li class="color-change-theme" data-color="#fa9928" style="background-color:#fa9928"></li>
            <li class="color-change-theme" data-color="#fd6602" style="background-color:#fd6602"></li>
            <li class="color-change-theme" data-color="#59b210" style="background-color:#59b210"></li>
            <li class="color-change-theme" data-color="#ff749f" style="background-color:#ff749f"></li>
            <li class="color-change-theme" data-color="#f8008c" style="background-color:#f8008c"></li>
            <li class="color-change-theme" data-color="#6453f7" style="background-color:#6453f7"></li>
        </ul>
        <div class="demo-btn"><i class="material-symbols-outlined">settings</i></div>
    </div>
    @endif
    <!--Header Area starts-->
    <header>
        <div id="header-middle" class="header-middle">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-baseline">
                    <div class="category__menu show-on-mobile"><i class="material-symbols-outlined">menu</i></div>
                    <div class="logo">
                        <a href="{{url('/')}}">
                            @if(!config('database.connections.saleprosaas_landlord'))
                                @if(isset($ecommerce_setting->logo))
                                <img src="{{ url('frontend/images/') }}/{{$ecommerce_setting->logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                                @else
                                <img src="{{ asset('public/logo') }}/{{$general_setting->site_logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                                @endif
                            @else
                                @if(isset($ecommerce_setting->logo))
                                <img src="{{ asset('../../public/frontend/images/') }}/{{$ecommerce_setting->logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                                @else
                                <img src="{{ asset('../../public/logo') }}/{{$general_setting->site_logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                                @endif
                            @endif
                        </a>
                    </div>
                    <form action="{{route('products.search')}}" method="post" class="header-search" style="max-width:550px">
                        @csrf
                        <div class="header-search-container">
                            <input id="search" type="text" placeholder="Search products..." name="search">
                            <div class="search_result"></div>
                        </div>
                        <button class="btn btn-search" type="submit" style="margin-top:-2px"><span class="d-flex"><i class="material-symbols-outlined">search</i></span></button>
                    </form>
                    <ul class="offset-menu-wrapper">
                        <!-- <li class="language"><a  class="active" href="">En</a> / <a href="">Bn</a></li> -->
                        @guest
                        <li>
                            <a href="{{url('customer/login')}}">Login</a>
                        </li>
                        @endguest
                        @if(auth()->user())
                        <li class="user-menu">
                            <i class="material-symbols-outlined">person_add</i>
                            <ul class="user-dropdown-menu">
                                <li><a href="{{url('customer/account-details')}}">My Account</a></li>
                                <li><a href="{{url('customer/orders')}}">Order History</a></li>
                                <li><a href="{{url('customer/address')}}">Addresses</a></li>
                                <li><a href="{{url('customer/logout')}}"> {{trans('file.logout')}}</a></li>
                            </ul>
                        </li>
                        @endif
                        <li>
                            <a href="{{url('track-order')}}" title="{{trans('file.Track Order')}}"><i class="material-symbols-outlined">pin_drop</i></a>
                        </li>
                        <li class="wishlist__menu">
                            <a href="{{url('customer/wishlist')}}" title="{{trans('file.Wishlist')}}"><i class="material-symbols-outlined" title="{{trans('file.My Wishlist')}}">favorite</i></a>
                            <span class="badge badge-light cart_qty">
                            {{ $wishlist_count }}
                            </span>
                        </li>
                        @php

                        $total_qty = session()->has('total_qty') ? session()->get('total_qty') : 0;
                        $subTotal = session()->has('subTotal') ? session()->get('subTotal') : 0;

                        if($total_qty == 0){
                            $subTotal = 0;
                        }

                        @endphp
                        <li class="cart__menu">
                            <i class="material-symbols-outlined" title="{{trans('file.Cart')}}">shopping_bag</i>
                            <span class="badge badge-light cart_qty">{{ $total_qty ?? 0}}</span>
                            <span class="total">{{$currency->symbol}}{{ $subTotal ?? 0.00}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3 d-none d-lg-flex d-xl-flex">
                        <div class="category-list">
                            <ul>
                                <li class="has-dropdown">
                                    <a class="category-button" href="#"><i class="material-symbols-outlined">menu</i> Categories</a>
                                    <ul class="dropdown sidebar {{ (request()->is('/')) ? 'show' : '' }}">
                                        @php $i = 0 @endphp
                                        @foreach($all_categories as $category)
                                            @if(in_array($category->id, $parents))
                                            @php
                                            $categories = $all_categories->where('parent_id', $category->id)->where('is_active', 1);
                                            @endphp
                                            @if(count($categories) > 0)
                                            <li class="has-dropdown"><a href="{{ url('shop') }}/{{ $category->slug }}">@if(isset($category->icon))<img src="{{ url('images/category/icons/') }}/{{ $category->icon }}" alt="{{ $category->name }}">@endif <span>{{ $category->name }}</span></a>
                                                <ul class="dropdown">
                                                    @foreach($categories as $cat)
                                                    <li><a href="{{ url('shop') }}/{{ $cat->slug }}">@if(isset($cat->icon))<img src="{{ url('images/category/icons/') }}/{{ $cat->icon }}" alt="{{ $cat->name }}">@endif <span>{{ $cat->name }}</span></a></li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                            @else
                                            <li><a href="{{ url('shop') }}/{{ $category->slug }}">@if(isset($category->icon))<img src="{{ url('images/category/icons/') }}/{{ $category->icon }}" alt="{{ $category->name }}">@endif <span>{{ $category->name }}</span></a>
                                            </li>
                                            @endif
                                            @endif
                                            @php $i++ @endphp
                                        @endforeach
                                        <li><a class="button style3 text-center" href="{{ url('shop') }}/"> All Categories</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="main-header-inner">
                            <div id="main-menu" class="main-menu">
                                <nav id="mobile-nav" class="d-flex justify-content-between">
                                    <ul>
                                        @if(!empty($topNavItems))
                                            @foreach($topNavItems as $nav)
                                                @if(!empty($nav->children[0]))
                                                <li class="user-menu">
                                                    <a href="#">@if($nav->name == NULL) {{$nav->title}} @else {{$nav->name}} @endif <i class="caret"></i>
                                                        <ul class="user-dropdown-menu">
                                                            @foreach($nav->children[0] as $childNav)
                                                            @if($childNav->type == 'custom')
                                                            <li><a href="{{$childNav->slug}}" target="_blank">@if($childNav->name == NULL) {{$childNav->title}} @else {{$childNav->name}} @endif</a></li>
                                                            @elseif($childNav->type == 'category')
                                                            <li><a href="{{url('shop')}}/{{$childNav->slug}}">@if($childNav->name == NULL) {{$childNav->title}} @else {{$childNav->name}} @endif</a></li>
                                                            @else
                                                            <li><a href="{{url('')}}/{{$childNav->slug}}">@if($childNav->name == NULL) {{$childNav->title}} @else {{$childNav->name}} @endif</a></li>
                                                            @endif
                                                            @endforeach
                                                        </ul>
                                                    </a>
                                                </li>
                                                @else
                                                    @if($nav->type == 'custom')
                                                    <li><a href="{{$nav->slug}}" target="_blank">@if($nav->name == NULL) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @elseif($nav->type == 'category')
                                                    <li><a href="{{url('shop')}}/{{$nav->slug}}">@if($nav->name == NULL) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @elseif($nav->type == 'page' && ($nav->slug == 'home'))
                                                    <li><a href="{{url('/')}}">@if($nav->name == NULL) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @elseif($nav->type == 'collection')
                                                    <li><a href="{{url('products')}}/{{$nav->slug}}">@if($nav->name == NULL) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @elseif($nav->type == 'brand')
                                                    <li><a href="{{url('brand')}}/{{$nav->slug}}">@if($nav->name == NULL) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @else
                                                    <li><a href="{{url('')}}/{{$nav->slug}}">@if($nav->name == NULL) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    </ul>
                                    @if(isset($social_links))
                                    <ul class="social-links">
                                        @foreach ($social_links as $link)
                                        <li><a href="{{$link->link}}">{!!$link->icon!!}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="body__overlay"></div>
    <!-- Offset Wrapper starts-->
    <div class="offset__wrapper">
        <div class="shopping__cart">
            <div class="shopping__cart__header">
                <span class="h6">My Cart</span>
                <div class="offsetmenu__close__btn">
                    <span class="material-symbols-outlined">close</span>
                </div>
            </div>
            <div class="shopping__cart__inner">
                <div class="shp__cart__wrap">
                    <div class="d-flex justify-content-center" style="align-items:center;height: 80vh;"><div class="spinner-border text-secondary" role="status"></div></div>
                </div>

            </div>
            <div class="shopping__cart__footer">
                <ul class="shoping__total">
                    <li class="subtotal">Subtotal:</li>
                    <li class="total__price">{{$currency->symbol}}{{ $subTotal ?? 0.00 }}</li>
                </ul>
                <a href="{{ url('cart/') }}" class="button style3">View Cart</a>
                <a href="{{ url('checkout/') }}" class="button style1">Checkout</a>
            </div>
        </div>
    </div>
    <!-- Offset Wrapper ends -->
    <div class="category__list">

    </div>
    <!-- Header Area  ends -->
    <div class="alert alert-dismissible text-center fade" role="alert">
        <button type="button" class="close"><span class="material-symbols-outlined">close</span></button>
        <div class="message">
        </div>
    </div>
    @if(session()->has('message'))
    <div class="alert alert-custom alert-dismissible text-center fade show">
        <button type="button" class="close"><span class="material-symbols-outlined">close</span></button>
        <div class="message">{{ session()->get('message') }}</div>
    </div>
    @endif
    @if (count($errors) > 0)
    <div class="alert alert-custom alert-dismissible text-center fade show">
        <button type="button" class="close"><span class="material-symbols-outlined">close</span></button>
        <div class="message">
            @foreach ($errors->all() as $error)
            <p>- {{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif
    @yield('content')

    <!--Scroll to top starts-->
    <a href="#" id="scrolltotop"><span class="material-symbols-outlined">arrow_upward</span></a>
    <!--Scroll to top ends-->
    @if(isset($footer_top_widgets))
    <section class="promo-area mt-5 pt-5 pb-5">
        <div class="container-fluid">
            <div class="row">
                @foreach($footer_top_widgets as $widget)
                <div class="col-md-3 col-6 d-flex pt-3 pb-3 align-items-center">
                    <div class="promo-icon pr-2">
                        <img src="{{url('frontend/images/features')}}/{{$widget->feature_icon}}" alt="{{$widget->feature_title}}">
                    </div>
                    <div class="promo-content pl-3">
                        <h5>{{$widget->feature_title}}</h5>
                        <span>{{$widget->feature_secondary_title}}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    <!-- Footer section Starts-->
    <div class="footer-wrapper">
        <div class="container-fluid">
            @if(isset($footer_widgets))
            <div class="row">
                @foreach($footer_widgets as $item)
                @if($item->name == 'site-info-widget')
                <div class="col-md-3 col-12">
                    <div class="footer-logo">
                        <a href="#">
                            @if(isset($ecommerce_setting->logo))
                            <img src="{{ url('frontend/images/') }}/{{$ecommerce_setting->logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                            @else
                            <img src="{{ asset('public/logo') }}/{{$general_setting->site_logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                            @endif
                        </a>
                    </div>
                    @if(isset($item->site_info_description))
                    <div class="footer-text">
                        <p>{{$item->site_info_description}}</p>
                    </div>
                    @endif
                    @if(isset($item->site_info_address))
                    <div class="footer-text">
                        <p>{{$item->site_info_address}}</p>
                    </div>
                    @endif
                    @if(isset($item->site_info_phone))
                    <div class="footer-text">
                        <h5 class="text-grey">Need help? Call us:</h5>
                        <a href="tel:{{$ecommerce_setting->store_phone}}">
                            <h4>{{$item->site_info_phone}}</h4>
                        </a>
                    </div>
                    @endif
                    @if(isset($item->site_info_email))
                    <div class="footer-text">
                        <p>{{$item->site_info_email}}</p>
                    </div>
                    @endif
                    @if(isset($item->site_info_hours))
                    <div class="footer-text">
                        <p>{{$item->site_info_hours}}</p>
                    </div>
                    @endif
                </div>
                @endif
                @if($item->name == 'custom-menu-widget')
                <div class="col-md-2 col-sm-6">
                    <div class="footer-widget">

                        <h3>{{$item->quick_links_title}}</h3>

                        @if(isset($item->quick_links_menu))
                        @php
                            $quick_menu_items = $menu_items->where('menu_id', $item->quick_links_menu);
                        @endphp
                        <ul class="footer-menu">
                            @foreach($quick_menu_items as $link)
                            @if($link->type == 'custom')
                            <li><a class="" href="{{$link->slug}}">{{$link->name ?? $link->title}}</a></li>
                            @else
                            <li><a class="" href="{{url('/')}}/{{$link->slug}}">{{$link->name ?? $link->title}}</a></li>
                            @endif
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
                @endif
                @if($item->name == 'newsletter-widget')
                <div class="col-md-3 col-12">
                    <div class="newsletter">
                        <h4>{{$item->newsletter_title}}</h4>
                        <p>{{$item->newsletter_text}}</p>
                        <form class="mt-5" method="post" id="newsletter">
                        @csrf
                            <div class="input-group">
                                <input placeholder="{{trans('file.Your email')}}" class="form-control" type="email" name="email" />
                                <div class="input-group-append">
                                    <button class="button sm style1" type="submit">
                                        {{trans('file.Subscribe')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
            <div class="row footer-bottom">
                <div class="col-md-6">
                    <p>&copy; {{date('Y')}}. All rights reserved</p>
                </div>
                <div class="col-md-6">
                    <p class="developed-by">Developed by {{$general_setting->developed_by}}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer section Ends-->
    <!-- Quick Shop Modal starts -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="ti-close"></i></span>
                    </button>
                    <div class="row">
                        <div class="col-md-5">
                            <img src="" alt="" />
                        </div>
                        <div class="col-md-7">
                            <h3 class="item-name"></h3>
                            <div class="item-price"></div>
                            <div class="item-short-description">
                                <p></p>
                            </div>
                            <div class="item-options">
                                <div class="stock-alert"></div>
                                <form id="" method="post" class="">
                                    @csrf
                                    <div class="input-qty">
                                        <span class="input-group-btn">
                                            <button type="button" class="quantity-left-minus">
                                                <i class="material-symbols-outlined">remove</i>
                                            </button>
                                        </span>
                                        <input type="number" name="qty" class="input-number" value="1" min="1">
                                        <span class="input-group-btn">
                                            <button type="button" class="quantity-right-plus">
                                                <i class="material-symbols-outlined">add</i>
                                            </button>
                                        </span>
                                    </div>
                                    <br>
                                    <input class="product-id" type="hidden" name="product_id" value="">
                                    <button data-id="" class="button style1 add-to-cart-modal"><span class="material-symbols-outlined">shopping_bag</span> Add to cart</button>
                                </form>
                            </div>
                            <div class="item-share mt-3"><span>Share</span>
                            <ul class="footer-social d-inline pad-left-15">
                                <li>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="16" width="10" viewBox="0 0 320 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                            <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z" />
                                        </svg>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/intent/tweet?text=&url=&via=" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                            <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" />
                                        </svg>
                                    </a>
                                </li>
                                <li>
                                    <a href="http://pinterest.com/pin/create/button/?url=&media=&description=" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pinterest" viewBox="0 0 16 16">
                                            <path d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0" />
                                        </svg>
                                    </a>
                                </li>
                            </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Quick shop modal ends-->

    <div class="cart__menu fixed">
        <i class="material-symbols-outlined">shopping_bag</i>
        <span class="badge badge-light cart_qty">{{ $total_qty ?? 0}}</span>
        <span class="total">{{$currency->symbol}}{{ $subTotal ?? 0.00}}</span>
    </div>

    @if(isset($ecommerce_setting->custom_chat))
    {{$ecommerce_setting->custom_chat}}
    @endif

    
    @if(!config('database.connections.saleprosaas_landlord'))
    <!--Plugin js -->
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/plugin.js") !!}
    </script>
    <script src="{{ asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js') }}"></script>
    <!-- Main js -->
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/main.js") !!}
    </script>
    @else
    <!--Plugin js -->
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/plugin.js") !!}
    </script>
    <script src="{{ asset('../../public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js') }}"></script>
    <!-- Main js -->
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/main.js") !!}
    </script>
    @endif
    @if(isset($ecommerce_setting->custom_js))
    <script type="text/javascript">
    {{$ecommerce_setting->custom_js}}
    </script>
    @endif

    <script type="text/javascript">
        "use strict";

        $(document).ready(function(){
            $('.product-img').each(function(){
                var img = $(this).data('src');
                $(this).attr('src', img);
            })
        })

        @if(env('USER_VERIFIED') == false)
        $('.demo-btn').on('click', function() {
            $('#demo').toggleClass('open');
            $('.color-change-theme').click(function() {
                var color = $(this).data('color');
                $('#color-input').val(color);
                $('body').css('--theme-color', color);
            });
            $('.color-change-navbg').click(function() {
                var color = $(this).data('color');
                $('#color-input').val(color);
                $('body').css('--navbg-color', color);
            });
            $('.color-change-navtext').click(function() {
                var color = $(this).data('color');
                var hover = $(this).data('hover-color');
                $('#color-input').val(color);
                $('body').css('--menu-text-color', color);
                $('body').css('--menu-text-hover-color', hover);
            });
        });
        @endif

        setInterval(function() {
            $.ajax({
                url: "{{route('session')}}",
                type:"POST",
                success:function(response){
                    //alert('session alive');
                },
            });
        }, 50000); 


        $('#newsletter').on('submit', function(e){
			e.preventDefault();
            var data = $(this).serialize();
			var route = "{{ url('/newsletter/subscribe') }}";
            $('#newsletter button').html('<span class="spinner-border spinner-border-sm text-light" role="status"><span class="sr-only">...</span></span>');
			$.ajax({
		        url: route,
		        type:"POST",
		        data: data,
		        success:function(response){
		            if(response == 'success') {
		            	$('.alert').addClass('alert-custom show');
			            $('.alert-custom .message').html('{{trans("file.Thank You. You have subscribed to our newsletter")}}.');
			            $('#newsletter button').html("{{trans('file.Subscribe')}}");
                        $('#newsletter input').val('');
                        setTimeout(function() {
                            $('.alert').removeClass('show');
                        }, 4000);
		            } else {
                        $('.alert').addClass('alert-custom show');
			            $('.alert-custom .message').html(response.email[0]);
			            $('#newsletter button').html("{{trans('file.Subscribe')}}");
                        $('#newsletter input').val('');
                        setTimeout(function() {
                            $('.alert').removeClass('show');
                        }, 4000);
                    }
		        },
		    });
		})

        //SEARCH FIELD SUGGESTION
        $('#search').on('input', function() {
            var item = $('#search').val();
            if (item.length > 3) {
                $('.search_result').css('display','block').html('<div class="d-block text-center"><div class="spinner-border text-secondary" role="status"><span class="sr-only">Loading...</span></div></div>');
                $.ajax({
                    type: "get",
                    url: "{{url('search')}}/" + item,
                    success: function(data) {
                        $('.search_result').html('').css('height','200px');
                        $.each(data,function(key, value){ 
                            var image = value.image.split(',');
                            var url = "{{url('/product')}}/"+value.slug+"/"+value.id;
                            $('.search_result').append('<a href="'+url+'"><img src="{{asset("public/images/product/small/")}}/'+image[0]+'"><h4>'+value.name+'</h4></a>')
                        })
                    }
                })  
            } else if (item.length < 3) {
                $('.search_result').html('').css('display','none');
            }
        });

        $('.btn-search').on('click',function(e){
            e.preventDefault();
            var item = $('#search').val();
            if (item.length < 1) {
                $('.alert').addClass('alert-custom show');
                $('.alert-custom .message').html('{{trans("file.Please type a product name to search")}}');
                setTimeout(function() {
                    $('.alert').removeClass('show');
                }, 4000);
            } else {
                $(this).parent().submit();
            }
        })

        // Show empty cart message
        var cart_total = $('.shoping__total .total__price').html();

        if (parseFloat(cart_total.replace('{{$currency->symbol}}', '')) < 1) {
            $('.shp__cart__wrap').html('<h6 class="mt-3">No item in your cart</h6>');
        }

        // sidebar cart show products in cart
        $(document).on('click', '.cart__menu', function() {
            var route = "{{ route('cart') }}";
            $.ajax({
                url: route,
                type: "GET",
                success: function(response) {
                    console.log(response);
                    if (response.cart) {
                        $('.shp__cart__wrap').html('');
                        $.each(response.cart, function(index, value) {
                            var image = value.image.split(',');
                            var variant = '';
                            var true_variant = '';
                            var id = value.id;
                            if(value.variant != 0){
                                true_variant = value.variant;
                                variant = '(<span>'+ value.variant.join(' | ') +'</span>)'; 
                                id = value.id+'-'+value.variant.join('-')
                            }
                            var item = '<div class="shp__single__product" id="cart-item-' + id + '"><div class="shp__pro__thumb"><a href="{{url("/product")}}/' + value.slug + '/' + value.id + '"><img src="{{url("/images/product/small")}}/' + image[0] + '" alt="' + value.name + '"></a></div><div class="shp__pro__details"><h2><a href="{{url("/product")}}/' + value.slug + '/' + value.id + '">' + value.name + '</a></h2>'+ variant +' <span>' + value.qty + '</span> x <span class="shp__price">' + (value.total_price / value.qty) + '</span></div><div class="remove__btn"><a class="remove-from-cart" data-id="' + value.id + '" data-variant="'+ true_variant +'" title="Remove this item"><i class="material-symbols-outlined">close</i></a></div></div>';
                            $('.shp__cart__wrap').append(item);
                        })
                        $('.shoping__total .total__price').html('{{$currency->symbol}}' + response.subTotal.toFixed(2));

                        if (parseFloat(response.subTotal) < 1) {
                            $('.shp__cart__wrap').html('<h6 class="mt-3">{{trans("file.No item in your cart")}}</h6>');
                            $('.shopping__cart__footer .button').addClass('d-none');
                        } else {
                            $('.shopping__cart__footer .button').removeClass('d-none');
                        }
                    }
                },
            });
        })

        // Remove from sidebar cart
        $(document).on('click', '.remove-from-cart', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var variant = $(this).data('variant');

            var route = "{{ route('removeFromCart') }}";

            $.ajax({
                url: route,
                type: "POST",
                data: {
                    product_id: id,
                    variant: variant,
                },
                success: function(response) {
                    console.log(response);
                    if (response) {
                        $('.alert').addClass('alert-custom show');
                        $('.alert-custom .message').html(response.success);
                        $('#cart-item-' + response.deleted_item).html('').css('padding', 0);
                        $('.shoping__total .total__price').html('{{$currency->symbol}}' + response.subTotal.toFixed(2));
                        $('.cart__menu .cart_qty').html(response.total_qty);
                        $('.cart__menu .total').html('{{$currency->symbol}}' + response.subTotal.toFixed(2));
                        $('.single-cart-item-' + response.deleted_item).html('').css('padding', 0);
                        setTimeout(function() {
                            $('.alert').removeClass('show');
                        }, 4000);

                        if (response.subTotal < 1) {
                            $('.shp__cart__wrap').html('<h6 class="mt-3">{{trans("file.No item in your cart")}}</h6>');
                            $('.cart__menu .cart_qty').html('0');
                            $('.cart__menu .total').html('{{$currency->symbol}}0');
                            $('.shopping__cart__footer .button').addClass('d-none');

                            setTimeout(function() {
                                $('.alert').removeClass('show');
                            }, 4000);
                        }
                    }
                },
            });
        })

        $(document).on('click', '.add-to-cart-modal', function(e) {
            e.preventDefault();
            var id = $(this).siblings('.product-id').val();
            var parent = '#add_to_cart_modal_' + id;

            var qty = $("#detailsModal input[name=qty]").val();

            var route = "{{ route('addToCart') }}";

            $.ajax({
                url: route,
                type: "POST",
                data: {
                    product_id: id,
                    qty: qty,
                },
                success: function(response) {
                    history.back();
                    console.log(response);
                    if (response) {
                        $('.alert').addClass('alert-custom show');
                        $('.alert-custom .message').html(response.success);
                        $('.cart__menu .cart_qty').html(response.total_qty);
                        $('.cart__menu .total').html('{{$currency->symbol}}' + response.subTotal.toFixed(2));
                        $('#detailsModal').modal('toggle');

                        setTimeout(function() {
                            $('.alert').removeClass('show');
                        }, 4000);
                    }
                },
            });
        })
        
        var quantity = 0;
        $('.quantity-right-plus').on("click", function(e) {
            e.preventDefault();
            var quantity = parseInt($(this).siblings("input.input-number").val());
            //var max = $(this).siblings("input.input-number").attr('max');
            $(this).siblings("input.input-number").val(quantity + 1);
            // if ((quantity+1) > max) {
            //     $(this).siblings("input.input-number").val(max);
            // }
        });
        $('.quantity-left-minus').on("click", function(e) {
            e.preventDefault();
            var quantity = parseInt($(this).siblings("input.input-number").val());
            if (quantity > 1) {
                $(this).siblings("input.input-number").val(quantity - 1);
            }
        });

        $('.add-to-wishlist').on('click', function() {
            @if(auth()->user() && (auth()->user()->role_id == 5))
            var product_id = $(this).data('id');
            $(this).find('span').css('color','var(--theme-color)');
            $(this).removeClass('add-to-wishlist');
            $.ajax({
                type: "get",
                url: "{{url('customer/wishlist')}}/" + product_id,
                success: function(data) {
                    console.log(data);
                    if(data == 'fail'){
                        $('.alert').addClass('alert-custom show');
                        $('.alert-custom .message').html('{{trans("file.product already on wishlist")}}');
                    } else {
                        $('.alert').addClass('alert-custom show');
                        $('.alert-custom .message').html('{{trans("file.product added to wishlist")}}');
                    }
                    setTimeout(function() {
                        $('.alert').removeClass('show');
                    }, 4000);
                }
            })  
            @else
                $('.alert').addClass('alert-custom show');
                $('.alert-custom .message').html('{{trans("file.Please login first")}}');
                setTimeout(function() {
                    $('.alert').removeClass('show');
                }, 4000);
            @endif
        });
    </script>

    @yield('script')

</body>

</html>