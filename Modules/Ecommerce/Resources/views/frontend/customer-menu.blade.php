<ul class="">
    <li>
        <a class="nav-link {{ (request()->is('profile')) ? 'active' : '' }}" href="{{ url('/customer/profile') }}">{{trans('file.My Profile')}}</a>
    </li>
    <li>
        <a class="nav-link {{ (request()->is('orders')) ? 'active' : '' }}" href="{{ url('/customer/orders') }}">{{trans('file.My Orders')}}</a>
    </li>
    <li>
        <a class="nav-link {{ (request()->is('wishlist')) ? 'active' : '' }}" href="{{ url('/customer/wishlist') }}">{{trans('file.My Wishlist')}}</a>
    </li>
    <li>
        <a class="nav-link {{ (request()->is('address')) ? 'active' : '' }}" href="{{ url('/customer/address') }}">{{trans('file.My Addresses')}}</a>
    </li>
    <li>
        <a class="nav-link {{ (request()->is('account-details')) ? 'active' : '' }}" href="{{ url('/customer/account-details') }}">{{trans('file.Account Details')}}</a>
    </li>
    <li>
        <a class="nav-link" href="{{ url('/customer/logout') }}">{{trans('file.logout')}}</a>
    </li>
</ul>