@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i v-if="sidebar_toggle" class="sidebar-toggle fa fa-bars fa-1-6x me-4" v-cloak
               v-on:click="toggle_sidebar('open')"></i>
            <h5 class="iransans d-inline-block m-0 d-flex align-items-center">
                اخبار
                <span class="vertical-middle ms-2">(ویرایش)</span>
            </h5>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100 p-3">
        <form id="update_form" class="p-3" action="{{ route("News.update",$news->id) }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
            @csrf
            @method('put')
            <div class="row">
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        عنوان
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('title') is-invalid @enderror" type="text" name="title" value="{{ $news->title }}">
                    @error('title')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        شرح اسلاید
                    </label>
                    <input class="form-control text-center iransans @error('topic') is-invalid @enderror" type="text" name="topic" value="{{ $news->topic }}">
                    @error('topic')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        شرح مختصر
                        <strong class="red-color">*</strong>
                    </label>
                    <input class="form-control text-center iransans @error('brief') is-invalid @enderror" type="text" name="brief" value="{{ $news->brief }}">
                    @error('brief')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        شرح کامل
                    </label>
                    <textarea class="form-control iransans @error('description') is-invalid @enderror" id="editor" name="description">
                        {{ $news->description }}
                    </textarea>
                    @error('description')
                    <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        تصویر اصلی
                        <strong class="red-color">*</strong>
                    </label>
                    <s-file-browser @error('main_image') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror file_box_id="main_image" file_box_name="main_image" filename_box_id="main_image_box" :accept="['jpg','jpeg','png','tiff','bmp','svg']" size="3072000"></s-file-browser>
                    @error('main_image')
                    <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        تصاویر فرعی
                    </label>
                    <m-file-browser @error('images') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror file_box_id="images" file_box_name="images[]" filename_box_id="images_box" :accept="['jpg','jpeg','png','tiff','bmp','svg']" size="3072000"></m-file-browser>
                    @error('images')
                    <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3 col-12">
                    <label class="form-label iransans">
                        تصاویر فرعی بارگذاری شده
                    </label>
                    <div class="row row-cols-2 row-cols-lg-6">
                        @forelse($images as $image)
                            @if(explode("/",$image)[1] != $news->image)
                                <div class="col mb-2">
                                    <div class="position-relative news-image-wrapper">
                                        <div class="news-image-cover">
                                            <a href="{{route("News.delete_image",["id"=>explode("/",$image)[0], "file"=>explode("/",$image)[1]])}}" role="button" class="btn btn-outline-light">
                                                <i class="far fa-trash-can fa-2x"></i>
                                            </a>
                                        </div>
                                        <img alt="همیاران شمال شرق" class="news-image img-fluid" src="{{asset("storage/news/$image")}}">
                                    </div>
                                </div>
                            @endif
                        @empty
                            <span class="iransans bold-font">تصویری بارگذاری نشده است</span>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-12 text-center pt-4 pb-2 position-sticky">
                    <button type="submit" form="update_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
                        <span class="iransans">ارسال و ویرایش</span>
                    </button>
                    <a role="button" href="{{ route("News.index") }}"
                       class="btn btn-outline-secondary iransans">
                        <i class="fa fa-arrow-turn-right fa-1-2x me-1"></i>
                        <span class="iransans">بازگشت به لیست</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
