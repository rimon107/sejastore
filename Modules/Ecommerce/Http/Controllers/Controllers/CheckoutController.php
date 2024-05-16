<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Coupon;
use DB;

class CheckoutController extends Controller
{
	public function index(Request $request)
	{
		$cart = session()->has('cart') ? session()->get('cart') : [];
		$total_qty = session()->has('total_qty') ? session()->get('total_qty') : 0;
		$subTotal = session()->has('subTotal') ? session()->get('subTotal') : 0;

		if(cache()->has('ecommerce_setting')){
			$settings = cache('ecommerce_setting');
			$pages = DB::table('pages')->select('id','page_name','slug')->whereIn('id',json_decode($settings->checkout_pages))->get();
		}

		$gateways = DB::table('external_services')->where('type','payment')->where('active',1)->get();
		
		if(auth()->user()) {
			$customer = DB::table('customers')->where('user_id',auth()->user()->id)->first();
			$addresses = DB::table('customer_addresses')->where('customer_id',$customer->id)->get();
			$def_address = $addresses->where('default',1)->first();

			return view('ecommerce::frontend.checkout', compact('cart', 'total_qty', 'subTotal', 'customer', 'addresses', 'def_address', 'pages','gateways'));
		}
		
		return view('ecommerce::frontend.checkout', compact('cart', 'total_qty', 'subTotal', 'pages','gateways'));
	}

	public function applyCoupon(Request $request)
	{
		$code = $request->input('coupon_code');
		$coupons = Coupon::where('code', $code)->where('expired_date', '>', date('Y-m-d'))->where('is_active', 1)->first();

		if($coupons) {
			return response()->json(['status' => 'success', 'coupon_id' => $coupons->id, 'coupon_type' => $coupons->type, 'discount_amount' => $coupons->amount, 'message'=>'Discount applied']);
		} else {
			return response()->json(['status' => 'error', 'message'=>'Discount applied']);
		}

	}
	
	public function applyMembership(Request $request)
	{
		$card_no = $request->input('card_no');
		$membership = Membership::where('card_no', $card_no)->where('is_active', 1)->first();

		if($membership) {
			return response()->json(['status' => 'success', 'card_no' => $membership->card_no, 'message'=>'Discount applied']);
		} else {
			return response()->json(['status' => 'error', 'message'=>'Membership details do not match!']);
		}

	}
}