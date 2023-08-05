
@section('variables')
    <script>
        const allowed_organizations = @json($organizations);
    </script>
    @if(old("excel_columns") != null)
        <script>
            const excel_columns_data = @json(json_decode(old("excel_columns"),true));
        </script>
    @endif
@endsection@extends("staff.staff_dashboard")
@section('header')
    <div class="h-100 bg-white iransans p-3 border-3 border-bottom d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">

            <h5 class="iransans d-inline-block m-0">قالب فیش حقوقی</h5>
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
            <button class="btn btn-outline-info d-flex flex-row align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#new_template_modal">
                <i class="fa fa-plus fa-1-4x me-1"></i>
                <span class="iransans create-button">قالب جدید</span>
            </button>
            <input type="text" class="form-control text-center iransans" placeholder="جستجو با نام قالب">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search fa-1-2x"></i></span>
        </div>
        <div id="table-scroll-container">
            <div id="table-scroll" class="table-scroll">
                <table>
                    <thead class="bg-menu-dark white-color">
                    <tr class="iransans">
                        <th scope="col"><span>شماره</span></th>
                        <th scope="col"><span>قرارداد</span></th>
                        <th scope="col"><span>توسط</span></th>
                        <th scope="col"><span>تاریخ ثبت</span></th>
                        <th scope="col"><span>تاریخ ویرایش</span></th>
                        <th scope="col"><span>عملیات</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td><span class="iransans">{{ $template->id }}</span></td>
                            <td><span class="iransans">{{ $template->contract->name }}</span></td>
                            <td><span class="iransans">{{ $template->user->name }}</span></td>
                            <td><span class="iransans">{{ verta($template->cretaed_at)->format("Y/m/d") }}</span></td>
                            <td><span class="iransans">{{ verta($template->updated_at)->format("Y/m/d") }}</span></td>
                            <td class="position-relative">
                                <div class="dropdown table-functions iransans">
                                    <a class="table-functions-button dropdown-toggle border-0 iransans info-color" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cog fa-1-2x"></i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can("edit", "PaySlipTemplates")
                                            <a role="button" href="{{ route("PaySlipTemplates.edit",$template->id) }}" class="dropdown-item">
                                                <i class="fa fa-edit"></i>
                                                <span class="iransans">ویرایش</span>
                                            </a>
                                        @endcan
                                        @can("delete","PaySlipTemplates")
                                            <div class="dropdown-divider"></div>
                                            <form class="w-100" id="delete-form-{{ $template->id }}" action="{{ route("PaySlipTemplates.destroy",$template->id) }}" method="POST" v-on:submit="submit_form">
                                                @csrf
                                                @method("Delete")
                                                <button type="submit" form="delete-form-{{ $template->id }}" class="dropdown-item">
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
    <div class="modal fade rtl" id="new_template_modal" tabindex="-1" aria-labelledby="new_template_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iransans">ایجاد قالب جدید</h5>
                </div>
                <div class="modal-body" style="max-height: 80vh;overflow-y: auto">
                    <form id="main_submit_form" class="p-2" action="{{ route("PaySlipTemplates.store") }}" method="POST" data-json="excel_columns" v-on:submit="submit_form">
                        @csrf
                        <div class="row">
                            <div v-if="excel_columns.length > 0" class="col-12 mb-3">
                                <label class="form-label iransans">انتخاب قرارداد</label>
                                <tree-select :branch_node="true" @error('contract_id') :validation_error="true" @enderror @contract_deselected="ContractDeselected" @multi_contract_selected="MultiContractSelected" dir="rtl" :is_multiple="true" :placeholder="'انتخاب کنید'" :database="organizations"></tree-select>
                                <select class="@error('contract_id') is-invalid @enderror" hidden multiple v-model="multi_contract_id" name="contract_id[]">
                                    <option v-for="contract in multi_contract_id" :value="contract"></option>
                                </select>
                                @error('contract_id')
                                <span class="invalid-feedback iransans small_font" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label v-if="excel_columns.length === 0" class="form-label iransans">تعداد کل ستون های مورد نیاز</label>
                                <label v-if="excel_columns.length > 0" class="form-label iransans">افزایش / کاهش (به انتها و از انتها)</label>
                                <div v-if="excel_columns.length === 0" class="input-group">
                                    <input type="number" class="form-control text-center iransans" v-model="last_excel_column">
                                    <button type="button" class="btn btn-sm btn-primary iransans input-group-append ps-5 pe-5" v-on:click="ExcelColumnsCreation">
                                        ادامه
                                    </button>
                                </div>
                                <div v-if="excel_columns.length > 0" class="input-group">
                                    <input type="number" maxlength="702" class="form-control text-center iransans" v-model="add_remove_excel_column">
                                    <button type="button" class="btn btn-sm btn-outline-primary input-group-append ps-3 pe-3" v-on:click="ExcelColumnsNumber('increase')">
                                        <i class="fa fa-plus fa-1-4x"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger input-group-append ps-3 pe-3" v-on:click="ExcelColumnsNumber('decrease')">
                                        <i class="fa fa-minus fa-1-4x"></i>
                                    </button>
                                </div>
                            </div>
                            <div v-if="excel_columns.length > 0" class="col-12 mb-3">
                                <label class="form-label iransans">مشخص نمودن ستون مخصوص کد ملی پرسنل</label>
                                <select class="form-control iransans selectpicker-select" data-live-search="true" v-model="excel_column_index" data-size="10" name="national_code_index">
                                    <option v-for="(column,index) in excel_columns" :key="index" :value="index">
                                        ستون
                                        @{{ column.column }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="excel_columns.length > 0" class="col-12">
                                <div id="table-scroll-container">
                                    <div id="table-scroll" class="table-scroll fixed" style="max-height: 50vh">
                                        <table>
                                            <thead class="bg-dark white-color iransans">
                                            <tr>
                                                <th scope="col">ستون</th>
                                                <th scope="col">عنوان</th>
                                                <th scope="col">نوع</th>
                                                <th scope="col">خصوصیات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr class="iransans" v-for="(column,index) in excel_columns" :key="index">
                                                <td><span style="font-size:18px;font-weight: 700">@{{ column.column }}</span></td>
                                                <td>
                                                    <input :disabled="column.ignore || parseInt(excel_column_index) === index" type="text" class="form-control iransans text-center" placeholder="عنوان" v-model="parseInt(excel_column_index) === index ? column.title = 'کد ملی' : column.title = ''">
                                                </td>
                                                <td>
                                                    <select :disabled="column.ignore || parseInt(excel_column_index) === index" class="form-control iransans" v-model="column.type">
                                                        <option value="information">اطلاعات پرسنل</option>
                                                        <option value="function">کارکــرد</option>
                                                        <option value="advantage">مزایـــا</option>
                                                        <option value="deduction">کـسـورات</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <input :disabled="column.ignore || parseInt(excel_column_index) === index" class="form-check-input" type="checkbox" :id="`isNumber${index}`" :value="parseInt(excel_column_index) !== index" v-model="parseInt(excel_column_index) !== index ? column.isNumber = true : column.isNumber = false">
                                                        <label class="form-check-label iransans" :for="`isNumber${index}`">مقدار عددی</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input :disabled="parseInt(excel_column_index) === index" class="form-check-input" type="checkbox" :id="`isIgnored${index}`" :value="false" v-model="column.ignore">
                                                        <label class="form-check-label iransans" :for="`isIgnored${index}`">نادیده گرفتن</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-menu">
                    <button v-if="excel_columns.length > 0" type="submit" form="main_submit_form" class="btn btn-success submit_button">
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
    @if($errors->has('contract_id') || $errors->has('excel_columns'))
        <script defer>
            $(document).ready(function (){
                let modal = new bootstrap.Modal(document.getElementById("new_template_modal"), {});
                modal.show();
            });
        </script>
    @endif
@endsection
