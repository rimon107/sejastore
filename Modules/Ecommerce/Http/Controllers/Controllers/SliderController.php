<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;

use Modules\Ecommerce\Entities\Sliders;
use Intervention\Image\Facades\Image;
use Session;
use DB;

class SliderController extends Controller
{

    public function slidersShow()
    {
        $sliders = DB::table('sliders')->orderBy('order', 'asc')->get();

        return view ('ecommerce::backend.slider.sliders', compact('sliders'));
    }

    public function slidersCreate(Request $request)
    {
       
        $titles = $request->input('title');
        $links = $request->input('link');
        $orders = $request->input('order');
        // dd($request->all());  
        
        foreach($orders as $key=>$order) {  
            
            $data = [
                'order'  => $orders[$key],
                'title'  => $titles[$key],
                'link'   => $links[$key],
            ];

            $sliders = Sliders::create($data);

            $id = $sliders->id;  

            if(isset($request->image1[$key])) { 
                $image1 = $request->image1[$key];
                if ($image1) {
                    $ext = pathinfo($image1->getClientOriginalName(), PATHINFO_EXTENSION);
                    $imageName = $id . '.' . $ext;
                    //return $imageName;  
                    $image1->move('public/frontend/images/slider/desktop', $imageName);
                    $img_lg = Image::make('public/frontend/images/slider/desktop/'. $imageName)->fit(1090, 460)->save('public/frontend/images/slider/desktop/'. $imageName);

                    $data['image1'] = $imageName;
                }

                $sliders->image1 = $data['image1'];
                $sliders->save();
            }

            if(isset($request->image2[$key])) { 
                $image2 = $request->image2[$key];
                if ($image2) {
                    $ext = pathinfo($image2->getClientOriginalName(), PATHINFO_EXTENSION);
                    $imageName = $id . '.' . $ext;
                    //return $imageName;  
                    $image2->move('public/frontend/images/slider/tab', $imageName);
                    $img_lg = Image::make('public/frontend/images/slider/tab/'. $imageName)->fit(1090, 460)->save('public/frontend/images/slider/tab/'. $imageName);

                    $data['image2'] = $imageName;
                }

                $sliders->image2 = $data['image2'];
                $sliders->save();
            }

            if(isset($request->image3[$key])) { 
                $image3 = $request->image3[$key];
                if ($image3) {
                    $ext = pathinfo($image3->getClientOriginalName(), PATHINFO_EXTENSION);
                    $imageName = $id . '.' . $ext;
                    //return $imageName;  
                    $image3->move('public/frontend/images/slider/mobile', $imageName);
                    $img_lg = Image::make('public/frontend/images/slider/mobile/'. $imageName)->fit(650, 460)->save('public/frontend/images/slider/mobile/'. $imageName);

                    $data['image3'] = $imageName;
                }

                $sliders->image3 = $data['image3'];
                $sliders->save();
            }

        }

        Session::flash('message', 'Sliders inserted successfully.');
        Session::flash('type', 'success');

        return redirect()->back();
    }

    public function slidersDelete($id)
    {
        if(!env('USER_VERIFIED')) {
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }
        else {

            $slide = Sliders::find($id);
            $slide->delete();

            $this->fileDelete('frontend/images/slider/desktop/', $slide->image1);
            $this->fileDelete('frontend/images/slider/tab/', $slide->image2);
            $this->fileDelete('frontend/images/slider/mobile/', $slide->image3);

            Session::flash('message', 'Slider deleted successfully.');
            Session::flash('type', 'success');

            return redirect()->back();
        }
    }

}
