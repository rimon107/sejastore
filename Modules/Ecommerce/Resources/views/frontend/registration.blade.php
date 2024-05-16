@extends('ecommerce::frontend.layout.main')

@section('title') {{ $ecommerce_setting->site_title ?? '' }} @endsection

@section('description')  @endsection

@section('content')
    <section class="user-login-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 offset-md-4">
                    <form id="register-form" action="{{ route('customerRegistration') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="name" id="name" tabindex="1" class="form-control" placeholder="{{trans('file.name')}} *" value="" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" id="email" tabindex="1" class="form-control" placeholder="{{trans('file.email')}} *" value="" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" id="user_password" tabindex="2" class="form-control" placeholder="{{trans('file.Password')}} *" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password_confirmation" id="confirm-password" tabindex="2" class="form-control" placeholder="{{trans('file.Confirm Password')}} *" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" id="register-submit" class="button style1 d-block"> {{trans('file.Register')}}</button>
                        </div>
                        <div class="text-center">
                            {{trans('file.Already have an account')}}? <a href="{{url('customer/login')}}">{{trans('file.Log In')}}</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')

@endsection
