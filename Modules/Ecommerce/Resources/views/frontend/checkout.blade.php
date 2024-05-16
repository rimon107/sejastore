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

    <!-- google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <!-- Plugins CSS -->
    @if(!config('database.connections.saleprosaas_landlord'))
    <!-- Links -->
    <link rel="icon" type="image/ico" href="{{ url('frontend/images') }}/{{$ecommerce_setting->favicon ?? ''}}" />
    <!-- Plugins CSS -->
    <link href="{{ asset('public/frontend/css/plugins.css') }}" rel="stylesheet" />
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    </noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/frontend/css/bootstrap-select.min.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/frontend/css/bootstrap-select.min.css') }}">
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
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('../../public/frontend/css/bootstrap-select.min.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('../../public/frontend/css/bootstrap-select.min.css') }}">
    </noscript>
    @endif


    @if(isset($ecommerce_setting->custom_css))
    <style>
    {{$ecommerce_setting->custom_css}}
    </style>
    @endif
    <style>
        :root {
            --theme-color: #fa9928;
        }

        /* Minified style.css */
        {!! str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', preg_replace('/\s*([{}|:;,])\s+/', '$1', file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/css/style.css"))) !!}

        h4,
        h5,
        h6 {
            font-weight: 500
        }

        .logo img {
            max-height: 50px;
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
            color: #212222;
            font-weight: 500
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

    <div class="alert alert-dismissible text-center fade" role="alert">
        <button type="button" class="close"><span class="material-symbols-outlined">close</span></button>
        <div class="message">
        </div>
    </div>

    <!-- Content Wrapper -->
    <section class="content-wrapper pt-0">
        <div class="container ">
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
            @php
                $stripe = $gateways->where('name','Stripe')->first();
                if(isset($stripe)){
                    $stripe_details = $stripe->details;
                    $details_array = explode(';',$stripe_details);
                    $publishable_key = explode(',', $details_array[1])[1];
                }
            @endphp
            <form id="payment-form" action="{{ url('/place-order') }}" class="d-block validation" method="post" @if(isset($stripe)) data-cc-on-file="false" data-stripe-publishable-key="{{$publishable_key}}" @endif>
                @csrf
                <div class="row">
                    <div class="col-md-7 mt-3 cus-details">
                        @guest
                        <div class="text-center res-box mb-3">
                            <span>{{trans('file.Returning customer?')}} </span><a href="#" data-toggle="modal" data-target="#loginModal" class="bold">{{trans('file.Click here to login')}}</a>
                        </div>
                        @endguest
                        <h5>{{trans('file.Shipping Details')}}</h5>

                        @if(auth()->user() && auth()->user()->role_id == 5)
                        @if(isset($def_address->address))
                        <div class="res-box mb-3 default_address">
                            <strong>{{trans('file.Default Address')}}</strong>
                            <p>{{ $def_address->address }}</p>
                            <p>{{ $def_address->city }}@if(isset($def_address->state)), {{ $def_address->state }}@endif @if(isset($def_address->country)), {{ $def_address->country }}@endif @if(isset($def_address->zip)), {{ $def_address->zip }}@endif</p>
                            <p>{{ $def_address->name ?? $customer->name ?? ''}}</p>
                            <p>{{ $def_address->email ?? $customer->email ?? ''}}</p>
                            <p>{{ $def_address->phone ?? $customer->phone ?? ''}}</p>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label>{{trans('file.Address')}} *</label>
                                <select class=" form-control" name="address_list" id="address_list">
                                    @foreach($addresses as $address)
                                    <option value="{{$address->id}}">{{$address->address}}, {{$address->city}}, @if(isset($address->state)){{$address->state}},@endif {{$address->country}}</option>
                                    @endforeach
                                    <option value="0">{{trans('file.Add a different address')}}</option>
                                </select>
                                <input type="hidden" name="default_address" value="@if(isset($def_address->address)){{ $def_address->id }}@endif" />
                            </div>
                        </div>
                        @endif

                        <div class="row shipping_details @if(auth()->user() && auth()->user()->role_id == 5) d-none @endif">
                            <div class="col-12 mb-3 mt-2">
                                <label>{{trans('file.name')}} *</label>
                                <input class="form-control" type="text" name="shipping_name" id="name" value="{{ $customer->name ?? $def_address->name ?? ''}}" required>
                            </div>
                            <div class="col-md-4 mb-3 mt-2">
                                <label>{{trans('file.Phone')}}</label>
                                <input class="form-control" type="number" name="shipping_phone" id="phone" value="{{ $def_address->phone ?? $customer->phone_number ?? ''}}" minlength="11">
                            </div>
                            <div class="col-md-8 mb-3 mt-2">
                                <label>{{trans('file.email')}}</label>
                                <input class="form-control" type="email" name="shipping_email" id="email" value="{{ $def_address->email ?? $customer->email ?? ''}}" required>
                            </div>
                            <div class="col-12 mb-3 mt-2">
                                <label>{{trans('file.Address')}} *</label>
                                <input class="form-control" type="text" name="shipping_address" id="address" value="{{ $def_address->address ?? ''}}" required>
                            </div>

                            <div class="col-md-4 mb-3 mt-2">
                                <label>{{trans('file.City')}} *</label>
                                <input class="form-control" type="text" name="shipping_city" id="city" value="{{ $def_address->city ?? ''}}" required>
                            </div>
                            <div class="col-md-4 mb-3 mt-2">
                                <label>{{trans('file.State')}}</label>
                                <input class="form-control" type="text" name="shipping_state" id="state" value="{{ $def_address->state ?? ''}}">
                            </div>
                            <div class="col-md-4 mb-3 mt-2">
                                <label>{{trans('file.Zip / Post Code')}}</label>
                                <input class="form-control" type="text" name="shipping_zip" id="zip" value="{{ $def_address->zip ?? ''}}">
                            </div>
                            <div class="col-md-12 mb-3 mt-2">
                                <label>{{trans('file.Country')}} *</label>
                                <select class="form-control" name="shipping_country" id="country" required>
                                    <option value="">Select country...</option>
                                    <option value="AF">Afghanistan</option>
                                    <option value="AX">Åland Islands</option>
                                    <option value="AL">Albania</option>
                                    <option value="DZ">Algeria</option>
                                    <option value="AS">American Samoa</option>
                                    <option value="AD">Andorra</option>
                                    <option value="AO">Angola</option>
                                    <option value="AI">Anguilla</option>
                                    <option value="AQ">Antarctica</option>
                                    <option value="AG">Antigua and Barbuda</option>
                                    <option value="AR">Argentina</option>
                                    <option value="AM">Armenia</option>
                                    <option value="AW">Aruba</option>
                                    <option value="AU">Australia</option>
                                    <option value="AT">Austria</option>
                                    <option value="AZ">Azerbaijan</option>
                                    <option value="BS">Bahamas</option>
                                    <option value="BH">Bahrain</option>
                                    <option value="BD" selected="">Bangladesh</option>
                                    <option value="BB">Barbados</option>
                                    <option value="BY">Belarus</option>
                                    <option value="BE">Belgium</option>
                                    <option value="PW">Belau</option>
                                    <option value="BZ">Belize</option>
                                    <option value="BJ">Benin</option>
                                    <option value="BM">Bermuda</option>
                                    <option value="BT">Bhutan</option>
                                    <option value="BO">Bolivia</option>
                                    <option value="BQ">Bonaire, Saint Eustatius and Saba</option>
                                    <option value="BA">Bosnia and Herzegovina</option>
                                    <option value="BW">Botswana</option>
                                    <option value="BV">Bouvet Island</option>
                                    <option value="BR">Brazil</option>
                                    <option value="IO">British Indian Ocean Territory</option>
                                    <option value="BN">Brunei</option>
                                    <option value="BG">Bulgaria</option>
                                    <option value="BF">Burkina Faso</option>
                                    <option value="BI">Burundi</option>
                                    <option value="KH">Cambodia</option>
                                    <option value="CM">Cameroon</option>
                                    <option value="CA">Canada</option>
                                    <option value="CV">Cape Verde</option>
                                    <option value="KY">Cayman Islands</option>
                                    <option value="CF">Central African Republic</option>
                                    <option value="TD">Chad</option>
                                    <option value="CL">Chile</option>
                                    <option value="CN">China</option>
                                    <option value="CX">Christmas Island</option>
                                    <option value="CC">Cocos (Keeling) Islands</option>
                                    <option value="CO">Colombia</option>
                                    <option value="KM">Comoros</option>
                                    <option value="CG">Congo (Brazzaville)</option>
                                    <option value="CD">Congo (Kinshasa)</option>
                                    <option value="CK">Cook Islands</option>
                                    <option value="CR">Costa Rica</option>
                                    <option value="HR">Croatia</option>
                                    <option value="CU">Cuba</option>
                                    <option value="CW">Cura&amp;ccedil;ao</option>
                                    <option value="CY">Cyprus</option>
                                    <option value="CZ">Czech Republic</option>
                                    <option value="DK">Denmark</option>
                                    <option value="DJ">Djibouti</option>
                                    <option value="DM">Dominica</option>
                                    <option value="DO">Dominican Republic</option>
                                    <option value="EC">Ecuador</option>
                                    <option value="EG">Egypt</option>
                                    <option value="SV">El Salvador</option>
                                    <option value="GQ">Equatorial Guinea</option>
                                    <option value="ER">Eritrea</option>
                                    <option value="EE">Estonia</option>
                                    <option value="ET">Ethiopia</option>
                                    <option value="FK">Falkland Islands</option>
                                    <option value="FO">Faroe Islands</option>
                                    <option value="FJ">Fiji</option>
                                    <option value="FI">Finland</option>
                                    <option value="FR">France</option>
                                    <option value="GF">French Guiana</option>
                                    <option value="PF">French Polynesia</option>
                                    <option value="TF">French Southern Territories</option>
                                    <option value="GA">Gabon</option>
                                    <option value="GM">Gambia</option>
                                    <option value="GE">Georgia</option>
                                    <option value="DE">Germany</option>
                                    <option value="GH">Ghana</option>
                                    <option value="GI">Gibraltar</option>
                                    <option value="GR">Greece</option>
                                    <option value="GL">Greenland</option>
                                    <option value="GD">Grenada</option>
                                    <option value="GP">Guadeloupe</option>
                                    <option value="GU">Guam</option>
                                    <option value="GT">Guatemala</option>
                                    <option value="GG">Guernsey</option>
                                    <option value="GN">Guinea</option>
                                    <option value="GW">Guinea-Bissau</option>
                                    <option value="GY">Guyana</option>
                                    <option value="HT">Haiti</option>
                                    <option value="HM">Heard Island and McDonald Islands</option>
                                    <option value="HN">Honduras</option>
                                    <option value="HK">Hong Kong</option>
                                    <option value="HU">Hungary</option>
                                    <option value="IS">Iceland</option>
                                    <option value="IN">India</option>
                                    <option value="ID">Indonesia</option>
                                    <option value="IR">Iran</option>
                                    <option value="IQ">Iraq</option>
                                    <option value="IE">Ireland</option>
                                    <option value="IM">Isle of Man</option>
                                    <option value="IL">Israel</option>
                                    <option value="IT">Italy</option>
                                    <option value="CI">Ivory Coast</option>
                                    <option value="JM">Jamaica</option>
                                    <option value="JP">Japan</option>
                                    <option value="JE">Jersey</option>
                                    <option value="JO">Jordan</option>
                                    <option value="KZ">Kazakhstan</option>
                                    <option value="KE">Kenya</option>
                                    <option value="KI">Kiribati</option>
                                    <option value="KW">Kuwait</option>
                                    <option value="XK">Kosovo</option>
                                    <option value="KG">Kyrgyzstan</option>
                                    <option value="LA">Laos</option>
                                    <option value="LV">Latvia</option>
                                    <option value="LB">Lebanon</option>
                                    <option value="LS">Lesotho</option>
                                    <option value="LR">Liberia</option>
                                    <option value="LY">Libya</option>
                                    <option value="LI">Liechtenstein</option>
                                    <option value="LT">Lithuania</option>
                                    <option value="LU">Luxembourg</option>
                                    <option value="MO">Macao</option>
                                    <option value="MK">North Macedonia</option>
                                    <option value="MG">Madagascar</option>
                                    <option value="MW">Malawi</option>
                                    <option value="MY">Malaysia</option>
                                    <option value="MV">Maldives</option>
                                    <option value="ML">Mali</option>
                                    <option value="MT">Malta</option>
                                    <option value="MH">Marshall Islands</option>
                                    <option value="MQ">Martinique</option>
                                    <option value="MR">Mauritania</option>
                                    <option value="MU">Mauritius</option>
                                    <option value="YT">Mayotte</option>
                                    <option value="MX">Mexico</option>
                                    <option value="FM">Micronesia</option>
                                    <option value="MD">Moldova</option>
                                    <option value="MC">Monaco</option>
                                    <option value="MN">Mongolia</option>
                                    <option value="ME">Montenegro</option>
                                    <option value="MS">Montserrat</option>
                                    <option value="MA">Morocco</option>
                                    <option value="MZ">Mozambique</option>
                                    <option value="MM">Myanmar</option>
                                    <option value="NA">Namibia</option>
                                    <option value="NR">Nauru</option>
                                    <option value="NP">Nepal</option>
                                    <option value="NL">Netherlands</option>
                                    <option value="NC">New Caledonia</option>
                                    <option value="NZ">New Zealand</option>
                                    <option value="NI">Nicaragua</option>
                                    <option value="NE">Niger</option>
                                    <option value="NG">Nigeria</option>
                                    <option value="NU">Niue</option>
                                    <option value="NF">Norfolk Island</option>
                                    <option value="MP">Northern Mariana Islands</option>
                                    <option value="KP">North Korea</option>
                                    <option value="NO">Norway</option>
                                    <option value="OM">Oman</option>
                                    <option value="PK">Pakistan</option>
                                    <option value="PS">Palestinian Territory</option>
                                    <option value="PA">Panama</option>
                                    <option value="PG">Papua New Guinea</option>
                                    <option value="PY">Paraguay</option>
                                    <option value="PE">Peru</option>
                                    <option value="PH">Philippines</option>
                                    <option value="PN">Pitcairn</option>
                                    <option value="PL">Poland</option>
                                    <option value="PT">Portugal</option>
                                    <option value="PR">Puerto Rico</option>
                                    <option value="QA">Qatar</option>
                                    <option value="RE">Reunion</option>
                                    <option value="RO">Romania</option>
                                    <option value="RU">Russia</option>
                                    <option value="RW">Rwanda</option>
                                    <option value="BL">Saint Barth&amp;eacute;lemy</option>
                                    <option value="SH">Saint Helena</option>
                                    <option value="KN">Saint Kitts and Nevis</option>
                                    <option value="LC">Saint Lucia</option>
                                    <option value="MF">Saint Martin (French part)</option>
                                    <option value="SX">Saint Martin (Dutch part)</option>
                                    <option value="PM">Saint Pierre and Miquelon</option>
                                    <option value="VC">Saint Vincent and the Grenadines</option>
                                    <option value="SM">San Marino</option>
                                    <option value="ST">S&amp;atilde;o Tom&amp;eacute; and Pr&amp;iacute;ncipe</option>
                                    <option value="SA">Saudi Arabia</option>
                                    <option value="SN">Senegal</option>
                                    <option value="RS">Serbia</option>
                                    <option value="SC">Seychelles</option>
                                    <option value="SL">Sierra Leone</option>
                                    <option value="SG">Singapore</option>
                                    <option value="SK">Slovakia</option>
                                    <option value="SI">Slovenia</option>
                                    <option value="SB">Solomon Islands</option>
                                    <option value="SO">Somalia</option>
                                    <option value="ZA">South Africa</option>
                                    <option value="GS">South Georgia/Sandwich Islands</option>
                                    <option value="KR">South Korea</option>
                                    <option value="SS">South Sudan</option>
                                    <option value="ES">Spain</option>
                                    <option value="LK">Sri Lanka</option>
                                    <option value="SD">Sudan</option>
                                    <option value="SR">Suriname</option>
                                    <option value="SJ">Svalbard and Jan Mayen</option>
                                    <option value="SZ">Swaziland</option>
                                    <option value="SE">Sweden</option>
                                    <option value="CH">Switzerland</option>
                                    <option value="SY">Syria</option>
                                    <option value="TW">Taiwan</option>
                                    <option value="TJ">Tajikistan</option>
                                    <option value="TZ">Tanzania</option>
                                    <option value="TH">Thailand</option>
                                    <option value="TL">Timor-Leste</option>
                                    <option value="TG">Togo</option>
                                    <option value="TK">Tokelau</option>
                                    <option value="TO">Tonga</option>
                                    <option value="TT">Trinidad and Tobago</option>
                                    <option value="TN">Tunisia</option>
                                    <option value="TR">Turkey</option>
                                    <option value="TM">Turkmenistan</option>
                                    <option value="TC">Turks and Caicos Islands</option>
                                    <option value="TV">Tuvalu</option>
                                    <option value="UG">Uganda</option>
                                    <option value="UA">Ukraine</option>
                                    <option value="AE">United Arab Emirates</option>
                                    <option value="GB">United Kingdom (UK)</option>
                                    <option value="US">United States (US)</option>
                                    <option value="UM">United States (US) Minor Outlying Islands</option>
                                    <option value="UY">Uruguay</option>
                                    <option value="UZ">Uzbekistan</option>
                                    <option value="VU">Vanuatu</option>
                                    <option value="VA">Vatican</option>
                                    <option value="VE">Venezuela</option>
                                    <option value="VN">Vietnam</option>
                                    <option value="VG">Virgin Islands (British)</option>
                                    <option value="VI">Virgin Islands (US)</option>
                                    <option value="WF">Wallis and Futuna</option>
                                    <option value="EH">Western Sahara</option>
                                    <option value="WS">Samoa</option>
                                    <option value="YE">Yemen</option>
                                    <option value="ZM">Zambia</option>
                                    <option value="ZW">Zimbabwe</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mt-3">
                                <label>{{trans('file.Order Notes')}}</label>
                                <textarea class="form-control mar-top-20" name="sale_note" id="sale_note"></textarea>
                            </div>
                        </div>
                        <div class="row billing_details">
                            <div class="col-12 mb-3 mt-3">
                                <div class="custom-control custom-checkbox mt-3">
                                    <input type="checkbox" class="custom-control-input" id="different_billing" checked>
                                    <label class="custom-control-label" for="different_billing">{{trans('file.Billing address same as Shipping Address')}}</label>
                                </div>
                                <div class="collapse multi-collapse" id="billingAddress">
                                    <div class="row mt-3">
                                        <div class="col-12 mb-3 mt-2">
                                            <label>{{trans('file.name')}} *</label>
                                            <input class="form-control" type="text" name="billing_name" id="billing_name" value="{{ $def_address->name ?? $customer->name ?? ''}}" required>
                                        </div>
                                        <div class="col-md-4 mb-3 mt-2">
                                            <label>{{trans('file.Phone')}}</label>
                                            <input class="form-control" type="number" name="billing_phone" id="billing_phone" value="{{ $def_address->phone ?? $customer->phone_number ?? ''}}">
                                        </div>
                                        <div class="col-md-8 mb-3 mt-2">
                                            <label>{{trans('file.email')}}</label>
                                            <input class="form-control" type="email" name="billing_email" id="billing_email" value="{{ $def_address->email ?? $customer->email ?? ''}}" required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label>{{trans('file.Address')}} *</label>
                                            <input class="form-control" type="text" name="billing_address" id="billing_address" value="{{ $def_address->address ?? ''}}" required>
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label>{{trans('file.City')}} *</label>
                                            <input class="form-control" type="text" name="billing_city" id="billing_city" value="{{ $def_address->city ?? ''}}" required>
                                        </div>

                                        <div class="col-4 mb-3">
                                            <label>{{trans('file.State')}}</label>
                                            <input class="form-control" type="text" name="billing_state" id="billing_state" value="{{ $def_address->state ?? ''}}">
                                        </div>

                                        <div class="col-md-4 mb-3 mt-2">
                                            <label>{{trans('file.Zip / Post Code')}}</label>
                                            <input class="form-control" type="text" name="billing_zip" id="billing_zip" value="{{ $def_address->zip ?? ''}}">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label>{{trans('file.Country')}} *</label>
                                            <select class="form-control" name="billing_country" id="billing_country" required>
                                                <option value="">Select country...</option>
                                                <option value="AF">Afghanistan</option>
                                                <option value="AX">Åland Islands</option>
                                                <option value="AL">Albania</option>
                                                <option value="DZ">Algeria</option>
                                                <option value="AS">American Samoa</option>
                                                <option value="AD">Andorra</option>
                                                <option value="AO">Angola</option>
                                                <option value="AI">Anguilla</option>
                                                <option value="AQ">Antarctica</option>
                                                <option value="AG">Antigua and Barbuda</option>
                                                <option value="AR">Argentina</option>
                                                <option value="AM">Armenia</option>
                                                <option value="AW">Aruba</option>
                                                <option value="AU">Australia</option>
                                                <option value="AT">Austria</option>
                                                <option value="AZ">Azerbaijan</option>
                                                <option value="BS">Bahamas</option>
                                                <option value="BH">Bahrain</option>
                                                <option value="BD" selected="">Bangladesh</option>
                                                <option value="BB">Barbados</option>
                                                <option value="BY">Belarus</option>
                                                <option value="BE">Belgium</option>
                                                <option value="PW">Belau</option>
                                                <option value="BZ">Belize</option>
                                                <option value="BJ">Benin</option>
                                                <option value="BM">Bermuda</option>
                                                <option value="BT">Bhutan</option>
                                                <option value="BO">Bolivia</option>
                                                <option value="BQ">Bonaire, Saint Eustatius and Saba</option>
                                                <option value="BA">Bosnia and Herzegovina</option>
                                                <option value="BW">Botswana</option>
                                                <option value="BV">Bouvet Island</option>
                                                <option value="BR">Brazil</option>
                                                <option value="IO">British Indian Ocean Territory</option>
                                                <option value="BN">Brunei</option>
                                                <option value="BG">Bulgaria</option>
                                                <option value="BF">Burkina Faso</option>
                                                <option value="BI">Burundi</option>
                                                <option value="KH">Cambodia</option>
                                                <option value="CM">Cameroon</option>
                                                <option value="CA">Canada</option>
                                                <option value="CV">Cape Verde</option>
                                                <option value="KY">Cayman Islands</option>
                                                <option value="CF">Central African Republic</option>
                                                <option value="TD">Chad</option>
                                                <option value="CL">Chile</option>
                                                <option value="CN">China</option>
                                                <option value="CX">Christmas Island</option>
                                                <option value="CC">Cocos (Keeling) Islands</option>
                                                <option value="CO">Colombia</option>
                                                <option value="KM">Comoros</option>
                                                <option value="CG">Congo (Brazzaville)</option>
                                                <option value="CD">Congo (Kinshasa)</option>
                                                <option value="CK">Cook Islands</option>
                                                <option value="CR">Costa Rica</option>
                                                <option value="HR">Croatia</option>
                                                <option value="CU">Cuba</option>
                                                <option value="CW">Cura&amp;ccedil;ao</option>
                                                <option value="CY">Cyprus</option>
                                                <option value="CZ">Czech Republic</option>
                                                <option value="DK">Denmark</option>
                                                <option value="DJ">Djibouti</option>
                                                <option value="DM">Dominica</option>
                                                <option value="DO">Dominican Republic</option>
                                                <option value="EC">Ecuador</option>
                                                <option value="EG">Egypt</option>
                                                <option value="SV">El Salvador</option>
                                                <option value="GQ">Equatorial Guinea</option>
                                                <option value="ER">Eritrea</option>
                                                <option value="EE">Estonia</option>
                                                <option value="ET">Ethiopia</option>
                                                <option value="FK">Falkland Islands</option>
                                                <option value="FO">Faroe Islands</option>
                                                <option value="FJ">Fiji</option>
                                                <option value="FI">Finland</option>
                                                <option value="FR">France</option>
                                                <option value="GF">French Guiana</option>
                                                <option value="PF">French Polynesia</option>
                                                <option value="TF">French Southern Territories</option>
                                                <option value="GA">Gabon</option>
                                                <option value="GM">Gambia</option>
                                                <option value="GE">Georgia</option>
                                                <option value="DE">Germany</option>
                                                <option value="GH">Ghana</option>
                                                <option value="GI">Gibraltar</option>
                                                <option value="GR">Greece</option>
                                                <option value="GL">Greenland</option>
                                                <option value="GD">Grenada</option>
                                                <option value="GP">Guadeloupe</option>
                                                <option value="GU">Guam</option>
                                                <option value="GT">Guatemala</option>
                                                <option value="GG">Guernsey</option>
                                                <option value="GN">Guinea</option>
                                                <option value="GW">Guinea-Bissau</option>
                                                <option value="GY">Guyana</option>
                                                <option value="HT">Haiti</option>
                                                <option value="HM">Heard Island and McDonald Islands</option>
                                                <option value="HN">Honduras</option>
                                                <option value="HK">Hong Kong</option>
                                                <option value="HU">Hungary</option>
                                                <option value="IS">Iceland</option>
                                                <option value="IN">India</option>
                                                <option value="ID">Indonesia</option>
                                                <option value="IR">Iran</option>
                                                <option value="IQ">Iraq</option>
                                                <option value="IE">Ireland</option>
                                                <option value="IM">Isle of Man</option>
                                                <option value="IL">Israel</option>
                                                <option value="IT">Italy</option>
                                                <option value="CI">Ivory Coast</option>
                                                <option value="JM">Jamaica</option>
                                                <option value="JP">Japan</option>
                                                <option value="JE">Jersey</option>
                                                <option value="JO">Jordan</option>
                                                <option value="KZ">Kazakhstan</option>
                                                <option value="KE">Kenya</option>
                                                <option value="KI">Kiribati</option>
                                                <option value="KW">Kuwait</option>
                                                <option value="XK">Kosovo</option>
                                                <option value="KG">Kyrgyzstan</option>
                                                <option value="LA">Laos</option>
                                                <option value="LV">Latvia</option>
                                                <option value="LB">Lebanon</option>
                                                <option value="LS">Lesotho</option>
                                                <option value="LR">Liberia</option>
                                                <option value="LY">Libya</option>
                                                <option value="LI">Liechtenstein</option>
                                                <option value="LT">Lithuania</option>
                                                <option value="LU">Luxembourg</option>
                                                <option value="MO">Macao</option>
                                                <option value="MK">North Macedonia</option>
                                                <option value="MG">Madagascar</option>
                                                <option value="MW">Malawi</option>
                                                <option value="MY">Malaysia</option>
                                                <option value="MV">Maldives</option>
                                                <option value="ML">Mali</option>
                                                <option value="MT">Malta</option>
                                                <option value="MH">Marshall Islands</option>
                                                <option value="MQ">Martinique</option>
                                                <option value="MR">Mauritania</option>
                                                <option value="MU">Mauritius</option>
                                                <option value="YT">Mayotte</option>
                                                <option value="MX">Mexico</option>
                                                <option value="FM">Micronesia</option>
                                                <option value="MD">Moldova</option>
                                                <option value="MC">Monaco</option>
                                                <option value="MN">Mongolia</option>
                                                <option value="ME">Montenegro</option>
                                                <option value="MS">Montserrat</option>
                                                <option value="MA">Morocco</option>
                                                <option value="MZ">Mozambique</option>
                                                <option value="MM">Myanmar</option>
                                                <option value="NA">Namibia</option>
                                                <option value="NR">Nauru</option>
                                                <option value="NP">Nepal</option>
                                                <option value="NL">Netherlands</option>
                                                <option value="NC">New Caledonia</option>
                                                <option value="NZ">New Zealand</option>
                                                <option value="NI">Nicaragua</option>
                                                <option value="NE">Niger</option>
                                                <option value="NG">Nigeria</option>
                                                <option value="NU">Niue</option>
                                                <option value="NF">Norfolk Island</option>
                                                <option value="MP">Northern Mariana Islands</option>
                                                <option value="KP">North Korea</option>
                                                <option value="NO">Norway</option>
                                                <option value="OM">Oman</option>
                                                <option value="PK">Pakistan</option>
                                                <option value="PS">Palestinian Territory</option>
                                                <option value="PA">Panama</option>
                                                <option value="PG">Papua New Guinea</option>
                                                <option value="PY">Paraguay</option>
                                                <option value="PE">Peru</option>
                                                <option value="PH">Philippines</option>
                                                <option value="PN">Pitcairn</option>
                                                <option value="PL">Poland</option>
                                                <option value="PT">Portugal</option>
                                                <option value="PR">Puerto Rico</option>
                                                <option value="QA">Qatar</option>
                                                <option value="RE">Reunion</option>
                                                <option value="RO">Romania</option>
                                                <option value="RU">Russia</option>
                                                <option value="RW">Rwanda</option>
                                                <option value="BL">Saint Barth&amp;eacute;lemy</option>
                                                <option value="SH">Saint Helena</option>
                                                <option value="KN">Saint Kitts and Nevis</option>
                                                <option value="LC">Saint Lucia</option>
                                                <option value="MF">Saint Martin (French part)</option>
                                                <option value="SX">Saint Martin (Dutch part)</option>
                                                <option value="PM">Saint Pierre and Miquelon</option>
                                                <option value="VC">Saint Vincent and the Grenadines</option>
                                                <option value="SM">San Marino</option>
                                                <option value="ST">S&amp;atilde;o Tom&amp;eacute; and Pr&amp;iacute;ncipe</option>
                                                <option value="SA">Saudi Arabia</option>
                                                <option value="SN">Senegal</option>
                                                <option value="RS">Serbia</option>
                                                <option value="SC">Seychelles</option>
                                                <option value="SL">Sierra Leone</option>
                                                <option value="SG">Singapore</option>
                                                <option value="SK">Slovakia</option>
                                                <option value="SI">Slovenia</option>
                                                <option value="SB">Solomon Islands</option>
                                                <option value="SO">Somalia</option>
                                                <option value="ZA">South Africa</option>
                                                <option value="GS">South Georgia/Sandwich Islands</option>
                                                <option value="KR">South Korea</option>
                                                <option value="SS">South Sudan</option>
                                                <option value="ES">Spain</option>
                                                <option value="LK">Sri Lanka</option>
                                                <option value="SD">Sudan</option>
                                                <option value="SR">Suriname</option>
                                                <option value="SJ">Svalbard and Jan Mayen</option>
                                                <option value="SZ">Swaziland</option>
                                                <option value="SE">Sweden</option>
                                                <option value="CH">Switzerland</option>
                                                <option value="SY">Syria</option>
                                                <option value="TW">Taiwan</option>
                                                <option value="TJ">Tajikistan</option>
                                                <option value="TZ">Tanzania</option>
                                                <option value="TH">Thailand</option>
                                                <option value="TL">Timor-Leste</option>
                                                <option value="TG">Togo</option>
                                                <option value="TK">Tokelau</option>
                                                <option value="TO">Tonga</option>
                                                <option value="TT">Trinidad and Tobago</option>
                                                <option value="TN">Tunisia</option>
                                                <option value="TR">Turkey</option>
                                                <option value="TM">Turkmenistan</option>
                                                <option value="TC">Turks and Caicos Islands</option>
                                                <option value="TV">Tuvalu</option>
                                                <option value="UG">Uganda</option>
                                                <option value="UA">Ukraine</option>
                                                <option value="AE">United Arab Emirates</option>
                                                <option value="GB">United Kingdom (UK)</option>
                                                <option value="US">United States (US)</option>
                                                <option value="UM">United States (US) Minor Outlying Islands</option>
                                                <option value="UY">Uruguay</option>
                                                <option value="UZ">Uzbekistan</option>
                                                <option value="VU">Vanuatu</option>
                                                <option value="VA">Vatican</option>
                                                <option value="VE">Venezuela</option>
                                                <option value="VN">Vietnam</option>
                                                <option value="VG">Virgin Islands (British)</option>
                                                <option value="VI">Virgin Islands (US)</option>
                                                <option value="WF">Wallis and Futuna</option>
                                                <option value="EH">Western Sahara</option>
                                                <option value="WS">Samoa</option>
                                                <option value="YE">Yemen</option>
                                                <option value="ZM">Zambia</option>
                                                <option value="ZW">Zimbabwe</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 mt-3 cart">
                        <h5>{{trans('file.Your order')}}</h5>
                        <div class="row cart-header">
                            <div class="col-12">
                                <p class="mb-0">{{trans('file.product(s)')}}</p>
                            </div>
                        </div>
                        @foreach($cart as $id => $cart_product)
                        @php
                            if($cart_product['variant'] != 0){
                                $variant = implode(' | ', $cart_product['variant']);
                            }else{
                                $variant = 0;
                            }
                        @endphp
                        <div class="row cart-item">
                            <div class="col-3">
                                <div class="cart-item-img">
                                    @if(isset($cart_product['image']))
                                    @php
                                        $images = explode(',', $cart_product['image']);
                                        $cart_product['image'] = $images[0];
                                    @endphp
                                        @if(!config('database.connections.saleprosaas_landlord'))
                                        <img src="{{ asset('images/product/small') }}/{{ $cart_product['image'] }}" alt="{{ $cart_product['name'] }}">
                                        @else
                                        <img src="{{ asset('../../images/product/small') }}/{{ $cart_product['image'] }}" alt="{{ $cart_product['name'] }}">
                                        @endif
                                    @else
                                    <img src="https://dummyimage.com/80x80/e5e8ec/e5e8ec&text={{ $cart_product['name'] }}" alt="{{ $cart_product['name'] }}">
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <p class="mb-0 fw-500">{!! $cart_product['name'] !!} @if($variant != 0)({{$variant}})@endif</p>
                                <span>{{ $cart_product['qty'] }}</span> x <span>{{$currency->symbol ?? $currency->code}}{{ $cart_product['total_price'] / $cart_product['qty'] }}</span>
                            </div>
                            <div class="col-auto text-end">
                                <p class="fw-500">{{$currency->symbol ?? $currency->code}}{{ $cart_product['total_price'] }}</p>
                            </div>
                        </div>
                        @endforeach

                        <hr>

                        <div class="cost-details">
                            <p class="d-flex justify-content-between mt-3">
                                <span>{{trans('file.Sub Total')}}: </span>
                                <span>{{$currency->symbol ?? $currency->code}}<span class="sub_total">{{ $subTotal ?? 0.00 }}</span></span>
                                <input type="hidden" name="sub_total" id="input_sub_total" value="{{ $subTotal ?? 0.00 }}">
                            </p>
                            <p class="d-flex justify-content-between mt-3">
                                <span>{{trans('file.Shipping cost')}}: </span>
                                <span>{{$currency->symbol ?? $currency->code}}<span id="shipping_cost">0</span></span>
                                <input type="hidden" name="shipping_cost" id="input_shipping_cost" value="">
                            </p>
                            <p class="d-flex justify-content-between align-items-center mt-3 mb-3">
                                <strong>{{trans('file.grand total')}}: </strong>
                                <span class="h4">{{$currency->symbol ?? $currency->code}}<span id="total">0</span></span>
                                <input type="hidden" name="grand_total" id="input_total" value="">
                            </p>
                        </div>

                        <hr>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="button sm theme-color pl-0 pr-0 collapsed" data-toggle="collapse" data-target="#collapseCoupon" aria-expanded="false" aria-controls="collapseCoupon">{{trans('file.I have a coupon')}}</div>
                                <div class="collapse" id="collapseCoupon">
                                    <div class="input-group coupon-code">
                                        <input type="text" class="form-control" id="coupon_code" placeholder="{{trans('file.Enter coupon code')}}">
                                        <button type="submit" class="button sm style1 apply-coupon" style="font-size:20px;padding:0 10px;">
                                            <span class="material-symbols-outlined">arrow_right_alt</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="payment-options text-center">
                            @if(isset($pages))
                            <div class="res-box custom-control custom-checkbox text-center mt-3 mb-3">
                                <input type="checkbox" class="custom-control-input ml-2" id="tnc" required>
                                <label class="custom-control-label fw-500" for="tnc">{{trans('file.I have read and accept the')}}</label>
                                @foreach($pages as $index=>$page)
                                <a class="theme-color" href="{{url('/')}}/{{$page->slug}}">{{$page->page_name}}</a>
                                @if(count($pages) == ($index+1)) . @else , @endif
                                @endforeach
                            </div>
                            @endif

                            @if(isset($ecommerce_setting->gift_card) && ($ecommerce_setting->gift_card == 1))
                            <div class="row mt-3 mb-3">
                                <div class="col-md-12">
                                    <div class="button sm theme-color pl-0 pr-0 collapsed" data-toggle="collapse" data-target="#collapseGiftCard" aria-expanded="false" aria-controls="collapseGiftCard">{{trans('file.Pay with gift card')}}</div>
                                    <div class="collapse" id="collapseGiftCard">
                                        <div class="input-group gift-card">
                                            <input type="text" class="form-control" id="gift_card_no" placeholder="{{trans('file.Enter gift card number')}}">
                                            <button type="submit" class="button sm style1 apply-gift-card" style="font-size:20px;padding:0 10px;">
                                                <span class="material-symbols-outlined">arrow_right_alt</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @foreach($gateways as $gateway)
                            <label for="{{$gateway->name}}" class="button sm style3 pay-button online">
                                <input type="radio" id="{{$gateway->name}}" name="payment_mode" value="{{$gateway->name}}" required>
                                {{$gateway->name}}
                            </label>
                            @endforeach

                            <label for="Cash" class="button sm style3 pay-button cash">
                                <input type="radio" checked id="Cash" name="payment_mode" value="Cash on Delivery" required>
                                {{trans('file.Cash on Delivery')}}
                            </label>

                            <div class="col-md-12 payment-details">
                                <input type="hidden" id="gift_card_id" name="gift_card_id" value="">
                                <input type="hidden" id="gift_card_amount" name="gift_card_amount" value="">

                                <input type="hidden" id="coupon_id" name="coupon_id" value="">
                                <input type="hidden" id="coupon_discount" name="coupon_discount" value="">

                                <input type="hidden" id="item" name="item" value="">
                                <input type="hidden" id="total_qty" name="total_qty" value="{{ $total_qty ?? 0 }}">

                                <input type="hidden" name="currency" value="{{$currency->id}}">

                                <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{$ecommerce_setting->warehouse_id ?? 1}}">
                                <input type="hidden" id="biller_id" name="biller_id" value="{{$ecommerce_setting->biller_id ?? 1}}">

                                <button id="go" type="submit" class="button style1 d-block w-100 mt-3 place-order">Place Order</button>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </section>
    <!-- Content Wrapper Ends -->

    <!-- Login Modal starts -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModal" aria-hidden="true">
        <form class="d-block" action="{{ route('customerLogin') }}" method="post">
            @csrf
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="ti-close"></i></span>
                        </button> 
                        <div class="row">
                            <div class="col-12">
                                <h3 class="d-block text-center">Login</h3>
                            </div>
                            <div class="form-group col-12 mt-3">
                                <input type="text" name="email" id="email" tabindex="1" class="form-control" placeholder="{{trans('file.email')}}" value="" required>
                            </div>
                            <div class="form-group col-12">
                                <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="{{trans('file.Password')}}">
                            </div>
                            <div class="col-md-6 col-6 text-left">
                                <div class="">
                                    <div class="custom-control custom-checkbox text-center pl-0 mt-3 mb-3">
                                        <input type="checkbox" class="custom-control-input" id="remember">
                                        <label class="custom-control-label" for="remember">{{trans('file.Remember Me')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-6 text-right">
                                <div class="">
                                    <div class="mt-3 mb-3">
                                        <a href="{{url('/customer/forgot-password')}}" tabindex="5" class="forgot-password">{{trans('file.Forgot Password')}}?</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <input type="hidden" name="checkout" value="1" />
                                <button type="submit" id="login-submit" class="button style1 d-block">{{trans('file.Log In')}}</button>
                            </div>
                        </div>
                        <!-- <div class="row social-profile-login text-center">
                            <h5>Or Login with</h5>
                            <ul class="footer-social mt-3 pad-left-15">
                                <li><a href="#"><i class="ti-facebook"></i></a></li>
                            </ul>
                        </div> -->
                        <div class="text-center mt-3">
                            {{trans("file.don't have an account")}}? <a href="{{url('customer/register')}}">{{trans('file.sign up now')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!--Login modal ends-->

    @if(isset($ecommerce_setting->custom_chat))
    {{$ecommerce_setting->custom_chat}}
    @endif
    
    @if(!config('database.connections.saleprosaas_landlord'))
    <!--Plugin js -->
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/plugin.js") !!}
    </script>
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/bootstrap-select.min.js") !!}
    </script>
    <script src="{{ asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js') }}"></script>
    <!-- Main js -->
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/main.js") !!}
    </script>
    @else
    <!--Plugin js -->
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/plugin.js") !!}
    </script>
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/bootstrap-select.min.js") !!}
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
        $('select').selectpicker();

        var item = $('.cart-item').length;
        $('#item').val(item);

        function copy_input(x, y) {
            $(y).val($(x).val());
        }

        $('#name').on('input', function() {
            copy_input('#name', '#billing_name');
        });

        $('#phone').on('input', function() {
            copy_input('#phone', '#billing_phone');
        });

        $('#email').on('input', function() {
            copy_input('#email', '#billing_email');
        });

        $('#address').on('input', function() {
            copy_input('#address', '#billing_address');
        });

        $('#state').on('input', function() {
            copy_input('#state', '#billing_state');
        });

        $('#city').on('input', function() {
            copy_input('#city', '#billing_city');
        });

        $('#zip').on('input', function() {
            copy_input('#zip', '#billing_zip');
        });

        $('#country').on('changed.bs.select', function() {
            copy_input('#country', '#billing_country');
            $('#billing_country').selectpicker('refresh');
        });
        
        if ($('#address_list').val() == '0') {
            $('.shipping_details input,.shipping_details select,.billing_details input,.billing_details select').val('');
            $('.shipping_details').removeClass('d-none');
            $('.default_address').addClass('d-none');
        } 

        $('#address_list').on('change', function() {
            var def_address = $('input[name="default_address"]').val();
            if ((def_address.length > 0) && ($(this).val() == def_address)) {
                $('.default_address').removeClass('d-none');

                $('#name').val('{{$def_address->name ?? $customer->name ?? "" }}');
                copy_input('#name', '#billing_name');

                $('#phone').val('{{$def_address->phone ?? $customer->phone ?? ""}}');
                copy_input('#phone', '#billing_phone');

                $('#email').val('{{$def_address->email ?? $customer->email ?? ""}}');
                copy_input('#email', '#billing_email');

                $('#address').val('{{$def_address->address ?? ""}}');
                copy_input('#address', '#billing_address');

                $('#state').val('{{$def_address->state ?? ""}}');
                copy_input('#state', '#billing_state');

                $('#city').val('{{$def_address->city ?? ""}}');
                copy_input('#city', '#billing_city');

                $('#zip').val('{{$def_address->zip ?? ""}}');
                copy_input('#zip', '#billing_zip');

                $('#country').val('{{$def_address->country ?? ""}}');
                copy_input('#country', '#billing_country');
                $('#country,#billing_country').selectpicker('refresh');

            } else if ($(this).val() == '0') {
                $('.shipping_details input,.shipping_details select,.billing_details input,.billing_details select').val('');
                $('.shipping_details').removeClass('d-none');
                $('.default_address').addClass('d-none');
            } else {
                $('.default_address').addClass('d-none');

                var url = '{{url("/customer/address/edit/")}}/' + $(this).val();
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: 'json',
                    success: function(data) {
                        if ((data.name) && data.name.length > 1) {
                            $('#name').val(data.name);
                            copy_input('#name', '#billing_name');
                        } else {
                            $('#name').val('{{$customer->name ?? "" }}');
                            copy_input('#name', '#billing_name');
                        }

                        if ((data.phone) && data.phone.length > 1) {
                            $('#phone').val(data.phone);
                            copy_input('#phone', '#billing_phone');
                        } else {
                            $('#phone').val('{{$customer->phone ?? "" }}');
                            copy_input('#phone', '#billing_phone');
                        }

                        if ((data.email) && data.phone.email > 1) {
                            $('#email').val(data.email);
                            copy_input('#email', '#billing_email');
                        } else {
                            $('#email').val('{{$customer->email ?? "" }}');
                            copy_input('#email', '#billing_email');
                        }

                        $('#address').val(data.address);
                        copy_input('#address', '#billing_address');
                        $('#city').val(data.city);
                        copy_input('#city', '#billing_city');
                        $('#state').val(data.state);
                        copy_input('#state', '#billing_state');
                        $('#zip').val(data.zip);
                        copy_input('#zip', '#billing_zip');
                        $('#country').val(data.country);
                        copy_input('#country', '#billing_country');
                        $('#country,#billing_country').selectpicker('refresh');

                        $('.shipping_details').removeClass('d-none');
                    }
                })

            }
        })


        $('#payment-form').submit(function() {
            $('#go').on('click', function() {
                $('#go').attr("disabled", 'disabled').html('processing...');

            })
        })

        //coupon discount
        $(document).on('click', '.apply-coupon', function(e) {
            e.preventDefault();

            var coupon_code = $('#coupon_code').val();
            var route = "{{ route('applyCoupon') }}";

            if (coupon_code == '') {
                $('.alert').addClass('alert-danger show');
                $('.alert-danger .message').html('Please enter a valid coupon code');
            } else {
                $('.apply-coupon').attr('disabled', true);

                $.ajax({
                    url: route,
                    type: "POST",
                    data: {
                        coupon_code: coupon_code,
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status == 'success') {
                            $('.alert').addClass('alert-custom show');
                            $('.alert-custom .message').html(response.message);

                            var total = parseFloat($('.sub_total').html());

                            $('#coupon_id').val(response.coupon_id);

                            if (response.coupon_type == 'percentage') {
                                var discount_amount = Math.round(((total * response.discount_amount) / 100));
                                $('#coupon_discount').val(discount_amount);
                                total = Math.round((total - discount_amount));
                                $('.sub_total').html(total);
                                $('#input_sub_total').val(total);

                                var sub_total = parseFloat($('.sub_total').html());
                                var free_shipping_from = parseFloat('{{$ecommerce_setting->free_shipping_from ?? 0}}');
                                var flat_rate_shipping = parseFloat('{{$ecommerce_setting->flat_rate_shipping ?? 0}}');

                                if (sub_total > free_shipping_from) {
                                    $('#shipping_cost').html('0');
                                    $('#total').html(sub_total);
                                    $('#input_shipping_cost').val('0');
                                    $('#input_total').val(sub_total);
                                } else {
                                    $('#shipping_cost').html(flat_rate_shipping);
                                    $('#input_shipping_cost').val(flat_rate_shipping);
                                    var total = (sub_total + flat_rate_shipping);
                                    $('#total').html(total);
                                    $('#input_total').val(total);
                                }

                            } else {

                                $('#coupon_discount').val(response.discount_amount);
                                total = Math.round((total - response.discount_amount));
                                $('.sub_total').html(total);
                                $('#input_sub_total').val(total);

                                var sub_total = parseFloat($('.sub_total').html());
                                var free_shipping_from = parseFloat('{{$ecommerce_setting->free_shipping_from ?? 0}}');
                                var flat_rate_shipping = parseFloat('{{$ecommerce_setting->flat_rate_shipping ?? 0}}');

                                if (sub_total > free_shipping_from) {
                                    $('#shipping_cost').html('0');
                                    $('#total').html(sub_total);
                                    $('#input_shipping_cost').val('0');
                                    $('#input_total').val(sub_total);
                                } else {
                                    $('#shipping_cost').html(flat_rate_shipping);
                                    $('#input_shipping_cost').val(flat_rate_shipping);
                                    var total = (sub_total + flat_rate_shipping);
                                    $('#total').html(total);
                                    $('#input_total').val(total);
                                }

                            }
                        } else {
                            $('.alert').addClass('alert-danger show');
                            $('.alert-danger .message').html('Please enter a valid coupon code');
                        }
                    },
                });
            }

        });
        
        @if(isset($ecommerce_setting->gift_card) && ($ecommerce_setting->gift_card == 1))
        //gift card discount
        $(document).on('click', '.apply-gift-card', function(e) {
            e.preventDefault();

            var gift_card_no = $('#gift_card_no').val();
            var route = "{{ route('applyGiftCard') }}";

            if (gift_card_no == '') {
                $('.alert').addClass('alert-danger show');
                $('.alert-danger .message').html('Please enter a valid gift card number');
            } else {
                $('.apply-gift-card').attr('disabled', true);

                $.ajax({
                    url: route,
                    type: "POST",
                    data: {
                        gift_card_no: gift_card_no,
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status == 'success') {
                            $('#gift_card_id').val(response.gift_card_id);
                            $('#gift_card_amount').val(response.gift_card_amount);

                            var to_pay = 0;
                            var grand_total = $('#input_total').val();

                            if(response.gift_card_amount < grand_total) {
                                to_pay = '{{$currency->symbol ?? $currency->code}}'+(grand_total - response.gift_card_amount).toFixed(2);
                            } else {
                                to_pay = 0;
                            }

                            if(to_pay == 0){
                                $('.payment-details').prepend('<h4>Please place order below</h4>');
                            } else {
                                $('.payment-details').prepend('<h4>Please pay '+to_pay+'</h4>');
                            }

                        } else if (response.status == 'error') {
                            $('.alert').addClass('alert-custom show');
                            $('.alert-custom .message').html(response.message);
                        } else {
                            $('.alert').addClass('alert-custom show');
                            $('.alert-custom .message').html('Please enter a valid gift card number');
                        }
                    },
                });
            }

        });
        @endif

        //shipping cost
        $(document).on('click', function() {
            var sub_total = parseFloat($('.sub_total').html());
            var free_shipping_from = parseFloat('{{$ecommerce_setting->free_shipping_from ?? 0}}');
            var flat_rate_shipping = parseFloat('{{$ecommerce_setting->flat_rate_shipping ?? 0}}');

            if (sub_total > free_shipping_from) {
                $('#shipping_cost').html('0');
                $('#total').html(sub_total);
                $('#input_shipping_cost').val('0');
                $('#input_total').val(sub_total);
            } else {
                $('#shipping_cost').html(flat_rate_shipping);
                $('#input_shipping_cost').val(flat_rate_shipping);
                var total = (sub_total + flat_rate_shipping);
                $('#total').html(total);
                $('#input_total').val(total);
            }
        })

        var sub_total = parseFloat($('.sub_total').html());
        var free_shipping_from = parseFloat('{{$ecommerce_setting->free_shipping_from ?? 0}}');
        var flat_rate_shipping = parseFloat('{{$ecommerce_setting->flat_rate_shipping ?? 0}}');

        if (sub_total > free_shipping_from) {
            $('#shipping_cost').html('0');
            $('#total').html(sub_total);
            $('#input_shipping_cost').val('0');
            $('#input_total').val(sub_total);
        } else {
            $('#shipping_cost').html(flat_rate_shipping);
            $('#input_shipping_cost').val(flat_rate_shipping);
            var total = (sub_total + flat_rate_shipping);
            $('#total').html(total);
            $('#input_total').val(total);
        }

        $("#different_billing").change(function() {
            if ($(this).is(':checked')) {
                $('#billingAddress').removeClass('show');
            } else {
                $('#billingAddress').addClass('show');
            }
        });

        $(document).on('click', '.pay-button', function(e) {
            $('.pay-button').removeClass('selected');
            $(this).toggleClass('selected');
        });
    </script>

</body>

</html>