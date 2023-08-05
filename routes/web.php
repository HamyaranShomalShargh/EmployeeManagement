<?php

use App\Http\Controllers\Auth\LoginController;
use \App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Staff\ContractController;
use App\Http\Controllers\Staff\CustomGroupController;
use App\Http\Controllers\Staff\EmployeeAppointmentLetterController;
use App\Http\Controllers\Staff\EmployeeDocController;
use App\Http\Controllers\Staff\EmployeeFinancialAdvantagesController;
use App\Http\Controllers\Staff\EmployeeManagementController;
use App\Http\Controllers\Staff\EmployeePaySlipController;
use App\Http\Controllers\Staff\EmployeeRequestAutomationController;
use App\Http\Controllers\Staff\EmployeesRecruitingController;
use App\Http\Controllers\Staff\FormTemplateController;
use App\Http\Controllers\Staff\IdleQuickAccessEntities;
use App\Http\Controllers\Staff\LabourLawController;
use App\Http\Controllers\Staff\NewsController;
use App\Http\Controllers\Staff\OccasionController;
use App\Http\Controllers\Staff\OrganizationController;
use App\Http\Controllers\Staff\PaySlipTemplateController;
use App\Http\Controllers\Staff\RefreshDataEmployeeController;
use App\Http\Controllers\Staff\SalaryContentController;
use App\Http\Controllers\Staff\SmsPhraseCategoryController;
use App\Http\Controllers\Staff\SmsPhraseController;
use App\Http\Controllers\Staff\StaffSettingController;
use App\Http\Controllers\Staff\StaffUserController;
use App\Http\Controllers\Staff\TicketController;
use App\Http\Controllers\Staff\UnregisteredEmployeeController;
use App\Http\Controllers\SuperUser\AutomationFlowController;
use App\Http\Controllers\SuperUser\EmployeeRequestsController;
use App\Http\Controllers\SuperUser\MenuActionController;
use App\Http\Controllers\SuperUser\MenuHeaderController;
use App\Http\Controllers\SuperUser\MenuItemController;
use App\Http\Controllers\SuperUser\SuperUserRoleController;
use App\Http\Controllers\SuperUser\SuperUserUserController;
use App\Http\Controllers\SuperUser\SystemInformationController;
use App\Http\Controllers\User\ApplicationFormController;
use App\Http\Controllers\User\RefreshDataController;
use App\Http\Controllers\User\UserPaySlipController;
use App\Http\Controllers\User\UserSettingController;
use App\Http\Controllers\User\UserTicketController;
use App\Http\Controllers\ValidationController;
use App\Imports\ImportContractDateExcel;
use App\Imports\NewContractEmployee;
use App\Models\Automation;
use App\Models\CompanyInformation;
use App\Models\Contract;
use App\Models\ContractPreEmployee;
use App\Models\Employee;
use App\Models\EmployeeDataRequest;
use App\Models\EmployeePaySlip;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\UnregisteredEmployee;
use App\Models\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mews\Captcha\Facades\Captcha;
use \App\Http\Controllers\Staff\AxiosController;
use App\Http\Controllers\User\RegisterUserController;

Route::get('/', function () {
    return redirect()->route("Home");
});

