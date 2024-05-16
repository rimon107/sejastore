<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Product_Sale;
use App\Models\Product;
use App\Models\Product_Warehouse;
use Stripe\Stripe;
use DB;

class OrdersController extends Controller
{

    public function create(Request $request)
    {

        $post_data = array();
        
        $post_data['reference_no'] = uniqid(); // reference_no must be unique
        $post_data['currency'] = $request->input('currency');

        # BILLING INFORMATION
        $post_data['billing_name'] = trim(htmlspecialchars($request->input('billing_name')));
        $post_data['billing_email'] = trim(htmlspecialchars($request->input('billing_email')));
        $post_data['billing_phone'] = trim(htmlspecialchars($request->input('billing_phone')));
        $post_data['billing_address'] = trim(htmlspecialchars($request->input('billing_address')));
        $post_data['billing_city'] = trim(htmlspecialchars($request->input('billing_city')));
        $post_data['billing_state'] = trim(htmlspecialchars($request->input('billing_state')));
        $post_data['billing_zip'] = trim(htmlspecialchars($request->input('billing_zip')));
        $post_data['billing_country'] = trim(htmlspecialchars($request->input('billing_country')));

        # SHIPMENT INFORMATION
        $post_data['shipping_name'] = trim(htmlspecialchars($request->input('shipping_name')));
        $post_data['shipping_email'] = trim(htmlspecialchars($request->input('shipping_email')));
        $post_data['shipping_phone'] = trim(htmlspecialchars($request->input('shipping_phone')));
        $post_data['shipping_address'] = trim(htmlspecialchars($request->input('shipping_address')));
        $post_data['shipping_city'] = trim(htmlspecialchars($request->input('shipping_city')));
        $post_data['shipping_state'] = trim(htmlspecialchars($request->input('shipping_state')));
        $post_data['shipping_zip'] = trim(htmlspecialchars($request->input('shipping_zip')));
        $post_data['shipping_country'] = trim(htmlspecialchars($request->input('shipping_country')));

        $post_data['sale_note'] = trim(htmlspecialchars($request->input('sale_note')));

        $post_data['payment_mode'] = trim(htmlspecialchars($request->input('payment_mode')));

        if($post_data['payment_mode'] == 'Stripe'){
            $post_data['stripe_token'] = $request->input('stripeToken');
        }

        if(auth()->user())
        {
            $post_data['user_id'] = auth()->user()->id;
            $customer = Customer::select('id')->where('user_id',$post_data['user_id'])->first();

            $post_data['customer_id'] = $customer->id;
        } else {
            $user = User::firstOrCreate(
                ['email' =>  'guest@customer.com'],
                ['name' => 'Guest', 'password' => '12345678', 'phone' => '12345678', 'role_id' => 5, 'is_active' => 1]
            );

            $customer = Customer::firstOrCreate(
                ['email' =>  'guest@customer.com'],
                ['name' => 'Guest', 'phone_number' => '12345678', 'address' => ' ', 'city' => ' ', 'customer_group_id' => 1]
            );


            $post_data['user_id'] = $user->id;
            $post_data['customer_id'] = $customer->id; // walk-in-customer / guest customer
        }


        # OPTIONAL PARAMETERS
        $post_data['item'] = trim(htmlspecialchars($request->input('item')));
        $post_data['total_qty'] = trim(htmlspecialchars($request->input('total_qty')));
        $post_data['total_discount'] = 0; 
        $post_data['total_tax'] = 0;  
        $post_data['sub_total'] = trim(htmlspecialchars($request->input('sub_total')));             
        $post_data['grand_total'] = trim(htmlspecialchars($request->input('grand_total')));
        if(!$request->input('coupon_id')){
            $post_data['coupon_id'] = NULL;
            $post_data['coupon_discount'] = NULL;
        } else {
            $post_data['coupon_id'] = trim(htmlspecialchars($request->input('coupon_id')));
            $post_data['coupon_discount'] = trim(htmlspecialchars($request->input('coupon_discount')));
        }

        $post_data['shipping_cost'] = trim(htmlspecialchars($request->input('shipping_cost')));

        #Before  going to initiate the payment order status need to update as Pending.
        $create_sale = DB::table('sales')->insertGetId([
            'reference_no'         => $post_data['reference_no'],
            'user_id'              => $post_data['user_id'],
            'customer_id'          => $post_data['customer_id'],
            'billing_name'         => $post_data['billing_name'],
            'billing_email'        => $post_data['billing_email'],
            'billing_phone'        => $post_data['billing_phone'],
            'billing_address'      => $post_data['billing_address'],
            'billing_city'         => $post_data['billing_city'],
            'billing_state'        => $post_data['billing_state'],
            'billing_zip'          => $post_data['billing_zip'],
            'billing_country'      => $post_data['billing_country'],
            'shipping_name'        => $post_data['shipping_name'],
            'shipping_email'       => $post_data['shipping_email'],
            'shipping_phone'       => $post_data['shipping_phone'],
            'shipping_address'     => $post_data['shipping_address'],
            'shipping_city'        => $post_data['shipping_city'],
            'shipping_state'       => $post_data['shipping_state'],
            'shipping_zip'         => $post_data['shipping_zip'],
            'shipping_country'     => $post_data['shipping_country'],
            'warehouse_id'         => 1,
            'item'                 => $post_data['item'],
            'total_qty'            => $post_data['total_qty'],
            'total_discount'       => $post_data['total_discount'], 
            'total_tax'            => $post_data['total_tax'],  
            'total_price'          => $post_data['sub_total'],             
            'grand_total'          => $post_data['grand_total'],
            'coupon_id'            => $post_data['coupon_id'],
            'coupon_discount'      => $post_data['coupon_discount'],
            'shipping_cost'        => $post_data['shipping_cost'],
            'sale_status'          => 2,
            'payment_status'       => 1,
            'sale_note'            => $post_data['sale_note'],               
            'sale_type'            => 'online',
            'payment_mode'         => $post_data['payment_mode']
        ]);

        $cart = session()->has('cart') ? session()->get('cart') : [];
        foreach ($cart as $key => $product) {
            Product_Sale::create([
                'sale_id' => $create_sale,
                'product_id' => $key, 
                'qty' => $product['qty'],
                'net_unit_price' => $product['unit_price'],
                'sale_unit_id' => $product['sale_unit_id'],
                'discount' => 0,
                'tax_rate' => 0,
                'tax' => 0,
                'total' => $product['total_price'],
            ]);
        }

        if($post_data['payment_mode'] != 'Cash') {
            session(['grand_total' => $post_data['grand_total'], 'sale_id' => $create_sale, 'mode' => $post_data['payment_mode']]);
            return redirect()->route('online.payment');
        } else {            
            session(['cart' => [], 'total_qty' => 0, 'subTotal' => 0]);
            return redirect()->route('order.success');
        }

    }

