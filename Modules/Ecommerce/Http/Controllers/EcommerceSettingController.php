<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;

use Intervention\Image\Facades\Image;
use Modules\Ecommerce\Entities\Page;
use App\Traits\CacheForget;
use Session;
use Cache;
use DB;

class EcommerceSettingController extends Controller
{
    use CacheForget;

    public function index()
    {
        $settings = DB::table('ecommerce_settings')->first();
        $pages = DB::table('pages')->where('status',1)->get();
        //return dd($pages);
        $warehouse_list = DB::table('warehouses')->where('is_active',1)->get();
        $biller_list = DB::table('billers')->where('is_active',1)->get();

        return view ('ecommerce::backend.settings.index', compact('settings','pages','warehouse_list','biller_list'));
    }

    public function update(Request $request)
    {
        if(!env('USER_VERIFIED')){
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }
        
        $data = [
            'site_title'          => $request->site_title,
            'store_phone'         => $request->store_phone,
            'store_email'         => $request->store_email,
            'store_address'       => $request->store_address,
            'home_page'           => $request->home_page,
            'warehouse_id'        => $request->warehouse_id,
            'biller_id'           => $request->biller_id,
            'contact_form_email'  => $request->contact_form_email,
            'free_shipping_from'  => $request->free_shipping_from,
            'flat_rate_shipping'  => $request->flat_rate_shipping,
            'custom_css'          => $request->custom_css,
            'custom_js'           => $request->custom_js,
            'chat_code'           => $request->chat_code,
            'analytics_code'      => $request->analytics_code,
            'fb_pixel_code'       => $request->fb_pixel_code,
            'gift_card'           => $request->gift_card,
        ];

        if(!isset($request->gift_card)){
            $data['gift_card'] = 0;
        }

        if(isset($request->logo)){
            $this->validate($request, [
                'logo' => 'image|mimes:jpg,jpeg,png,gif|max:100000',
            ]);
        }

        if(isset($request->favicon)){
            $this->validate($request, [
                'favicon' => 'image|mimes:jpg,jpeg,png,gif|max:100000',
            ]);
        }
       
        if(isset($request->logo)) { 
            $logo = $request->logo;
            if ($logo) {
                $ext = pathinfo($logo->getClientOriginalName(), PATHINFO_EXTENSION);
                $imageName =  date("Ymdhis") . '.' . $ext;
                $logo->move('public/frontend/images/', $imageName);
                $img_lg = Image::make('public/frontend/images/'. $imageName)
                          ->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                          })
                          ->save('public/frontend/images/'. $imageName);


                $data['logo'] = $imageName;
            }

        }

        if(isset($request->favicon)) { 
            $favicon = $request->favicon;
            if ($favicon) {
                $ext = pathinfo($favicon->getClientOriginalName(), PATHINFO_EXTENSION);
                $imageName = date("Ymdhis") . '.' . $ext;
                //return $imageName;  
                $favicon->move('public/frontend/images/', $imageName);
                $img_lg = Image::make('public/frontend/images/'. $imageName)->fit(50, 50)->save('public/frontend/images/'. $imageName);

                $data['favicon'] = $imageName;
            }
        }
        if(isset($request->checkout_pages))
            $data['checkout_pages'] = json_encode($request->checkout_pages);

        $setting = DB::table('ecommerce_settings')->first();
        if(isset($setting->id)){
            DB::table('ecommerce_settings')->where('id', 1)->update($data);
        }else{
            DB::table('ecommerce_settings')->insert($data);
        }

        Session::flash('message', 'Settings updated successfully.');
        Session::flash('type', 'success');

        $this->cacheForget('ecommerce_setting');

        return redirect()->back();
    }

    public function gateway()
    {
        $payment_gateways = DB::table('external_services')->where('type','payment')->get();

        return view ('ecommerce::backend.settings.payment-gateways', compact('payment_gateways'));
    }

    public function gatewayUpdate(Request $request)
    {
        $gateways = DB::table('external_services')->where('type','payment')->get();
        
        $pgs = $request->input('pg_name');
        $actives = $request->input('active');

        foreach($pgs as $index=>$pg){
            $gateway = $gateways->where('name',$pg)->first();
            $lines = explode(';',$gateway->details);
            $keys = explode(',', $lines[0]);

            $vals = [];
            foreach($keys as $key){
                $para = $pg.'_'.str_replace(' ','_',$key);
                $val = $request->$para;
                array_push($vals,$val);
            }
            $lines[1] = implode(',',$vals);
            $details = $lines[0].';'.$lines[1];

            DB::table('external_services')->where('name',$pg)->update(['details'=>$details,'active'=>$actives[$index]]);
        }

        Session::flash('message', 'Payment gateways updated successfully.');
        Session::flash('type', 'success');

        $this->cacheForget('ecommerce_setting');

        return redirect()->back();
    }

}
