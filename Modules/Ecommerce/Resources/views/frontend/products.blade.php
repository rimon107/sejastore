@extends('ecommerce::frontend.layout.main')

@section('title') @if($category->page_title) {{$category->page_title}} @else {{ $ecommerce_setting->site_title ?? '' }} @endif @endsection

@section('description') @if($category->short_description) {{$category->short_description}} @else  @endif @endsection

@section('content')
	<!--Breadcrumb Area start-->
    <div class="breadcrumb-section">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="page-title">{{ $category->name }}</h1>
                    <ul>
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li class="active">{{ $category->name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--Breadcrumb Area ends-->
    <!--Shop cart starts-->
    <section class="shop-cart-section">
        <div class="container-fluid">
            <div class="row"> 
            	<div class="product-grid">
                	@foreach($products as $product)
                    @include('ecommerce::frontend.includes.product-template')
                    @endforeach
                    @if(count($products) == 0)
                    <h3 class="text-center mt-5 mb-5 d-block w-100">Sorry, no products found</h3>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!--Shop cart ends-->
@endsection

@section('script')
	<script type="text/javascript">
		"use strict";

		$(document).on('click', '.add-to-cart', function(e){
			e.preventDefault();
            var id = $(this).data('id');
            var parent = '#add_to_cart_'+id;

			var qty = $(parent+" input[name=qty]").val();

			var route = "{{ route('addToCart') }}";

			$.ajax({
		        url: route,
		        type:"POST",
		        data:{
					product_id: id,
					qty: qty,
		        },
		        success:function(response){
			        console.log(response);
		            if(response) {
		            	$('.alert').addClass('alert-custom show');
			            $('.alert-custom .message').html(response.success);
			            $('.cart__menu .cart_qty').html(response.total_qty);
			            $('.cart__menu .total').html('{{$currency->symbol ?? $currency->code}}'+response.subTotal.toFixed(2));

                        setTimeout(function() {
                            $('.alert').removeClass('show');
                        }, 4000);
		            }
		        },
		    });
		})

        // Load more
        var page_num = 1;
        var total_page = <?php echo json_encode($products->total()) ?>;
        $(window).scroll( function() {
            if( ( $(window).scrollTop() + $(window).height() > ( $(document).height() * (2/3) ) ) && (total_page>=page_num) ) {
                loadMoreData(++page_num);
            }
            
        });
        
        function loadMoreData(page_num) {
            $.ajax({
                url: '?page=' + page_num,
                type: "get",
            }).done(function(data) {
                $(".product-grid").append(data.html);
                $('.product-img').each(function(){
                    var img = $(this).data('src');
                    $(this).attr('src', img);
                })
            }).fail(function(jqXHR, ajaxOptions, thrownError)
            {
                 console.log('server not responding...');
            });
        }
	</script>
@endsection