<?php

namespace Modules\Ecommerce\Http\Controllers;

use Modules\Ecommerce\Entities\Page;
use Modules\Ecommerce\Entities\PageWidgets;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Session;
use DB;

class PageWidgetController extends Controller
{
    use \App\Traits\TenantInfo;
    
    public function store(Request $request)
    {

        $input = $request->except('image');
        
        $input['name'] = $input['widget_name'];
        
        if($input['name'] == 'category-slider-widget'){
            if(isset($request->category_slider_ids)) {
                $input['category_slider_ids'] = implode(',',$request->category_slider_ids);
            }else{
                $input['category_slider_ids'] = NULL;
            }
        }

        if($input['name'] == 'brand-slider-widget'){
            if(isset($request->brand_slider_ids)) {
                $input['brand_slider_ids'] = implode(',',$request->brand_slider_ids);
            }else{
                $input['brand_slider_ids'] = NULL;
            }
        }

        if($input['name'] == 'tab-product-collection-widget'){
            if(isset($request->tab_product_collection_ids)) {
                $input['tab_product_collection_id'] = implode(',',$request->tab_product_collection_ids);
            }else{
                $input['tab_product_collection_id'] = NULL;
            }
        } 

        $widget = PageWidgets::create($input);

        return $widget->id;

    }

    public function update(Request $request)
    {
        // if(!env('USER_VERIFIED')){
        //     return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        // }

        $data = $request->except('image');

        if($data['widget_name'] == 'category-slider-widget'){
            $data['category_slider_ids'] = implode(',',$request->category_slider_ids);
        }

        if($data['widget_name'] == 'brand-slider-widget'){
            $data['brand_slider_ids'] = implode(',',$request->brand_slider_ids);
        }

        if($data['widget_name'] == 'tab-product-collection-widget'){
            $data['tab_product_collection_id'] = implode(',',$request->tab_product_collection_ids);
        }

        $widget = PageWidgets::find($request->id);
        $widget->update($data);
        
        // if(isset($request->image))
        // {
        //     $image = $request->image;
        //     $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        //     $imageName = date("Ymdhis") . ($request->order);
        //     if(!config('database.connections.saleprosaas_landlord')) {
        //         $imageName = $imageName . '.' . $ext;
        //         $image->move('public/frontend/images/features', $imageName);
        //         $img = Image::make('public/frontend/images/features/'. $imageName)->fit(100, 100)->save('public/frontend/images/features/'. $imageName, 100);

        //     }
        //     else {
        //         $imageName = $this->getTenantId() . '_' . $imageName . '.' . $ext;
        //         $image->move('public/frontend/images/features', $imageName);
        //         $img = Image::make('public/frontend/images/features/'. $imageName)->fit(100, 100)->save('public/frontend/images/features/'. $imageName, 100);

        //     }
        //     $widget->image = $imageName;
        //     $widget->save();
        // }

        return response(['id' => $widget->id]);
    }

    public function delete($id)
    {
        if(!env('USER_VERIFIED')){
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }
        
        $widget = PageWidgets::where('id',$id)->delete();

        // if(isset($widget->icon))
        // {
        //     $this->fileDelete('frontend/images/features/', $widget->icon);
        // }

        return redirect()->back();
    }
}
