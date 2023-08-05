@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">

            <h5 class="iransans d-inline-block m-0">سازمان ها</h5>
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
            <button class="btn btn-outline-primary d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_organization_modal">
                <i class="fa fa-plus fa-1-4x me-1"></i>
                <span class="iransans create-button">سازمان جدید</span>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table>
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>نام</span></th>
                        <th scope="col"><span>وضعیت</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($organizations as $organization)
                        <tr>
                            <td><span class="iransans">{{ $organization->id }}</span></td>
                            <td><span class="iransans">{{ $organization->name }}</span></td>
                            <td>
                                <span class="iransans">
                                     @if($organization->inactive == 1)
                                        <i class="far fa-times-circle red-color fa-1-4x vertical-middle"></i>
                                    @elseif($organization->inactive == 0)
                                        <i class="far fa-check-circle green-color fa-1-4x vertical-middle"></i>
                                    @endif
                                </span>
                            </td>
                            <td><span class="iransans">{{ $organization->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($organization->cretaed_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($organization->updated_at)->format("Y/m/d") }}</span></td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can("activation", "Organizations")
                                        <form class="w-100" id="activation-form-{{ $organization->id }}" action="{{ route("Organizations.activation",$organization->id) }}" method="POST" v-on:submit="submit_form">
                                            @csrf
                                            <button type="submit" form="activation-form-{{ $organization->id }}" class="dropdown-item">
                                                @if($organization->inactive == 0)
                                                    <i class="fa fa-lock"></i>
                                                    <span>غیر فعال سازی</span>
                                                @elseif($organization->inactive == 1)
                                                    <i class="fa fa-lock-open"></i>
                                                    <span>فعال سازی</span>
                                                @endif
                                            </button>
                                        </form>
                                        @endcan
                                        @can("edit", "Organizations")
                                            <div class="dropdown-divider"></div>
                                            <a role="button" href="{{ route("Organizations.edit",$organization->id) }}" class="dropdown-item">
                                                <i class="fa fa-edit"></i>
                                                <span class="iransans">ویرایش</span>
                                            </a>
                                        @endcan
                                        @can("delete","Organizations")
                                            <div class="dropdown-divider"></div>
                                            <form class="w-100" id="delete-form-{{ $organization->id }}" action="{{ route("Organizations.destroy",$organization->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $organization->id }}" class="dropdown-item">
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
    <div class="modal fade rtl" id="new_organization_modal" tabindex="-1" aria-labelledby="new_organization_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title iransans">ایجاد سازمان جدید</h6>
                </div>
                <div class="modal-body">
                    <form id="main_submit_form" class="p-3" action="{{ route("Organizations.store") }}" method="POST" enctype="multipart/form-data" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label iransans">
                                    نام
                                    <strong class="red-color">*</strong>
                                </label>
                                <input class="form-control text-center iransans @error('name') is-invalid @enderror" type="text" name="name" value="{{ old("name") }}">
                                @error('name')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button type="submit" form="main_submit_form" class="btn btn-success submit_button">
                        <i class="submit_button_icon fa fa-check fa-1-2x me-1"></i>
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
    @if($errors->has('name'))
        <script defer>
            $(document).ready(function (){
                let modal = new bootstrap.Modal(document.getElementById("new_organization_modal"), {});
                modal.show();
            });
        </script>
    @endif
@endsection
