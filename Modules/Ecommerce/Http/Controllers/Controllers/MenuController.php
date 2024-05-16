<?php

namespace Modules\Ecommerce\Http\Controllers;

use Modules\Ecommerce\Entities\Menus;
use Modules\Ecommerce\Entities\MenuItems;
use App\Models\Category;
use Modules\Ecommerce\Entities\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Str;
use Session;
use DB;

class MenuController extends Controller
{

    public function index()
    {
        $menus = Menus::all();
        return view('ecommerce::backend.menu.index', compact('menus'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED')) {
            Session::flash('message', 'This feature is disable for demo!');
            Session::flash('type', 'error');
            return redirect()->back();
        }
        else {
            $data = $request->all();

            if (Menus::create($data)) {
                $newdata = Menus::orderby('id', 'DESC')->first();
                Session::flash('message', 'Menu saved successfully.');
                Session::flash('type', 'success');
                return redirect()->back();
            } else {
                Session::flash('message', 'Failed to save menu.');
                Session::flash('type', 'danger');
                return redirect()->back();
            }
        }
    }

    public function edit($id)
    {
        $menu = Menus::find($id);
        return $menu;
    }

    public function updateMenu(Request $request)
    {
        if(!env('USER_VERIFIED')) {
            Session::flash('message', 'This feature is disable for demo!');
            Session::flash('type', 'error');
            return redirect()->back();
        }
        else {
            $newdata = $request->all();
            $menu = Menus::findOrFail($request->menuid);
            $content = $request->data;
            $newdata = [];
            $newdata['content'] = json_encode($content);
            $menu->update($newdata);
        }
    }

    public function destroy(Request $request)
    {
        if(!env('USER_VERIFIED')) {
            Session::flash('message', 'This feature is disable for demo!');
            Session::flash('type', 'error');
            return redirect()->back();
        }
        else {
            MenuItems::where('menu_id', $request->id)->delete();
            Menus::findOrFail($request->id)->delete();
            return redirect('manage-menus')->with('success', 'Menu deleted successfully');
        }
    }
}
