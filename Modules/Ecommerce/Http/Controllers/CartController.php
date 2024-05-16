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

		if($total_qty == 0){
			$subTotal = 0;
		}

		if ($request->ajax()) {
            return response()->json(['cart'=>$cart, 'total_qty'=>$total_qty, 'subTotal'=>$subTotal]);  
        }
		
		return view('ecommerce::frontend.cart', compact('cart', 'total_qty', 'subTotal'));
	}

    public function addToCart(Request $request)
    {
		$cid = $request->input('product_id');

    	$id = explode(',',$cid)[0];
    	if($request->input('qty'))
    		$qty = $request->input('qty');
    	else
    		$qty = 1;
		if($request->input('variant') && ($request->input('variant') != 0))
			$variant = $request->input('variant');
		else
			$variant = 0;

    	$product = Product::select('image', 'name', 'slug', 'price', 'sale_unit_id', 'promotion_price', 'promotion')->find($id);
    	$cart = session()->has('cart') ? session()->get('cart') : [];
    	$total_qty = session()->has('total_qty') ? session()->get('total_qty') : 0;
		$subTotal = session()->has('subTotal') ? session()->get('subTotal') : 0;

    	if(array_key_exists($cid, $cart)) {
			$cart[$cid]['qty'] += $qty;
			$cart[$cid]['total_price'] += $qty * $cart[$cid]['unit_price'];
    	}
    	else {
            if($product->promotion) {
        		$cart[$cid] = [
        			'id' => $id,
        			'image' => $product->image,
        			'name' => $product->name,
					'slug' => $product->slug,
        			'qty' => $qty,
                    'sale_unit_id' => $product->sale_unit_id,
        			'unit_price' => $product->promotion_price,
        			'total_price' => $qty * $product->promotion_price,
					'variant' => $variant
        		];
            }
            else {
                $cart[$cid] = [
                    'id' => $id,
                    'image' => $product->image,
                    'name' => $product->name,
					'slug' => $product->slug,
                    'qty' => $qty,
                    'sale_unit_id' => $product->sale_unit_id,
                    'unit_price' => $product->price,
                    'total_price' => $qty * $product->price,
					'variant' => $variant
                ];
            }
    	}
    	$total_qty += $qty;
    	$subTotal += $qty * $cart[$cid]['unit_price'];

		if($total_qty == 0){
			$subTotal = 0;
		}

    	session(['cart' => $cart, 'total_qty' => $total_qty, 'subTotal' => $subTotal]);

    	return response()->json(['total_qty' => $total_qty, 'subTotal' => $subTotal, 'success'=>'Product added to cart']);
    }

    public function updateCart(Request $request)
    {
    	$product_id = $request->input('product_id');
    	$product_qty = $request->input('product_qty');
		$product_variant = $request->input('product_variant');
    	
    	// $max_qty = Product::select('qty')->where('id',$product_id)->first();
    	    	
    	// if($product_qty > $max_qty->qty) {
    	//     $product_qty = $max_qty->qty;
    	// }

		if(strlen($product_variant) > 1){
			$product_id = $product_id.','.$product_variant;
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
		$variant = $request->input('variant');

		$delete_id = $id;

		if(strlen($variant) > 1){
			$delete_id = $id.'-'.implode('-',explode(',',$variant));
			$id = $id.','.$variant;
		}
    	$cart = session()->get('cart');
    	$total_qty = session()->get('total_qty');
    	$subTotal = session()->get('subTotal');
    	//removing element from cart
    	$total_qty -= $cart[$id]['qty'];
    	$subTotal -= $cart[$id]['total_price'];
    	unset($cart[$id]);
    	session(['cart' => $cart, 'total_qty' => $total_qty, 'subTotal' => $subTotal]);

    	return response()->json(['deleted_item' => $delete_id, 'total_qty' => $total_qty, 'subTotal' => $subTotal, 'success'=>'Product successfully removed from cart']);

    }
}