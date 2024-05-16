@extends('ecommerce::frontend.layout.main')

@section('title') {{ $ecommerce_setting->site_title ?? '' }} @endsection

@section('description') {{ $ecommerce_setting->site_title ?? '' }} @endsection

@section('content')
	<!--Breadcrumb Area start-->
    <div class="breadcrumb-section">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="page-title">{{trans('file.Order Details')}}</h1>
                    <ul>
                        <li><a href="{{url('customer/profile')}}">{{trans('file.dashboard')}}</a></li>
                        <li class="active">{{trans('file.Order Details')}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div> 
    <!--Breadcrumb Area ends-->
    <!--My account Dashboard starts-->
    <section class="my-account-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="user-sidebar-menu">
                        @include('ecommerce::frontend.customer-menu')
                    </div>
                </div>
                <div class="col-md-9 tabs style1">
                    <div class="row">
                        <div class="col-md-12">
                            Order Date: <strong>{{ date('d-m-Y', strtotime($sale->created_at)) }}</strong> | Ref. No.: <strong>{{ $sale->reference_no }}</strong>
                            <hr class="mt-3 mb-3">
                            <div class="cart-table table-content table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($sale))
                                        @foreach($products as $product)
                                        <tr class="sale-{{$sale->id}}">
                                            <td>
                                                {!! $product->name !!}
                                            </td>
                                            <td>
                                                {{ $product->qty }}
                                            </td>
                                            <td>
                                                {{ $product->net_unit_price }} 
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                    <tfooter>
                                        <tr>
                                            <th></th>
                                            <th>Shipping</th>
                                            <th>(+) {{$currency->symbol ?? $currency->code}}{{ $sale->shipping_cost ?? '0' }}</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>Discount</th>
                                            <th>(-) {{$currency->symbol ?? $currency->code}}{{ $sale->coupon_discount ?? '0' }}</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>Total</th>
                                            <th>{{$currency->symbol ?? $currency->code}}{{ ($sale->grand_total - $sale->coupon_discount) }}</th>
                                        </tr>
                                    </tfooter>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!--My account Dashboard ends-->
@endsection

@section('script')

@endsection