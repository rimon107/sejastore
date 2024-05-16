@extends('ecommerce::frontend.layout.main')

@section('title')  @endsection

@section('description')  @endsection

@section('content')
	<!--Breadcrumb Area start-->
    <div class="breadcrumb-section">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="page-title">{{ trans('file.Blog') }}</h1>
                    <ul>
                        <li><a href="{{ url('/') }}">{{trans('file.Home')}}</a></li>
                        <li class="active">{{ trans('file.Blog') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--Breadcrumb Area ends-->
    <!--Section starts-->
    <section class="pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    @foreach($blogs as $blog)
                    <a href="{{url('blog')}}/{{$blog->slug}}" class="mt-5">
                        @if(isset($blog->thumbnail))
                        <img class="post-thumb mb-5" src="" data-src="{{url('frontend/images/blog')}}/{{$blog->thumbnail}}" alt="{{$blog->title}}" />
                        @endif
                        <h2 class="text-center mb-5">{{$blog->title}}</h2>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!--Section ends-->
@endsection

@section('script')
<script type="text/javascript">
    "use strict";

    $(document).ready(function(){
        $('.post-thumb').each(function(){
            var img = $(this).data('src');
            $(this).attr('src', img);
        })
    })
</script>
@endsection