    public function onlinePayment()
    {
        $grand_total = session()->get('grand_total');
        $sale_id = session()->get('sale_id');
        $mode = session()->get('mode');

        if($mode == 'Stripe'){
            $stripe = DB::table('external_services')->where('name','Stripe')->first();
            $stripe_details = $stripe->details;
            $details_array = explode(';',$stripe_details);
            $publishable_key = explode(',', $details_array[1])[1];

            return view('ecommerce::frontend.checkout-online', compact('publishable_key','grand_total','sale_id','mode'));
        }

        if($mode == 'PayPal'){
            $paypal = DB::table('external_services')->where('name','PayPal')->first();
            $paypal_details = $paypal->details;
            $details_array = explode(';',$paypal_details);
            $client_id = explode(',', $details_array[1])[0];

            return view('ecommerce::frontend.checkout-online', compact('client_id','grand_total','sale_id','mode'));
        }
        
    }

    public function stripePayment(Request $request)
    {
        $stripe = DB::table('external_services')->where('name','Stripe')->first();
        $stripe_details = $stripe->details;
        $details_array = explode(';',$stripe_details);
        $secret_key = explode(',', $details_array[1])[0];

        Stripe::setApiKey($secret_key);
    
        $token = $request->stripeToken;
        $amount = $request->grand_total;
        $currency = cache('currency')->code;
        // Create a Customer:
        $stripe_customer = \Stripe\Customer::create([
            'source' => $token
        ]);
        
        // Charge the Customer instead of the card:
        $charge = \Stripe\Charge::create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'customer' => $stripe_customer->id
        ]);

        $sale = Sale::find($request->sale_id);
        $sale->payment_status = 4;
        $sale->save();

        session(['cart' => [], 'total_qty' => 0, 'subTotal' => 0, 'grand_total' => 0, 'sale_id' => '', 'mode' => '']);

        return redirect()->route('order.success',['sale_reference' => $sale->reference_no]);

    }

    public function paypalPayment(Request $request)
    {
        $sale = Sale::find(session()->get('sale_id'));
        $sale->payment_status = 4;
        $sale->save();

        session(['cart' => [], 'total_qty' => 0, 'subTotal' => 0, 'grand_total' => 0, 'sale_id' => '', 'mode' => '']);

        return response()->json(["success" => "Success", "sale_reference" => $sale->reference_no]);
    }

    public function success($sale_reference)
    {
        return view('ecommerce::frontend.success',compact('sale_reference'));
    }

    public function fail(Request $request)
    {

    }

    public function cancelOnline(Request $request)
    {

    }
}
