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

    public function store(Request $request)
    {
        $input = $request->except('image');
        
        $input['name'] = $input['widget_name'];

        if($input['name'] == 'category-slider-widget'){
            $input['category_slider_ids'] = implode(',',$request->category_slider_ids);
        }

        if($input['name'] == 'brand-slider-widget'){
            $input['brand_slider_ids'] = implode(',',$request->brand_slider_ids);
        }

        if($input['name'] == 'tab-product-category-widget'){
            $input['product_category_id'] = implode(',',$request->product_category_id);
        }

        $widget = PageWidgets::create($input);

        return $widget->id;

    }

    public function update(Request $request)
    {
        $data = $request->except('image');


        if($data['widget_name'] == 'category-slider-widget'){
            $data['category_slider_ids'] = implode(',',$request->category_slider_ids);
        }

        if($data['widget_name'] == 'brand-slider-widget'){
            $data['brand_slider_ids'] = implode(',',$request->brand_slider_ids);
        }

        if($data['widget_name'] == 'tab-product-category-widget'){
            $data['product_category_id'] = implode(',',$request->product_category_id);
        }

        $widget = PageWidgets::find($request->id);
        $widget->update($data);
        
        if(isset($request->image))
        {
            $image = $request->image;
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = date("Ymdhis") . ($request->order);
            if(!config('database.connections.saleprosaas_landlord')) {
                $imageName = $imageName . '.' . $ext;
                $image->move('public/frontend/images/features', $imageName);
                $img = Image::make('public/frontend/images/features/'. $imageName)->fit(100, 100)->save('public/frontend/images/features/'. $imageName, 100);

            }
            else {
                $imageName = $this->getTenantId() . '_' . $imageName . '.' . $ext;
                $image->move('public/frontend/images/features', $imageName);
                $img = Image::make('public/frontend/images/features/'. $imageName)->fit(100, 100)->save('public/frontend/images/features/'. $imageName, 100);

            }
            $widget->image = $imageName;
            $widget->save();
        }

        return response()->json(['id' => $widget->id]);
    }

    public function delete($id)
    {
        $widget = PageWidgets::where('id',$id)->delete();

        // if(isset($widget->icon))
        // {
        //     $this->fileDelete('frontend/images/features/', $widget->icon);
        // }

        return redirect()->back();
    }
}
