<div class="single-product-wrapper">
    <div class="single-product-item">
        @if(($product->promotion == 1) && (($product->last_date > date('Y-m-d')) || !isset($product->last_date)))
        <div class="product-promo-text style1 bg-danger">
            <span>-{{ round(($product->price - $product->promotion_price) / $product->price * 100) }}%</span>
        </div>    
        @endif
        <a href="{{url('product')}}/{{$product->slug}}/{{$product->id}}"  class="view-details">
            @if($product->image!==null)
            @php
                $images = explode(',', $product->image);
                $product->image = $images[0];
            @endphp
            <img loading="lazy" class="product-img" data-src="{{ url('images/product/medium/') }}/{{ $product->image }}" alt="{{ $product->name }}">
            @else
            <img loading="lazy" src="https://dummyimage.com/300x300/e5e8ec/e5e8ec&text={{ $product->name }}" alt="{{ $product->name }}">
            @endif
        </a>
        <div class="product-overlay">
            @if(in_array($product->id,explode(',',$wishlist)))
            <a><span style="color: var(--theme-color);" class="material-symbols-outlined">favorite</span></a>
            @else
            <a data-id="{{$product->id}}" class="add-to-wishlist"><span class="material-symbols-outlined">favorite</span></a>
            @endif
            <!-- <a data-toggle="modal" data-target="#detailsModal" title="quick view"><span class="material-symbols-outlined">zoom_in</span></a> -->
        </div>
    </div>

    <div class="product-details">
        <a class="product-name" href="{{url('product')}}/{{$product->slug}}/{{$product->id}}">
            {!! ucwords($product->name) !!}
            @if(isset($product->unit))
            <span class="product-quantity">({{ $product->unit->unit_name }})</span>
            @endif
        </a>
        <div class="product-price">
            @if(($product->promotion == 1) && (($product->last_date > date('Y-m-d')) || !isset($product->last_date)))
            <span class="price">{{$currency->symbol ?? $currency->code}}{{ $product->promotion_price }}</span>
            <span class="old-price">{{$currency->symbol ?? $currency->code}}{{ $product->price }}</span>
            @else
            <span class="price">{{$currency->symbol ?? $currency->code}}{{ $product->price }}</span>
            @endif
        </div> 
        @if($product->in_stock == 1)
            @if(is_null($product->is_variant))
            <form class="d-flex justify-content-between" method="post" id="add_to_cart_{{ $product->id }}">
                @csrf
                <div class="d-flex align-items-center">
                    <div class="input-qty">
                        <button type="button" class="quantity-left-minus">
                            <i class="material-symbols-outlined">remove</i>
                        </button>
                        <input type="number" name="qty" class="input-number" value="1" min="1" max="{{ $product->qty }}">
                        <button type="button" class="quantity-right-plus">
                            <i class="material-symbols-outlined">add</i>
                        </button>
                    </div>
                </div>
                <button data-id="{{ $product->id }}" type="submit" class="button style1 add-to-cart"><span class="material-symbols-outlined">shopping_bag</span></button>
            </form>
            @else
            <div class="text-center">
                <a href="{{url('/')}}/product/{{$product->slug}}/{{$product->id}}" class="button style1">{{trans('file.Add to cart')}}</a>
            </div>
            @endif
        @else
        <span>{{trans('file.Out of stock')}}</span>
        @endif
    </div>
</div>

