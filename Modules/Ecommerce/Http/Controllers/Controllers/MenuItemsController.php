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

class MenuItemsController extends Controller
{

    public function index($id)
    {
        $pages = DB::table('pages')->where('status',1)->get();
        $categories = DB::table('categories')->where('is_active',1)->get();
        $collections = DB::table('collections')->where('status',1)->get();
        $brands = DB::table('brands')->where('is_active',1)->get();
        $menus = DB::table('menus')->get();

        $desiredMenu = DB::table('menus')->where('id', $id)->first();
        if ($desiredMenu->content != '') {
            $menuitems = json_decode($desiredMenu->content);
            $menuitems = $menuitems[0];
            foreach ($menuitems as $menu) {
                $item = DB::table('menu_items')->where('id', $menu->id)->first();
                $menu->title = $item->title;
                $menu->name = $item->name;
                $menu->slug = $item->slug;
                $menu->target = $item->target;
                $menu->type = $item->type;
                if (!empty($menu->children[0])) {
                    foreach ($menu->children[0] as $child) {
                        $child_item = DB::table('menu_items')->where('id', $child->id)->first();
                        $child->title = $child_item->title;
                        $child->name = $child_item->name;
                        $child->slug = $child_item->slug;
                        $child->target = $child_item->target;
                        $child->type = $child_item->type;
                    }
                }
            }
        } else {
            $menuitems = DB::table('menu_items')->where('menu_id', $desiredMenu->id)->get();
        }

        return view('ecommerce::backend.menu.menu-items', compact('pages', 'brands', 'categories', 'collections', 'menus', 'desiredMenu', 'menuitems', 'id'));
    }

    public function addCatToMenu(Request $request)
    {
        $data = $request->all();
        $menuid = $request->menuid;
        $ids = explode(',', $request->ids);
        $menu = Menus::findOrFail($menuid);
        if (strlen($menu->content) < 1) {
            foreach ($ids as $id) {
                $cat = Category::find($id);
                $data['title'] = $cat->name;
                $data['slug'] = $cat->slug;
                $data['type'] = 'category';
                $data['menu_id'] = $menuid;
                $data['updated_at'] = NULL;
                MenuItems::create($data);
            }
        } else {
            $olddata = json_decode($menu->content, true);
            foreach ($ids as $id) {
                $cat = Category::find($id);
                $data['title'] = $cat->name;
                $data['slug'] = $cat->slug;
                $data['type'] = 'category';
                $data['menu_id'] = $menuid;
                $data['updated_at'] = NULL;
                MenuItems::create($data);
            }
            foreach ($ids as $id) {
                $cat = Category::find($id);
                $array['title'] = $cat->name;
                $array['slug'] = $cat->name;
                $array['name'] = NULL;
                $array['type'] = 'category';
                $array['target'] = NULL;
                $array['id'] = MenuItems::where('slug', $array['slug'])->where('name', $array['name'])->where('type', $array['type'])->value('id');
                $array['children'] = [[]];
                array_push($olddata[0], $array);
                $oldata = json_encode($olddata);
                $menu->update(['content' => $olddata]);
            }
        }
    }

    public function addCollectionToMenu(Request $request)
    {
        $data = $request->all();
        $menuid = $request->menuid;
        $ids = explode(',', $request->ids);
        $menu = Menus::findOrFail($menuid);
        if (strlen($menu->content) < 1) {
            foreach ($ids as $id) {
                $col = DB::table('collections')->where('id',$id)->first();
                $data['title'] = $col->name;
                $data['slug'] = $col->slug;
                $data['type'] = 'collection';
                $data['menu_id'] = $menuid;
                $data['updated_at'] = NULL;
                MenuItems::create($data);
            }
        } else {
            $olddata = json_decode($menu->content, true);
            foreach ($ids as $id) {
                $col = DB::table('collections')->where('id',$id)->first();
                $data['title'] = $col->name;
                $data['slug'] = $col->slug;
                $data['type'] = 'collection';
                $data['menu_id'] = $menuid;
                $data['updated_at'] = NULL;
                MenuItems::create($data);
            }
            foreach ($ids as $id) {
                $col = DB::table('collections')->where('id',$id)->first();
                $array['title'] = $col->name;
                $array['slug'] = $col->name;
                $array['name'] = NULL;
                $array['type'] = 'collection';
                $array['target'] = NULL;
                $array['id'] = MenuItems::where('slug', $array['slug'])->where('name', $array['name'])->where('type', $array['type'])->value('id');
                $array['children'] = [[]];
                array_push($olddata[0], $array);
                $oldata = json_encode($olddata);
                $menu->update(['content' => $olddata]);
            }
        }
    }

