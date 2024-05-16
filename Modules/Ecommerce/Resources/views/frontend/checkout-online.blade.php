@php

$all_categories = $categories_list;

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
    <title>Checkout</title>
    <meta name="description" content="" />
    <meta name="author" content="LionCoders" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Links -->
    <link rel="icon" type="image/ico" href="{{ url('frontend/images') }}/{{$ecommerce_setting->favicon ?? ''}}" />
    <!-- google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <!-- Plugins CSS -->
    <link href="{{ asset('public/frontend/css/plugins.css') }}" rel="stylesheet" />
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/frontend/css/bootstrap-select.min.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/frontend/css/bootstrap-select.min.css') }}">
    </noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    </noscript>
    <!-- style CSS -->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/frontend/css/style.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/frontend/css/style.css') }}">
    </noscript>

    @if(isset($ecommerce_setting->custom_css))
    <style>
    {{$ecommerce_setting->custom_css}}
    </style>
    @endif
    <style>
        :root {
            --theme-color: #0071df;
        }

        h4,
        h5,
        h6 {
            font-weight: 500
        }

        .res-box {
            background: #f5f6f7;
            padding: 20px
        }

        .cus-details label {
            position: absolute;
            background-color: #fff;
            font-size: 12px;
            padding: 0 5px;
            left: 25px;
            top: -8px;
            z-index: 1;
        }

        .cart-header {
            background-color: #f5f6f7;
            color: #212222;
            font-weight: 700;
            margin-bottom: 15px;
            padding: 10px 5px;
        }

        .cart-header p {
            font-weight: 500;
        }

        .cart-item {
            margin-bottom: 15px;
        }

        .cart-item p,
        .cost-details p {
            color: #212222
        }

        .cart-item span {
            font-size: 12px;
        }

        .cart-item-img img {
            border-radius: 5px;
            max-width: 90px;
            padding: .5rem;
        }

        @media all and (min-width: 992px) {
            .cus-details {
                padding-right: 60px;
            }

            .cart {
                padding-left: 60px;
                border-left: 1px solid #666
            }
        }
    </style>
</head>

<body>

    <!-- Content Wrapper -->
    <section class="content-wrapper pt-0">
        <div class="container">
            <div class="col-md-6 offset-md-3">
                <div class="logo">
                    <a href="{{url('/')}}">
                        @if(isset($ecommerce_setting->logo))
                        <img src="{{ url('frontend/images/') }}/{{$ecommerce_setting->logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                        @else
                        <img src="{{ asset('public/logo') }}/{{$general_setting->site_logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                        @endif
                    </a>
                </div>
                @if($mode == 'Stripe')
                <form id="payment-form" action="{{ url('/stripe-payment') }}" class="d-block validation mt-5" method="post" data-cc-on-file="false" data-stripe-publishable-key="{{$publishable_key}}">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-2">
                            <strong>Card Details</strong>
                        </div>

                        <div class="col-6">
                            <input type="text" name="card-num" autocomplete="off" required class="form-control mt-0" placeholder="{{__('Card Number *')}}">
                        </div>
                        <div class="col-2">
                            <input type="text" name="card-cvc" required class="form-control mt-0" placeholder="{{__('CVC *')}}">
                        </div>
                        <div class="col-2">
                            <input type="text" name="card-expiry-month" required class="form-control mt-0" placeholder="{{__('MM *')}}">
                        </div>
                        <div class="col-2">
                            <input type="text" name="card-expiry-year" required class="form-control mt-0" placeholder="{{__('YYYY *')}}">
                        </div>

                        <div class="col-md-12">
                            <input type="hidden" name="grand_total" value="{{$grand_total}}" />
                            <input type="hidden" name="sale_id" value="{{$sale_id}}" />
                            <input type="hidden" name="payment_id" value="{{$payment_id}}" />
                            <button id="go" type="submit" class="button style1 d-block w-100 mt-3 place-order">{{trans('file.Pay')}} {{$currency->symbol}}{{$grand_total}}</button>
                        </div>

                    </div>
                </form>
                @endif

                @if($mode == 'PayPal')
                <div id="paypal-button-container" class="payment-form mt-5"></div>
                <input type="hidden" name="grand_total" value="{{$grand_total}}" />
                <input type="hidden" name="payment_id" value="{{$payment_id}}" />
                @endif
            </div>
        </div>
    </section>
    <!-- Content Wrapper Ends -->


    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    @if($mode == 'Stripe')
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript">
        "use strict";

        $(function() {
            var $form = $(".validation");
            $('form.validation').bind('submit', function(e) {

                $('#go').prop('disabled', true).text('processing your payment...');

                var $form = $(".validation"),
                    inputVal = ['input[type=email]', 'input[type=password]',
                        'input[type=text]', 'input[type=file]',
                        'textarea'
                    ].join(', '),
                    $inputs = $form.find('.required').find(inputVal),
                    $errorStatus = $form.find('div.error'),
                    valid = true;
                $errorStatus.addClass('hide');

                $('.has-error').removeClass('has-error');
                $inputs.each(function(i, el) {
                    var $input = $(el);
                    if ($input.val() === '') {
                        $input.parent().addClass('has-error');
                        $errorStatus.removeClass('hide');
                        e.preventDefault();
                    }
                });

                if (!$form.data('cc-on-file')) {
                    e.preventDefault();
                    Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                    Stripe.createToken({
                        number: $('input[name=card-num]').val(),
                        cvc: $('input[name=card-cvc]').val(),
                        exp_month: $('input[name=card-expiry-month]').val(),
                        exp_year: $('input[name=card-expiry-year]').val()
                    }, stripeHandleResponse);
                }

            });

            function stripeHandleResponse(status, response) {
                if (response.error) {
                    $('.error')
                        .removeClass('hide')
                        .find('.alert')
                        .text('Sorry, we couldn\'t process the payment due to a technical issue!');
                    console.log(response.error.message);
                    $('#go').prop('disabled', false).text('Lets Try Again');
                } else {
                    var token = response['id'];
                    $form.find('input[type=text]').empty();
                    $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                    $form.get(0).submit();
                }
            }

        });
    </script>
    @endif

    @if($mode == 'PayPal')
    <script src="https://www.paypal.com/sdk/js?client-id={{$client_id}}" data-namespace="paypal_sdk"></script>
    <script type="text/javascript">
        let targetURL = "{{ url('/paypal-payment')}}";
        let cancelURL = "{{ url('payment/paypal/pay/cancel')}}";
        let redirectURL = "{{ url('order/success')}}";
        let redirectURLAfterCancel = "{{ url('/checkout')}}";
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        // Render the PayPal button into #paypal-button-container
        paypal_sdk.Buttons({
            // Call your server to set up the transaction
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: $('input[name="grand_total"]').val(),
                            currency_code: "{{$currency->code}}",
                        }
                    }]
                });
            },

            // Call your server to finalize the transaction
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    if (details.status == "COMPLETED") {
                        var payment_id  = $('input[name="grand_total"]').val();
                        $.post({
                            url: targetURL,
                            data: {payment_id: payment_id, transaction_id: details.id},
                            success: function(response) {
                                console.log(response);
                                if (response.errors) {
                                    let html = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
													${response.errors}
													<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
												</div>`;
                                    $('#errorMessage').html(html);
                                } else if (response.success) {
                                    window.location.href = redirectURL+'/'+response.sale_reference;
                                }
                            }
                        });
                    }
                });
            }
        }).render('#paypal-button-container');
    </script>       
    @endif

</body>

</html>