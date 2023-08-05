<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                ایجاد درخواست جدید
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">نوع درخواست</label>
                </span>
                <div class="fieldset-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-check-inline">
                                <input class="form-check-input" type="radio" name="operation_type" id="view" value="view" v-model="operation_type">
                                <label class="form-check-label iransans" for="view">
                                    به صورت آزمایشی
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <input class="form-check-input" type="radio" name="operation_type" id="save" value="save" v-model="operation_type">
                                <label class="form-check-label iransans" for="save">
                                    ثبت نهایی درخواست
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <select id="applications" class="form-control iransans" v-model="application_type">
                                <option v-for="application in $root.applications" :key="application.id" :value="application.application_form_type">{{ application.name }}</option>
                            </select>
                        </div>
                        <div class="col-12 mb-2" v-if="this.operation_type === 'save' && application_type === 'LoanPaymentConfirmationApplication' || application_type === 'EmploymentCertificateApplication'">
                            <label class="form-label iransans">نهاد درخواست کننده</label>
                            <input id="recipient" type="text" class="form-control iransans text-center" v-model="recipient">
                        </div>
                        <div class="col-12 mb-2" v-if="this.operation_type === 'save' && application_type === 'LoanPaymentConfirmationApplication'">
                            <label class="form-label iransans">نام متقاضی وام (در صورت ضمانت)</label>
                            <input type="text" class="form-control iransans text-center" v-model="borrower">
                        </div>
                        <div class="col-12 mb-2" v-if="this.operation_type === 'save' && application_type === 'LoanPaymentConfirmationApplication'">
                            <label class="form-label iransans">مبلغ وام</label>
                            <input id="loan_amount" type="text" class="form-control iransans text-center" v-model="loan_amount">
                        </div>
                        <div class="col-12" v-if="this.operation_type === 'save'">
                            <label class="form-label iransans">توضیحات</label>
                            <textarea class="form-control iransans text-center" v-model="comment"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button v-if="reference !== null && data !== null && application_type !== null" type="button" class="btn btn-success" v-on:click="MakeApplications">
                <i class="far fa-file-circle-plus fa-1-2x me-1 vertical-middle"></i>
                <span class="iransans">ایجاد درخواست</span>
            </button>
            <button v-if="view_link !== null" type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#pdf_viewer_modal" v-on:click="PdfViewer">
                <i class="fas fa-file-pdf fa-1-2x me-2 vertical-middle"></i>
                <span class="iransans">مشاهده فایل PDF</span>
            </button>
            <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" v-on:click="$root.$data.employee_operation_type=''">
                <i class="fa fa-times fa-1-2x me-1"></i>
                <span class="iransans">بستن</span>
            </button>
        </div>
    </div>
</template>

<script>
import route from "ziggy-js";

export default {
    name: "EmployeeApplicationsModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            operation_type: "view",
            application_type: null,
            view_link: null,
            recipient: "",
            borrower: "",
            loan_amount: "",
            comment: ""
        }
    },
    mounted() {
        $(document).ready(() => {
            $("#applications").selectpicker();
        })
    },
    methods:{
        ReferenceChecked(ref){
            this.reference = ref;
        },
        ReferenceSetup(ref){
            this.reference = ref.type;
            this.data = ref.target;
        },
        GetRoute(routeName, parameter) {
            return route(routeName, parameter);
        },
        PdfViewer(){
            const self = this;
            $('#pdf_viewer').attr('src',self.view_link);
        },
        MakeApplications(){
            const self = this;
            $("*").removeClass("is-invalid");
            if (this.application_type !== null && this.application_type !== "") {
                if (this.operation_type === "save" && this.application_type === "LoanPaymentConfirmationApplication" && this.recipient === "" && this.loan_amount === "" || this.operation_type === "save" && this.application_type === "EmploymentCertificateApplication" && this.recipient === ""){
                    if (self.recipient === "")
                        $("#recipient").removeClass("is-invalid").addClass("is-invalid");
                    if (self.recipient === "")
                        $("#loan_amount").removeClass("is-invalid").addClass("is-invalid");
                }
                else {
                    bootbox.confirm({
                        message: "آیا برای انجام عملیات اطمینان دارید؟",
                        closeButton: false,
                        buttons: {
                            confirm: {
                                label: 'بله',
                                className: 'btn-success',
                            },
                            cancel: {
                                label: 'خیر',
                                className: 'btn-danger',
                            }
                        },
                        callback: function (result) {
                            if (result === true) {
                                self.$root.$data.show_loading = true;
                                bootbox.hideAll();
                                let data = new FormData();
                                data.append("reference", self.reference);
                                data.append("application_type", self.application_type);
                                data.append("operation_type", self.operation_type);
                                data.append("recipient", self.recipient);
                                data.append("borrower", self.borrower);
                                data.append("loan_amount", self.loan_amount);
                                data.append("comment", self.comment);
                                if (self.data !== null) {
                                    switch (self.reference) {
                                        case "organization":
                                            data.append("contract_id", self.data);
                                            break;
                                        case "group":
                                            data.append("group_id", self.data);
                                            break;
                                        case "custom":
                                            data.append("employees", JSON.stringify(self.data));
                                            break;
                                        case "individual":
                                            data.append("employee_id", self.data);
                                    }
                                }
                                axios.post(route("EmployeesManagement.item_batch_application"), data)
                                    .then(function (response) {
                                        self.$root.refresh_selects();
                                        self.$root.$data.show_loading = false;
                                        if (response?.data) {
                                            if (response.data.data) {
                                                self.$root.$data.user_allowed_contracts = response.data.data.contracts;
                                                self.$root.$data.user_allowed_groups = response.data.data.groups;
                                                if (response.data?.data?.view)
                                                    self.view_link = response.data.data.view;
                                            }
                                            switch (response.data["result"]) {
                                                case "success": {
                                                    alertify.notify(response.data["message"], 'success', "5");
                                                    break;
                                                }
                                                case "warning": {
                                                    alertify.notify(response.data["message"], 'warning', "20");
                                                    break;
                                                }
                                                case "fail": {
                                                    alertify.notify(response.data["message"], 'error', "30");
                                                    break;
                                                }
                                            }
                                        }
                                    }).catch(function (error) {
                                    self.$root.$data.show_loading = false;
                                    alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                                });
                            }
                        }
                    });
                }
            }
        }
    }
}
</script>

<style scoped>

</style>