    public function addBrandToMenu(Request $request)
    {
        $data = $request->all();
        $menuid = $request->menuid;
        $ids = explode(',', $request->ids);
        $menu = Menus::findOrFail($menuid);
        if (strlen($menu->content) < 1) {
            foreach ($ids as $id) {
                $page = DB::table('brands')->where('id',$id)->first();
                $data['title'] = $page->title;
                $data['slug'] = $page->slug;
                $data['type'] = 'brand';
                $data['menu_id'] = $menuid;
                $data['updated_at'] = NULL;
                MenuItems::create($data);
            }
        } else {
            $olddata = json_decode($menu->content, true);
            foreach ($ids as $id) {
                $page = DB::table('brands')->where('id',$id)->first();
                $data['title'] = $page->title;
                $data['slug'] = $page->slug;
                $data['type'] = 'brand';
                $data['menu_id'] = $menuid;
                $data['updated_at'] = NULL;
                MenuItems::create($data);
            }
            foreach ($ids as $id) {
                $page = DB::table('brands')->where('id',$id)->first();
                $array['title'] = $page->title;
                $array['slug'] = $page->slug;
                $array['name'] = NULL;
                $array['type'] = 'brand';
                $array['target'] = NULL;
                $array['id'] = MenuItems::where('slug', $array['slug'])->where('name', $array['name'])->where('type', $array['type'])->orderby('id', 'DESC')->value('id');
                $array['children'] = [[]];
                array_push($olddata[0], $array);
                $oldata = json_encode($olddata);
                $menu->update(['content' => $olddata]);
            }
        }
    }

    public function addPageToMenu(Request $request)
    {
        $data = $request->all();
        $menuid = $request->menuid;
        $ids = explode(',', $request->ids);
        $menu = Menus::findOrFail($menuid);
        if (strlen($menu->content) < 1) {
            foreach ($ids as $id) {
                $page = Page::find($id);
                $data['title'] = $page->page_name;
                $data['slug'] = $page->slug;
                $data['type'] = 'page';
                $data['menu_id'] = $menuid;
                $data['updated_at'] = NULL;
                MenuItems::create($data);
            }
        } else {
            $olddata = json_decode($menu->content, true);
            foreach ($ids as $id) {
                $page = Page::find($id);
                $data['title'] = $page->page_name;
                $data['slug'] = $page->slug;
                $data['type'] = 'page';
                $data['menu_id'] = $menuid;
                $data['updated_at'] = NULL;
                MenuItems::create($data);
            }
            foreach ($ids as $id) {
                $page = Page::find($id);
                $array['title'] = $page->page_name;
                $array['slug'] = $page->slug;
                $array['name'] = NULL;
                $array['type'] = 'page';
                $array['target'] = NULL;
                $array['id'] = MenuItems::where('slug', $array['slug'])->where('name', $array['name'])->where('type', $array['type'])->orderby('id', 'DESC')->value('id');
                $array['children'] = [[]];
                array_push($olddata[0], $array);
                $oldata = json_encode($olddata);
                $menu->update(['content' => $olddata]);
            }
        }
    }

    public function addCustomLink(Request $request)
    {
        $data = $request->all();
        $menuid = $request->menuid;
        $menu = Menus::findOrFail($menuid);
        if (strlen($menu->content) < 1) {
            $data['title'] = $request->link;
            $data['slug'] = $request->url;
            $data['type'] = 'custom';
            $data['menu_id'] = $menuid;
            $data['updated_at'] = NULL;
            MenuItems::create($data);
        } else {
            $olddata = json_decode($menu->content, true);
            $data['title'] = $request->link;
            $data['slug'] = $request->url;
            $data['type'] = 'custom';
            $data['menu_id'] = $menuid;
            $data['updated_at'] = NULL;
            MenuItems::create($data);
            $array = [];
            $array['title'] = $request->link;
            $array['slug'] = $request->url;
            $array['name'] = NULL;
            $array['type'] = 'custom';
            $array['target'] = NULL;
            $array['id'] = MenuItems::where('slug', $array['slug'])->where('name', $array['name'])->where('type', $array['type'])->orderby('id', 'DESC')->value('id');
            $array['children'] = [[]];
            array_push($olddata[0], $array);
            $oldata = json_encode($olddata);
            $menu->update(['content' => $olddata]);
        }
    }

    public function updateMenuItem(Request $request)
    {
        $data = $request->all();
        $item = MenuItems::findOrFail($request->id);
        $item->update($data);
        return redirect()->back();
    }

    public function deleteMenuItem($id, $key, $in)
    {
        $menuitem = MenuItems::findOrFail($id);
        $menu = Menus::where('id', $menuitem->menu_id)->first();
        if ($menu->content != '') {
            $data = json_decode($menu->content, true);
            $maindata = $data[0];
            if ($in == 'x') {
                unset($data[0][$key]);
                $newdata = json_encode($data);
                $menu->update(['content' => $newdata]);
            } else {
                unset($data[0][$key]['children'][0][$in]);
                $newdata = json_encode($data);
                $menu->update(['content' => $newdata]);
            }
        }
        $menuitem->delete();
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        MenuItems::where('menu_id', $request->id)->delete();
        Menus::findOrFail($request->id)->delete();
        return redirect('manage-menus')->with('success', 'Menu deleted successfully');
    }
}
