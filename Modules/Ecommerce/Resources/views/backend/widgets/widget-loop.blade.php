
@if($widget->name == 'text-widget')
<li><a class="toggle-collapse"> {{trans('file.Text')}} <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
    <div class="collapse">
        <hr>
        <form action="" class="">
            {{csrf_field()}}
            <label>{{trans('file.Title')}}</label>
            <input type="text" name="text_title" class="form-control" value="{{$widget->text_title}}">
            <label>{{trans('file.Text')}}</label>
            <textarea name="text_content" class="form-control">{{$widget->text_content}}</textarea>
            <div class="form-group mt-2">
                <button class="btn btn-sm btn-primary btn-save"><i class="dripicons-checkmark"></i></button>
                <a href="" class="btn btn-sm btn-danger btn-delete"><i class="dripicons-trash"></i></a>
                <input type="hidden" name="location" value="{{$widget->location}}">
                <input type="hidden" name="order" value="{{$widget->order}}">
                <input type="hidden" name="id" value="{{$widget->id}}">
                <input type="hidden" name="name" value="text-widget">
            </div>
        </form>
    </div>
</li>
@elseif($widget->name == 'custom-menu-widget')
<li><a class="toggle-collapse"> {{trans('file.Custom Menu')}} <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
    <div class="collapse">
        <hr>
        <form action="" class="">
            {{csrf_field()}}
            <label>{{trans('file.Title')}}</label>
            <input type="text" name="quick_links_title" class="form-control" value="{{$widget->quick_links_title}}">
            <label>{{trans('file.Select menu')}}</label>
            <select name="quick_links_menu" class="selectpicker form-control">
                @foreach($menus as $menu)
                <option @if($widget->quick_links_menu == $menu->id) selected @endif value="{{$menu->id}}">{{$menu->title}}</option>
                @endforeach
            </select>
            <div class="form-group mt-2">
                <button class="btn btn-sm btn-primary btn-save"><i class="dripicons-checkmark"></i></button>
                <a href="" class="btn btn-sm btn-danger btn-delete"><i class="dripicons-trash"></i></a>
                <input type="hidden" name="location" value="{{$widget->location}}">
                <input type="hidden" name="order" value="{{$widget->order}}">
                <input type="hidden" name="id" value="{{$widget->id}}">
                <input type="hidden" name="name" value="custom-menu-widget">
            </div>
        </form>
    </div>
</li>
@elseif($widget->name == 'site-features-widget')
<li><a class="toggle-collapse"> {{trans('file.Site Features')}} <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
    <div class="collapse">
        <hr>
        <form action="" class="" enctype="multipart/form-data">
            {{csrf_field()}}
            <label>{{trans('file.Title')}}</label>
            <input type="text" name="feature_title" class="form-control" value="{{$widget->feature_title}}">
            <label>{{trans('file.Text')}}</label>
            <textarea name="feature_secondary_title" class="form-control">{{$widget->feature_secondary_title}}</textarea>
            <label>{{trans('file.Icon')}}</label>
            <input type="file" name="feature_icon" class="form-control">
            <div class="form-group mt-2">
                <button class="btn btn-sm btn-primary btn-save"><i class="dripicons-checkmark"></i></button>
                <a href="" class="btn btn-sm btn-danger btn-delete"><i class="dripicons-trash"></i></a>
                <input type="hidden" name="location" value="{{$widget->location}}">
                <input type="hidden" name="order" value="{{$widget->order}}">
                <input type="hidden" name="id" value="{{$widget->id}}">
                <input type="hidden" name="name" value="site-features-widget">
            </div>
        </form>
    </div>
</li>
@elseif($widget->name == 'site-info-widget')
<li><a class="toggle-collapse"> {{trans('file.Site Information')}} <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
    <div class="collapse">
        <hr>
        <form action="" class="">
            {{csrf_field()}}
            <label>{{trans('file.Title')}}</label>
            <input type="text" name="site_info_name" class="form-control" value="{{$widget->site_info_name}}">
            <label>{{trans('file.Text')}}</label>
            <textarea name="site_info_description" class="form-control">{{$widget->site_info_description}}</textarea>
            <label>{{trans('file.Address')}}</label>
            <input type="text" name="site_info_address" class="form-control" value="{{$widget->site_info_address}}">
            <label>{{trans('file.Phone')}}</label>
            <input type="text" name="site_info_phone" class="form-control" value="{{$widget->site_info_phone}}">
            <label>{{trans('file.Email')}}</label>
            <input type="text" name="site_info_email" class="form-control" value="{{$widget->site_info_email}}">
            <label>{{trans('file.Hours')}}</label>
            <input type="text" name="site_info_hours" class="form-control" value="{{$widget->site_info_hours}}">
            <div class="form-group mt-2">
                <button class="btn btn-sm btn-primary btn-save"><i class="dripicons-checkmark"></i></button>
                <a href="" class="btn btn-sm btn-danger btn-delete"><i class="dripicons-trash"></i></a>
                <input type="hidden" name="location" value="{{$widget->location}}">
                <input type="hidden" name="order" value="{{$widget->order}}">
                <input type="hidden" name="id" value="{{$widget->id}}">
                <input type="hidden" name="name" value="site-info-widget">
            </div>
        </form>
    </div>
</li>
@elseif($widget->name == 'newsletter-widget')
<li><a class="toggle-collapse"> {{trans('file.Newsletter form')}} <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
    <div class="collapse">
        <hr>
        <form action="" class="">
            {{csrf_field()}}
            <label>{{trans('file.Title')}}</label>
            <input type="text" name="newsletter_title" class="form-control" value="{{$widget->newsletter_title}}">
            <label>{{trans('file.Text')}}</label>
            <textarea name="newsletter_text" class="form-control">{{$widget->newsletter_text}}</textarea>
            <div class="form-group mt-2">
                <button class="btn btn-sm btn-primary btn-save"><i class="dripicons-checkmark"></i></button>
                <a href="" class="btn btn-sm btn-danger btn-delete"><i class="dripicons-trash"></i></a>
                <input type="hidden" name="location" value="{{$widget->location}}">
                <input type="hidden" name="order" value="{{$widget->order}}">
                <input type="hidden" name="id" value="{{$widget->id}}">
                <input type="hidden" name="name" value="newsletter-widget">
            </div>
        </form>
    </div>
</li>
@endif