Auth::routes();
Route::post("/recaptcha",function (){
    return response()->json([
        'captcha' => Captcha::img()
    ]);
});
Route::get('logout', [LoginController::class,'logout']);
Route::group(["prefix" => "Home"],function (){
    Route::get('/', [HomeController::class, 'index'])->name('Home');
    Route::get('NewsDetails/{id}', [HomeController::class, 'NewsDetails'])->name('NewsDetails');
    Route::get('ContactUs', [HomeController::class, 'ContactUs'])->name('ContactUs');
    Route::get('AboutUs', [HomeController::class, 'AboutUs'])->name('AboutUs');
});
Route::group(["prefix" => "Registration"],function (){
    Route::get("/", [RegisterUserController::class, 'introduction'])->name("introduction");
    Route::get("/SuccessfulRegistration/{national_code}", [RegisterUserController::class, 'success_registration'])->name("success_registration");
    Route::get("/FollowUpRegistration",[RegisterUserController::class, 'follow_up_registration'])->name('follow_up_registration');
    Route::post("/RegistrationResult",[RegisterUserController::class, 'following_up_registration'])->name('registration_result');
    Route::get("/ReloadEmployeeDataIndex/{id}",[RegisterUserController::class, 'reload_data_index'])->name('registration_reload_data_index');
    Route::get("/ReloadEmployeeDataFinished",[RegisterUserController::class, 'reload_data_finish'])->name('reload_data_finish');
    Route::post("/ReloadEmployeeData/{id}",[RegisterUserController::class, 'reload_data'])->name('registration_reload_data');
    Route::post("/UnregisteredEmployees",[RegisterUserController::class, 'unregistered_employees'])->name('unregistered_employees');
    Route::get('/CheckNationalCode', [RegisterUserController::class, 'step_one'])->name('step_one');
    Route::post('/CheckNationalCode', [RegisterUserController::class, 'check_national_code'])->name('check_national_code');
    Route::group(["middleware" => ["registration_expiration"]],function (){
        Route::get("/GetMobileNumber",[RegisterUserController::class, 'step_two'])->name("step_two");
        Route::post("/GetMobileNumber",[RegisterUserController::class, 'send_verification_code'])->name("send_verification_code");
        Route::post("/ResendVerificationCode",[RegisterUserController::class, 'resend_verification_code'])->name("resend_verification_code");
        Route::get("/CheckVerificationCode",[RegisterUserController::class, 'step_three'])->name("step_three");
        Route::post("/CheckVerificationCode",[RegisterUserController::class, 'check_verification_code'])->name("check_verification_code");
        Route::group(["middleware" => ["registration_activation"]],function () {
            Route::get("/PersonalInformation", [RegisterUserController::class, 'step_four'])->name("step_four");
            Route::post("/PersonalInformation", [RegisterUserController::class, 'store_personal_information'])->name("store_personal_information");
            Route::group(["middleware" => ["registration_p_information"]],function () {
                Route::get("/JobInformation", [RegisterUserController::class, 'step_five'])->name("step_five");
                Route::post("/JobInformation", [RegisterUserController::class, 'store_job_information'])->name("store_job_information");
                Route::group(["middleware" => ["registration_j_information"]],function () {
                    Route::get("/UploadImageDocuments/{clear?}", [RegisterUserController::class, 'step_six'])->name("step_six");
                    Route::post("/UploadImageDocuments", [RegisterUserController::class, 'store_image_documents'])->name("store_image_documents");
                    Route::group(["middleware" => ["upload_documents"]],function () {
                        Route::get("/CheckUp", [RegisterUserController::class, 'check_up'])->name("check_up");
                        Route::post("/RegisterEmployee", [RegisterUserController::class, 'register_employee'])->name("register_employee");
                    });
                });
            });
        });
    });
});
Route::group(['middleware' => ['auth','staff_permission']],function (){
    Route::group(['prefix' => 'ContractSubsetEmployee'],function (){
        Route::post("/Edit",[AxiosController::class,"ContractSubsetEmployeeEdit"])->name("ContractSubsetEmployeeEdit");
        Route::post("/Delete",[AxiosController::class,"ContractSubsetEmployeeDelete"])->name("ContractSubsetEmployeeDelete");
        Route::post("/DeleteAll",[AxiosController::class,"ContractSubsetEmployeeDeleteAll"])->name("ContractSubsetEmployeeDeleteAll");
        Route::post("/Add",[AxiosController::class,"ContractSubsetEmployeeAdd"])->name("ContractSubsetEmployeeAdd");
    });
    Route::group(['prefix' => 'Employee'],function (){
        Route::post("/Edit",[AxiosController::class,"EditEmployeeDatabaseInformation"])->name("EditEmployeeDatabaseInformation");
    });
});
Route::group(['prefix'=>'Dashboard', 'middleware'=>['auth']],function() {
    Route::get("/",[DashboardController::class,"idle"])->name("idle");
    Route::group(['prefix'=>'SuperUser', 'middleware'=>['superuser_permission']],function(){
        Route::get("/",[DashboardController::class,"superuser"])->name("superuser_idle");
        Route::get("/SystemInformation",[SystemInformationController::class,"index"])->name("SystemInformation.index");
        Route::put("/SystemInformation",[SystemInformationController::class,"update"])->name("SystemInformation.update");
        Route::resource("/MenuHeaders",MenuHeaderController::class);
        Route::post("/MenuHeaders/Activation/{id}",[MenuHeaderController::class,"status"])->name("MenuHeaders.activation");
        Route::resource("/MenuItems",MenuItemController::class);
        Route::resource("/MenuActions",MenuActionController::class);
        Route::resource("/SuperUserUsers",SuperUserUserController::class);
        Route::post("/SuperUserUsers/Activation/{id}",[SuperUserUserController::class,"status"])->name("SuperUserUsers.activation");
        Route::resource("/SuperUserRoles",SuperUserRoleController::class);
        Route::post("/SuperUserRoles/Activation/{id}",[SuperUserRoleController::class,"status"])->name("SuperUserRoles.activation");
        Route::resource("/AutomationFlow",AutomationFlowController::class);
        Route::post("/AutomationFlow/Activation/{id}",[AutomationFlowController::class,"status"])->name("AutomationFlow.activation");
        Route::resource("/EmployeeRequests",EmployeeRequestsController::class);

    });
    Route::group(['prefix'=>'Staff', 'middleware'=>['staff_permission']],function(){
        Route::get("/",[DashboardController::class,"staff"])->name("staff_idle");
        Route::post("/IdlePluginsFeed",[IdleQuickAccessEntities::class,"publish"])->name("IdlePlugins");
        Route::group(['prefix'=>'Organizations'],function () {
            Route::get("/index", [OrganizationController::class,"index"])->name("Organizations.index");
            Route::post("/store", [OrganizationController::class,"store"])->name("Organizations.store");
            Route::get("/edit/{id}", [OrganizationController::class,"edit"])->name("Organizations.edit");
            Route::put("/update/{id}", [OrganizationController::class,"update"])->name("Organizations.update");
            Route::delete("/destroy/{id}", [OrganizationController::class,"destroy"])->name("Organizations.destroy");
            Route::post("/activation/{id}", [OrganizationController::class, "status"])->name("Organizations.activation");
        });

        Route::group(['prefix'=>'Contracts'],function () {
            Route::get("/index", [ContractController::class,"index"])->name("Contracts.index");
            Route::post("/store", [ContractController::class,"store"])->name("Contracts.store");
            Route::get("/edit/{id}", [ContractController::class,"edit"])->name("Contracts.edit");
            Route::put("/update/{id}", [ContractController::class,"update"])->name("Contracts.update");
            Route::delete("/destroy/{id}", [ContractController::class,"destroy"])->name("Contracts.destroy");
            Route::post("/activation/{id}", [ContractController::class, "status"])->name("Contracts.activation");
            Route::get("/download/{id}", [ContractController::class, "download_docs"])->name("Contracts.download_docs");
            Route::get("/excel_download", [ContractController::class, "excel_download"])->name("Contracts.excel_download");
            Route::post("/excel_upload", [ContractController::class, "excel_upload"])->name("Contracts.excel_upload");
            Route::post("/subset_destroy", [ContractController::class,"subset_destroy"])->name("Subsets.destroy");
            Route::post("/pre_employee/operation/{type}", [ContractController::class, "pre_employee_operations"])->name("Contracts.pre_employee_operation");
        });

        Route::group(['prefix'=>'EmployeesRecruiting'],function (){
            Route::get("/index",[EmployeesRecruitingController::class,"index"])->name("EmployeesRecruiting.index");
            Route::post("/Confirm",[EmployeesRecruitingController::class,"confirm"])->name("EmployeesRecruiting.confirm");
            Route::post("/Refuse",[EmployeesRecruitingController::class,"refuse"])->name("EmployeesRecruiting.refuse");
            Route::post("/ReloadData",[EmployeesRecruitingController::class,"reload_data"])->name("EmployeesRecruiting.reload_data");
        });

        Route::group(['prefix'=>'FormTemplates'],function (){
            Route::get("/index", [FormTemplateController::class,"index"])->name("FormTemplates.index");
            Route::post("/store", [FormTemplateController::class,"store"])->name("FormTemplates.store");
            Route::get("/edit/{id}", [FormTemplateController::class,"edit"])->name("FormTemplates.edit");
            Route::put("/update/{id}", [FormTemplateController::class,"update"])->name("FormTemplates.update");
            Route::delete("/destroy/{id}", [FormTemplateController::class,"destroy"])->name("FormTemplates.destroy");
            Route::post("/activation/{id}", [FormTemplateController::class, "status"])->name("FormTemplates.activation");
        });

        Route::group(['prefix'=>'EmployeesManagement'],function (){
            Route::get("/index",[EmployeeManagementController::class,"index"])->name('EmployeesManagement.index');
            Route::group(['prefix' => 'Excel'],function (){
                Route::get("/excel_download/{option}", [EmployeeManagementController::class, "excel_download"])->name("EmployeesManagement.excel_download");
                Route::post("/excel_upload/{option}", [EmployeeManagementController::class, "excel_upload"])->name("EmployeesManagement.excel_upload");
            });
            Route::group(['prefix' => 'Operation'],function (){
                Route::post("/Add", [EmployeeManagementController::class, "add_employee"])->name("EmployeesManagement.add_new_item");
                Route::post("/Delete", [EmployeeManagementController::class, "delete_employee"])->name("EmployeesManagement.delete_item");
                Route::post("/Status", [EmployeeManagementController::class, "employee_status"])->name("EmployeesManagement.item_status");
                Route::post("/Authentication", [EmployeeManagementController::class, "employee_auth"])->name("EmployeesManagement.item_auth");
                Route::post("/RefreshData", [EmployeeManagementController::class, "employee_refresh_data"])->name("EmployeesManagement.item_data_refresh");
                Route::post("/DateExtension", [EmployeeManagementController::class, "employee_date_extension"])->name("EmployeesManagement.item_date_extension");
                Route::post("/ContractConversion", [EmployeeManagementController::class, "employee_contract_conversion"])->name("EmployeesManagement.item_contract_conversion");
                Route::post("/BatchApplication", [EmployeeManagementController::class, "employee_batch_application"])->name("EmployeesManagement.item_batch_application");
                Route::post("/ExcelList", [EmployeeManagementController::class, "employee_excel_list"])->name("EmployeesManagement.item_excel_list");
                Route::post("/SendTicket", [EmployeeManagementController::class, "employee_send_ticket"])->name("EmployeesManagement.send_ticket");
                Route::post("/GetTickets", [EmployeeManagementController::class, "employee_get_tickets"])->name("EmployeesManagement.get_tickets");
                Route::post("/SendSms", [EmployeeManagementController::class, "employee_send_sms"])->name("EmployeesManagement.send_sms");
                Route::post("/GetDeletedEmployees", [EmployeeManagementController::class, "get_deleted_employees"])->name("EmployeesManagement.get_deleted_employees");
                Route::post("/RecoverEmployee", [EmployeeManagementController::class, "recover_employee"])->name("EmployeesManagement.recover_employee");
            });
            Route::post("/Confirm",[EmployeeManagementController::class,"confirm"])->name("EmployeesManagement.confirm");
            Route::post("/Refuse",[EmployeeManagementController::class,"refuse"])->name("EmployeesManagement.refuse");
            Route::post("/ReloadData",[EmployeeManagementController::class,"reload_data"])->name("EmployeesManagement.reload_data");
        });
        Route::group(['prefix' => 'PaySlipTemplates'],function (){
            Route::get("/index", [PaySlipTemplateController::class,"index"])->name("PaySlipTemplates.index");
            Route::post("/store", [PaySlipTemplateController::class,"store"])->name("PaySlipTemplates.store");
            Route::get("/edit/{id}", [PaySlipTemplateController::class,"edit"])->name("PaySlipTemplates.edit");
            Route::put("/update/{id}", [PaySlipTemplateController::class,"update"])->name("PaySlipTemplates.update");
            Route::delete("/destroy/{id}", [PaySlipTemplateController::class,"destroy"])->name("PaySlipTemplates.destroy");
        });
        Route::group(['prefix' => 'EmployeePaySlips'],function (){
            Route::group(['prefix' => 'Excel'],function (){
                Route::get("/download/{contract_id}", [EmployeePaySlipController::class, "excel_download"])->name("EmployeePaySlips.excel_download");
                Route::post("/upload", [EmployeePaySlipController::class, "excel_upload"])->name("EmployeePaySlips.excel_upload");
            });
            Route::get("/index", [EmployeePaySlipController::class,"index"])->name("EmployeePaySlips.index");
            Route::get("/show/{id}", [EmployeePaySlipController::class,"show"])->name("EmployeePaySlips.show");
            Route::post("/store", [EmployeePaySlipController::class,"store"])->name("EmployeePaySlips.store");
            Route::get("/edit/{id}", [EmployeePaySlipController::class,"edit"])->name("EmployeePaySlips.edit");
            Route::put("/update/{id}", [EmployeePaySlipController::class,"update"])->name("EmployeePaySlips.update");
            Route::delete("/destroy/{id}", [EmployeePaySlipController::class,"destroy"])->name("EmployeePaySlips.destroy");
            Route::delete("/destroyAll", [EmployeePaySlipController::class,"destroyAll"])->name("EmployeePaySlips.destroyAll");
        });
        Route::group(['prefix' => 'Tickets'],function (){
            Route::get("/index", [TicketController::class,"index"])->name("Tickets.index");
            Route::post("/store", [TicketController::class,"store"])->name("Tickets.store");
            Route::put("/update/{id}", [TicketController::class,"update"])->name("Tickets.update");
            Route::delete("/destroy/{id}", [TicketController::class,"destroy"])->name("Tickets.destroy");
            Route::delete("/destroyAll/{id}", [TicketController::class,"destroyAll"])->name("Tickets.destroyAll");
        });
        Route::group(['prefix' => 'UnregisteredEmployees'],function (){
            Route::get("/index", [UnregisteredEmployeeController::class,"index"])->name("UnregisteredEmployees.index");
            Route::put("/confirm/{id}", [UnregisteredEmployeeController::class,"confirm"])->name("UnregisteredEmployees.confirm");
            Route::delete("/refuse/{id}", [UnregisteredEmployeeController::class,"refuse"])->name("UnregisteredEmployees.refuse");
        });
        Route::group(['prefix' => 'RefreshDataEmployees'],function (){
            Route::get("/index", [RefreshDataEmployeeController::class,"index"])->name("RefreshDataEmployees.index");
            Route::put("/confirm/{id}", [RefreshDataEmployeeController::class,"confirm"])->name("RefreshDataEmployees.confirm");
            Route::delete("/refuse/{id}", [RefreshDataEmployeeController::class,"refuse"])->name("RefreshDataEmployees.refuse");
        });
        Route::group(['prefix' => 'EmployeeFinancialAdvantages'],function (){
            Route::group(['prefix' => 'Excel'],function (){
                Route::get("/download", [EmployeeFinancialAdvantagesController::class, "excel_download"])->name("EmployeeFinancialAdvantages.excel_download");
                Route::post("/upload", [EmployeeFinancialAdvantagesController::class, "excel_upload"])->name("EmployeeFinancialAdvantages.excel_upload");
            });
            Route::get("/index", [EmployeeFinancialAdvantagesController::class,"index"])->name("EmployeeFinancialAdvantages.index");
            Route::post("/store", [EmployeeFinancialAdvantagesController::class,"store"])->name("EmployeeFinancialAdvantages.store");
            Route::post("/storeSolo/", [EmployeeFinancialAdvantagesController::class,"storeSolo"])->name("EmployeeFinancialAdvantages.store_solo");
            Route::get("/edit/{id}", [EmployeeFinancialAdvantagesController::class,"edit"])->name("EmployeeFinancialAdvantages.edit");
            Route::put("/update/{id}", [EmployeeFinancialAdvantagesController::class,"update"])->name("EmployeeFinancialAdvantages.update");
            Route::delete("/destroy/{id}", [EmployeeFinancialAdvantagesController::class,"destroy"])->name("EmployeeFinancialAdvantages.destroy");
            Route::delete("/destroyAll", [EmployeeFinancialAdvantagesController::class,"destroyAll"])->name("EmployeeFinancialAdvantages.destroyAll");
        });
        Route::group(['prefix' => 'EmployeeRequestsAutomation'],function (){
            Route::get("/index", [EmployeeRequestAutomationController::class,"index"])->name("EmployeeRequestsAutomation.index");
            Route::post("/refresh", [EmployeeRequestAutomationController::class,"refresh_data"])->name("EmployeeRequestsAutomation.refresh_data");
            Route::post("/seen", [EmployeeRequestAutomationController::class,"seen"])->name("EmployeeRequestsAutomation.seen");
            Route::post("/confirm", [EmployeeRequestAutomationController::class,"confirm"])->name("EmployeeRequestsAutomation.confirm");
            Route::post("/reject", [EmployeeRequestAutomationController::class,"reject"])->name("EmployeeRequestsAutomation.reject");
            Route::get("/preview/{id}",[EmployeeRequestAutomationController::class,"preview"])->name("EmployeeRequestsAutomation.preview");
            Route::post("/latest", [EmployeeRequestAutomationController::class,"get_latest"])->name("EmployeeRequestsAutomation.latest");
            Route::post("/editEmployeeInformation", [EmployeeRequestAutomationController::class,"edit_employee_information"])->name("EmployeeRequestsAutomation.edit_employee_information");
        });
        Route::group(['prefix' => 'EmployeeAppointmentLetter'],function (){
            Route::get("index",[EmployeeAppointmentLetterController::class,"index"])->name("EmployeeAppointmentLetter.index");
            Route::post("BatchPrint",[EmployeeAppointmentLetterController::class,"batch_print"])->name("EmployeeAppointmentLetter.batch_print");
        });
        Route::resource("/LabourLaw",LabourLawController::class);
        Route::resource("StaffUsers",StaffUserController::class);
        Route::post("/StaffUsers/Activation/{id}",[StaffUserController::class,"status"])->name("StaffUsers.activation");

        Route::resource("/SmsPhraseCategory",SmsPhraseCategoryController::class);
        Route::post("/SmsPhraseCategory/Activation/{id}",[SmsPhraseCategoryController::class,"status"])->name("SmsPhraseCategory.activation");
        Route::resource("/SmsPhrases",SmsPhraseController::class);
        Route::post("/SmsPhrases/Test/{id}",[SmsPhraseController::class,"test"])->name("SmsPhrases.test");

        Route::resource("/SalaryContents",SalaryContentController::class);

        Route::group(['prefix'=>'CustomGroups'],function () {
            Route::get("/index", [CustomGroupController::class,"index"])->name("CustomGroups.index");
            Route::post("/store", [CustomGroupController::class,"store"])->name("CustomGroups.store");
            Route::get("/edit/{id}", [CustomGroupController::class,"edit"])->name("CustomGroups.edit");
            Route::put("/update/{id}", [CustomGroupController::class,"update"])->name("CustomGroups.update");
            Route::delete("/destroy/{id}", [CustomGroupController::class,"destroy"])->name("CustomGroups.destroy");
            Route::post("/delete_employee", [CustomGroupController::class,"delete_employee"])->name("CustomGroups.delete_employee");
            Route::post("/activation/{id}",[CustomGroupController::class,"status"])->name("CustomGroups.activation");
            Route::get("/excel_download",[CustomGroupController::class,"excel_download"])->name("CustomGroups.excel_download");
            Route::post("/excel_upload",[CustomGroupController::class,"excel_upload"])->name("CustomGroups.excel_upload");
        });

        Route::group(['prefix'=>'StaffSettings'],function(){
            Route::put("/username",[StaffSettingController::class,"UsernameChange"])->name("StaffSettings.UsernameChange");
            Route::put("/password",[StaffSettingController::class,"PasswordChange"])->name("StaffSettings.PasswordChange");
        });

        Route::resource("News",NewsController::class);
        Route::get("News/DeleteImage/{id}/{file}",[NewsController::class,"delete_image"])->name("News.delete_image");
        Route::post("News/activation/{id}", [NewsController::class, "status"])->name("News.activation");
        Route::resource("Occasions",OccasionController::class);
        Route::post("Occasions/activation/{id}", [OccasionController::class, "status"])->name("Occasions.activation");
    });
    Route::group(['prefix'=>'User', 'middleware'=>['user_permission']],function(){
        Route::group(['middleware' => ['employee_check_status']],function (){
            Route::get("/",[DashboardController::class,"user"])->name("user_idle");
            Route::group(['prefix'=>'ApplicationForms'],function(){
                Route::get("/index",[ApplicationFormController::class,"index"])->name("ApplicationForms.index");
                Route::get("/create",[ApplicationFormController::class,"create"])->name("ApplicationForms.create");
                Route::post("/store",[ApplicationFormController::class,"store"])->name("ApplicationForms.store");
                Route::get("/edit",[ApplicationFormController::class,"edit"])->name("ApplicationForms.edit");
                Route::get("/update",[ApplicationFormController::class,"update"])->name("ApplicationForms.update");
                Route::get("/destroy",[ApplicationFormController::class,"destroy"])->name("ApplicationForms.destroy");
                Route::get("/download/{id}",[ApplicationFormController::class,"download_pdf"])->name("ApplicationForms.download_pdf");
            });
            Route::group(['prefix'=>'UserTickets'],function(){
                Route::get("/index",[UserTicketController::class,"index"])->name("UserTickets.index");
                Route::get("/create",[UserTicketController::class,"create"])->name("UserTickets.create");
                Route::post("/store",[UserTicketController::class,"store"])->name("UserTickets.store");
                Route::post("/send",[UserTicketController::class,"send"])->name("UserTickets.send");
                Route::put("/update/{id}",[UserTicketController::class,"update"])->name("UserTickets.update");
                Route::delete("/destroy/{id}",[UserTicketController::class,"destroy"])->name("UserTickets.destroy");
            });
            Route::group(['prefix'=>'UserPaySlips'],function(){
                Route::get("/index",[UserPaySlipController::class,"index"])->name("UserPaySlips.index");
                Route::post("/published",[UserPaySlipController::class,"published"])->name("UserPaySlips.published");
                Route::get("/download/{id}",[UserPaySlipController::class,"PdfDownload"])->name("UserPaySlips.download");
            });
            Route::group(['prefix'=>'UserSettings'],function(){
                Route::put("/username",[UserSettingController::class,"UsernameChange"])->name("UserSettings.UsernameChange");
                Route::put("/password",[UserSettingController::class,"PasswordChange"])->name("UserSettings.PasswordChange");
            });

        });
        Route::group(['prefix'=>'EmployeeRefreshData'],function(){
            Route::get("index",[RefreshDataController::class,"index"])->name("EmployeeRefreshData.index");
            Route::post("store/{id}",[RefreshDataController::class,"store"])->name("EmployeeRefreshData.store");
            Route::get("waiting",[RefreshDataController::class,"waiting"])->name("EmployeeRefreshData.waiting");
        });
    });
});
Route::group(['prefix'=>'Docs', 'middleware'=>['auth','staff_permission']],function() {
    Route::group(['prefix'=>'Download'],function() {
        Route::get("/Application/{path}", [EmployeeDocController::class, "download_application"])->name("docs.application_download");
        Route::get("/Payslip/{path}", [EmployeeDocController::class, "download_payslip"])->name("docs.payslip_download");
    });
    Route::group(['prefix'=>'View'],function() {
        Route::get("/Images/{path}",[EmployeeDocController::class,"image_view"])->name("docs.image_view");
    });
});
Route::group(['prefix'=>'Print', 'middleware'=>['auth','staff_permission']],function() {
    Route::get("/Docs/{path}/{config?}",[EmployeeDocController::class,"print_docs"])->name("print_docs");
});
Route::group(['prefix'=>'Validation'],function() {
    Route::get("direct/{i_number}",[ValidationController::class,"direct"])->name("Validation.direct");
    Route::get("index",[ValidationController::class,"index"])->name("Validation.index");
    Route::post("check",[ValidationController::class,"check"])->name("Validation.check");
});
//Route::get("/c",function (){
//    $rr = [
//        "بانک ملّی ایران",
//        "بانک اقتصاد نوین",
//        "بانک قرض‌الحسنه مهر ایران",
//        "بانک سپه",
//        "بانک پارسیان",
//        "بانک قرض‌الحسنه رسالت",
//        "بانک صنعت و معدن",
//        "بانک کارآفرین",
//        "بانک کشاورزی",
//        "بانک سامان",
//        "بانک مسکن",
//        "بانک سینا",
//        "بانک توسعه صادرات ایران",
//        "بانک خاور میانه",
//        "بانک توسعه تعاون",
//        "بانک شهر",
//        "پست بانک ایران",
//        "بانک دی",
//        "بانک صادرات",
//        "بانک ملت",
//        "بانک تجارت",
//        "بانک رفاه",
//        "بانک آینده",
//        "بانک گردشگری",
//        "بانک ایران زمین",
//        "بانک قوامین(وابسته به بانک سپه",
//        "بانک انصار(وابسته به بانک سپه)",
//        "بانک سرمایه",
//        "بانک پاسارگاد"
//    ];
//    foreach ($rr as $e)
//        \App\Models\Bank::query()->create(["name" => $e]);
//});
//Route::get("/b",function (){
//    $news = \App\Models\DomesticNews::query()->with("image")->get();
//    $allFiles = \Illuminate\Support\Facades\File::allFiles(storage_path("app/public/image_gallery"));
//    foreach ($news as $article) {
//        $files = $article->image;
//        $main = $article->image->where("role","=",1)->first();
//        $added = \App\Models\News::query()->create([
//            "user_id" => 1,
//            "title" => $article->title,
//            "topic" => $article->title,
//            "brief" => $article->short_desc,
//            "description" => $article->description,
//            "views" => $article->view,
//            "published" => 1,
//            "image" => $main->image_file_name,
//            "created_at" => date("Y-m-d H:i:s",strtotime($article->created_at)),
//            "updated_at" => date("Y-m-d H:i:s",strtotime($article->created_at))
//        ]);
//        \Illuminate\Support\Facades\Storage::disk("news")->makeDirectory($added->id);
//        foreach ($files as $file){
//            foreach ($allFiles as $allFile){
//                if ($allFile->getFilename() == $file->image_file_name)
//                    \Illuminate\Support\Facades\File::copy($allFile,\Illuminate\Support\Facades\Storage::disk("news")->path($added->id."/".$allFile->getFilename()));
//            }
//        }
//    }
//});
//
//Route::get("/v",function (){
//    $contacts = Contract::all();
//    foreach ($contacts as $contact){
//        if ($contact->is_parent == 0) {
//            $id = $contact->id;
//            $import = new NewContractEmployee($contact->id);
//            $import->import(storage_path("app/public/exl/$id.xlsx"));
//        }
//    }
//});
//Route::get("/bv",function (){
//    $a = Automation::query()->findOrFail(42);
//    $flow_details = json_decode($a->flow,true);
//    $main_role = array_filter($flow_details,function ($flow){
//        return $flow["is_main_role"] == 1;
//    });
//    if ($main_role){
//        $user = $a->signs()->whereHas("user",function ($query) use ($main_role){
//            $query->whereIn("users.role_id",array_column($main_role,"is_main_role"));
//        })->first();
//        dd($user);
//    }
//});
Route::get("/optimize" ,function (){
    \Illuminate\Support\Facades\Artisan::call("optimize:clear");
});
