<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
	public function index(Request $request)
	{
		$cart = session()->has('cart') ? session()->get('cart') : [];
		$total_qty = session()->has('total_qty') ? session()->get('total_qty') : 0;
		$subTotal = session()->has('subTotal') ? session()->get('subTotal') : 0;

		if ($request->ajax()) {
            return response()->json(['cart'=>$cart, 'total_qty'=>$total_qty, 'subTotal'=>$subTotal]);  
        }
		
		return view('ecommerce::frontend.cart', compact('cart', 'total_qty', 'subTotal'));
	}

    public function addToCart(Request $request)
    {
    	$id = $request->input('product_id');
    	if($request->input('qty'))
    		$qty = $request->input('qty');
    	else
    		$qty = 1;

    	$product = Product::select('image', 'name', 'price', 'sale_unit_id', 'promotion_price', 'promotion')->find($id);
    	$cart = session()->has('cart') ? session()->get('cart') : [];
    	$total_qty = session()->has('total_qty') ? session()->get('total_qty') : 0;
		$subTotal = session()->has('subTotal') ? session()->get('subTotal') : 0;

    	if(array_key_exists($id, $cart)) {
    		$cart[$id]['qty'] += $qty;
    		$cart[$id]['total_price'] += $qty * $cart[$id]['unit_price'];
    	}
    	else {
            if($product->promotion) {
        		$cart[$id] = [
        			'id' => $id,
        			'image' => $product->image,
        			'name' => $product->name,
        			'qty' => $qty,
                    'sale_unit_id' => $product->sale_unit_id,
        			'unit_price' => $product->promotion_price,
        			'total_price' => $qty * $product->promotion_price,
        		];
            }
            else {
                $cart[$id] = [
                    'id' => $id,
                    'image' => $product->image,
                    'name' => $product->name,
                    'qty' => $qty,
                    'sale_unit_id' => $product->sale_unit_id,
                    'unit_price' => $product->price,
                    'total_price' => $qty * $product->price,
                ];
            }
    	}
    	$total_qty += $qty;
    	$subTotal += $qty * $cart[$id]['unit_price'];

    	session(['cart' => $cart, 'total_qty' => $total_qty, 'subTotal' => $subTotal]);

    	return response()->json(['total_qty' => $total_qty, 'subTotal' => $subTotal, 'success'=>'Product added to cart']);
    }

    public function updateCart(Request $request)
    {
    	$product_id = $request->input('product_id');
    	$product_qty = $request->input('product_qty');
    	
    	$max_qty = Product::select('qty')->where('id',$product_id)->first();
    	
    	//return $max_qty->qty;
    	
    	if($product_qty > $max_qty->qty) {
    	    $product_qty = $max_qty->qty;
    	}
    	
    	$cart = session()->has('cart') ? session()->get('cart') : [];
    	$total_qty = session()->has('total_qty') ? session()->get('total_qty') : 0;
		$subTotal = session()->has('subTotal') ? session()->get('subTotal') : 0;

		$old_qty = $cart[$product_id]['qty'];
		$old_price = $cart[$product_id]['total_price'];

		$cart[$product_id]['qty'] = $product_qty;
		$cart[$product_id]['total_price'] = $product_qty * $cart[$product_id]['unit_price'];

		$total_qty = $total_qty + $product_qty - $old_qty;
		$subTotal = $subTotal + $cart[$product_id]['total_price'] - $old_price;

		session(['cart' => $cart, 'total_qty' => $total_qty, 'subTotal' => $subTotal]);
    	
    	return response()->json(['total_qty' => $total_qty, 'subTotal' => $subTotal, 'success'=>'Product added to cart']);
    }

    public function removeFromCart(Request $request)
    {
    	$id = $request->input('product_id');
    	$cart = session()->get('cart');
    	$total_qty = session()->get('total_qty');
    	$subTotal = session()->get('subTotal');
    	//removing element from cart
    	$total_qty -= $cart[$id]['qty'];
    	$subTotal -= $cart[$id]['total_price'];
    	unset($cart[$id]);
    	session(['cart' => $cart, 'total_qty' => $total_qty, 'subTotal' => $subTotal]);

    	return response()->json(['deleted_item' => $id, 'total_qty' => $total_qty, 'subTotal' => $subTotal, 'success'=>'Product successfully removed from cart']);

    }
}