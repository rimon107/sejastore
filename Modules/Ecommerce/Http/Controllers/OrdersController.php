<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Product_Sale;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\Payment;
use App\Models\PaymentWithCreditCard;
use App\Models\PaymentWithGiftCard;
use App\Models\PaymentWithPaypal;
use App\Models\Account;
use App\Models\GiftCard;
use Stripe\Stripe;
use DB;
use Illuminate\Support\Facades\Auth;

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

        $post_data['warehouse_id'] = $request->input('warehouse_id');
        $post_data['biller_id'] = $request->input('biller_id');

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
                ['name' => 'Guest', 'password' => '12345678', 'phone' => '12345678', 'role_id' => 5, 'is_active' => 1, 'is_deleted' => 0]
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
        $create_sale = Sale::create([
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
            'warehouse_id'         => $post_data['warehouse_id'],
            'biller_id'            => $post_data['biller_id'],
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
            'payment_mode'         => $post_data['payment_mode'],
            'created_at'           => date('Y-m-d H:i:s')
        ]);

        $cart = session()->has('cart') ? session()->get('cart') : [];
        foreach ($cart as $key => $product) {
            if($product['variant'] != 0){
                $product_variant = implode('/',$product['variant']);
                $variant = DB::table('variants')->where('name',$product_variant)->first();
                $variant_id = $variant->id;
            }else{
                $variant_id = 0;
            }
            Product_Sale::create([
                'sale_id' => $create_sale->id,
                'product_id' => $product['id'], 
                'variant_id' => $variant_id,
                'qty' => $product['qty'],
                'net_unit_price' => $product['unit_price'],
                'sale_unit_id' => $product['sale_unit_id'],
                'discount' => 0,
                'tax_rate' => 0,
                'tax' => 0,
                'total' => $product['total_price'],
            ]);
        }

        if(auth()->user()){
            $user_id = auth()->user()->id;
        } else {
            $user = User::where('role_id',1)->where('is_active',1)->first();
            $user_id = $user->id;
        }

        $account = Account::where('is_default',1)->first();

        if(strlen($request->input('gift_card_id')) > 0 && strlen($request->input('gift_card_amount')) > 0){
            $post_data['gift_card_id'] = trim(htmlspecialchars($request->input('gift_card_id')));
            $post_data['gift_card_amount'] = trim(htmlspecialchars($request->input('gift_card_amount')));

            $payment = Payment::create([
                'payment_reference' => 'spr-'.date('Ymd-His'),
                'user_id' => $user_id,
                'sale_id' => $create_sale->id,
                'account_id' => $account->id, 
                'amount' => $post_data['gift_card_amount'],
                'change' => 0,
                'paying_method' => 'Gift Card'
            ]);

            PaymentWithGiftCard::create([
                'gift_card_id' => $post_data['gift_card_id'],
                'payment_id' => $payment->id,
            ]);

            $gift_card = GiftCard::where('id',$post_data['gift_card_id'])->first();
            $gift_card->amount = ($gift_card->amount - $post_data['gift_card_amount']);
            $gift_card->save();

            $post_data['grand_total'] = ($post_data['grand_total'] - $post_data['gift_card_amount']);
        }
        
        if($post_data['payment_mode'] != 'Cash on Delivery') {
            session(['grand_total' => $post_data['grand_total'], 'sale_id' => $create_sale->id, 'mode' => $post_data['payment_mode'], 'user_id' => $user_id, 'account_id' => $account->id]);
            return redirect()->route('online.payment');
        } else {  
            if($post_data['grand_total'] > 0){
                $payment = Payment::create([
                    'payment_reference' => 'spr-'.date('Ymd-His'),
                    'user_id' => $user_id,
                    'sale_id' => $create_sale->id,
                    'account_id' => $account->id, 
                    'amount' => $post_data['grand_total'],
                    'change' => 0,
                    'paying_method' => 'Cash'
                ]);  
            } 

            session(['cart' => [], 'total_qty' => 0, 'subTotal' => 0]);
            return redirect()->route('order.success',$create_sale->reference_no);
        }

    }

    public function onlinePayment()
    {
        $grand_total = session()->get('grand_total');
        $sale_id = session()->get('sale_id');
        $mode = session()->get('mode');

        $user_id = session()->get('user_id');
        $account_id = session()->get('account_id');

        if($mode == 'Stripe'){
            $payment = Payment::create([
                'payment_reference' => 'spr-'.date('Ymd-His'),
                'user_id' => $user_id,
                'sale_id' => $sale_id,
                'account_id' => $account_id, 
                'amount' => $grand_total,
                'change' => 0,
                'paying_method' => 'Credit Card'
            ]);
    
            $payment_id = $payment->id;

            $stripe = DB::table('external_services')->where('name','Stripe')->first();
            $stripe_details = $stripe->details;
            $details_array = explode(';',$stripe_details);
            $publishable_key = explode(',', $details_array[1])[1];

            return view('ecommerce::frontend.checkout-online', compact('publishable_key','grand_total','sale_id','mode','payment_id'));
        }

        if($mode == 'PayPal'){
            $payment = Payment::create([
                'payment_reference' => 'spr-'.date('Ymd-His'),
                'user_id' => $user_id,
                'sale_id' => $sale_id,
                'account_id' => $account_id, 
                'amount' => $grand_total,
                'change' => 0,
                'paying_method' => 'Paypal'
            ]);
    
            $payment_id = $payment->id;

            $paypal = DB::table('external_services')->where('name','PayPal')->first();
            $paypal_details = $paypal->details;
            $details_array = explode(';',$paypal_details);
            $client_id = explode(',', $details_array[1])[0];

            return view('ecommerce::frontend.checkout-online', compact('client_id','grand_total','sale_id','mode','payment_id'));
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

        PaymentWithCreditCard::create([
            'payment_id' => $request->payment_id,
            'charge_id' => $charge->id,
        ]);

        $sale = Sale::find($request->sale_id);
        $sale->payment_status = 4;
        $sale->save();

        session(['cart' => [], 'total_qty' => 0, 'subTotal' => 0, 'grand_total' => 0, 'sale_id' => '', 'mode' => '', 'user_id' => '', 'account_id' => '']);

        return redirect()->route('order.success',['sale_reference' => $sale->reference_no]);

    }

    public function paypalPayment(Request $request)
    {
        PaymentWithPaypal::create([
            'payment_id' => $request->payment_id,
            'transaction_id' => $request->transaction_id
        ]);
        
        $sale = Sale::find(session()->get('sale_id'));
        $sale->payment_status = 4;
        $sale->save();

        session(['cart' => [], 'total_qty' => 0, 'subTotal' => 0, 'grand_total' => 0, 'sale_id' => '', 'mode' => '', 'user_id' => '', 'account_id' => '']);

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
