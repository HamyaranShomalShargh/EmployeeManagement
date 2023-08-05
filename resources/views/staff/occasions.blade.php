@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">

            <h5 class="iransans d-inline-block m-0">مناسبت ها</h5>
            <span>(ایجاد، جستجو و ویرایش)</span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-light">
                <i class="fa fa-circle-question fa-1-4x green-color"></i>
            </button>
            <a role="button" class="btn btn-sm btn-outline-light" href={{route("staff_idle")}}>
                <i class="fa fa-times fa-1-4x gray-color"></i>
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="page-content w-100 p-3">
        <div class="input-group mb-2">
            <button class="btn btn-outline-info d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_occasion_modal">
                <i class="fa fa-plus fa-1-4x me-1"></i>
                <span class="iransans create-button">مناسبت جدید</span>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام سرفصل">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table class="table table-striped sortArrowWhite">
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col" data-sortas="numeric"><span>شماره</span></th>
                        <th scope="col"><span>عنوان</span></th>
                        <th scope="col"><span>وضعیت انتشار</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($occasions as $occasion)
                        <tr class="iransans">
                            <td>{{ $occasion->id }}</td>
                            <td style="max-width: 250px;overflow: hidden;text-overflow: ellipsis">{{ $occasion->title }}</td>
                            <td>
                                @if($occasion->publish == 1)
                                    <i class="fa fa-check-circle green-color fa-1-4x"></i>
                                @elseif($occasion->publish == 0)
                                    <i class="fa fa-times-circle red-color fa-1-4x"></i>
                                @else
                                    <span class="iransans">نامشخص</span>
                                @endif
                            </td>
                            <td>{{ $occasion->user->name }}</td>
                            <td>{{ verta($occasion->created_at)->format("Y/m/d") }}</td>
                            <td>{{ verta($occasion->updated_at)->format("Y/m/d") }}</td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can("activation", "Occasions")
                                            <form class="w-100" id="activation-form-{{ $occasion->id }}" action="{{ route("Occasions.activation",$occasion->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                <button type="submit" form="activation-form-{{ $occasion->id }}" class="dropdown-item">
                                                    @if($occasion->publish == 1)
                                                        <i class="fa fa-eye-slash vertical-middle"></i>
                                                        <span>عدم انتشار</span>
                                                    @elseif($occasion->publish == 0)
                                                        <i class="fa fa-eye vertical-middle"></i>
                                                        <span>انتشار</span>
                                                    @endif
                                                </button>
                                            </form>
                                        @endcan
                                        @can("edit", "Occasions")
                                                <div class="dropdown-divider"></div>
                                            <a role="button" href="{{ route("Occasions.edit",$occasion->id) }}" class="dropdown-item">
                                                <i class="fa fa-edit"></i>
                                                <span class="iransans">ویرایش</span>
                                            </a>
                                        @endcan
                                        @can("delete","Occasions")
                                            <div class="dropdown-divider"></div>
                                            <form class="w-100" id="delete-form-{{ $occasion->id }}" action="{{ route("Occasions.destroy",$occasion->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $occasion->id }}" class="dropdown-item">
                                                    <i class="fa fa-trash"></i>
                                                    <span class="iransans">حذف</span>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade rtl" id="new_occasion_modal" tabindex="-1" aria-labelledby="new_occasion_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد مناسبت جدید</h5>
                </div>
                <div class="modal-body" style="max-height: 80vh;overflow-y: auto">
                    <form id="main_submit_form" class="p-3" action="{{ route("Occasions.store") }}" method="post" v-on:submit="submit_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    عنوان
                                </label>
                                <input class="form-control text-center iransans @error('title') is-invalid @enderror" type="text" name="title" value="{{ old("title") }}">
                                @error('title')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    شرح اسلاید
                                </label>
                                <input class="form-control text-center iransans @error('description') is-invalid @enderror" type="text" name="description" value="{{ old("description") }}">
                                @error('description')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label iransans">
                                    تصویر
                                    <strong class="red-color">*</strong>
                                </label>
                                <s-file-browser @error('image') class="is-invalid is-invalid-fake" :error_class="'is-invalid'" @enderror file_box_id="image" file_box_name="image" filename_box_id="image_box" :accept="['jpg','jpeg','png','tiff','bmp','svg']" size="3072000"></s-file-browser>
                                @error('image')
                                <span class="invalid-feedback iranyekan small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-save fa-1-2x me-1"></i>
                        <span class="iransans">ارسال و ذخیره</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal">
                        <i class="fa fa-times fa-1-2x me-1"></i>
                        <span class="iransans">انصراف</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if($errors->has('images'))
        <script>
            const modal = new bootstrap.Modal(document.getElementById("new_occasion_modal"));
            modal.show();
        </script>
    @endif
@endsection
