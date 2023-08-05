<style>
    @font-face {
        font-family: 'Iransans';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/iransans.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Iranyekan';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/iranyekan.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Mitra';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/mitra.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Nastaliq';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/nastaliq.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Nazanin';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/nazanin.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Nazanin';
        font-style: normal;
        font-weight: bold;
        src: url({{ storage_path('/fonts/nazanin_bold.ttf') }}) format('truetype');
    }
    @font-face {
        font-family: 'Titr';
        font-style: normal;
        font-weight: normal;
        src: url({{ storage_path('/fonts/titr.ttf') }}) format('truetype');
    }
    .iransans{
        font-family: Iransans,'sans-serif';
    }
    .iranyekan{
        font-family: Iranyekan,'sans-serif';
    }
    .nazanin{
        font-family: Nazanin,'sans-serif';
    }
    .mitra{
        font-family: Mitra,'sans-serif';
    }
    .nastaliq{
        font-family: Nazanin,'sans-serif';
    }
    .titr{
        font-family: Titr,'sans-serif';
    }
    body{
        direction: rtl;
        font-weight: normal;
    }
    .logo{
        width: 25px;
        height: auto;
        margin-bottom: 5px;
    }
    .bold{
        font-weight: bold;
    }
    table{
        border-collapse: collapse;
        table-layout: fixed;
    }
    table td{
        border: 1px solid #bdbebf;
        font-family: Nazanin, 'sans-serif';
        font-size: 13px;
        padding: 6px 4px;
    }
    table th{
        border: 1px solid #bdbebf;
        padding: 8px 5px;
    }
    table td div{
        margin: 5px 2px;
    }
    .header tr td:first-child{

    }
    .sign{
        margin-right: auto;
        margin-left: auto;
        object-fit: cover;
    }
    @page {
        margin-top: 35px;
        margin-bottom: 35px;
        margin-right: 40px;
        margin-left: 40px;
        size: 210mm 297mm;
    }
    p{
        text-align: justify;
    }
</style>
<body>
<table class="header">
    <tr>
        <td colspan="4" style="text-align: center;background-color: #f1f1f1;">
            <div style="text-align: center">
                <div><img alt="logo" class="logo" src="{{ "data:image/png;base64,$logo" }}"></div>
                <div class="titr" style="font-size: 16px">همیاران شمال شرق (سهامی خاص) - شماره ثبت : 47651 - شناسه ملی : 10380641381</div>
            </div>
        </td>
    </tr>
    <tr>
        <th style="width: 15%" rowspan="6" class="titr"><span style="font-weight: 500;font-size: 14px">الف) مشخصات کارگر</span></th>
        <td style="width: 20%"><span>1- نام : </span><span class="bold">{{$employee->first_name}}</span></td>
        <td style="width: 27%"><span>2 - نام خانوادگی : </span><span class="bold">{{$employee->last_name}}</span></td>
        <td style="width: 38%;"><span>3 - شماره شناسنامه : </span><span class="bold">{{$employee->id_number}}</span></td>
    </tr>
    <tr>
        <td><span>4 - تاریخ تولد : </span><span class="bold">{{$employee->birth_date}}</span></td>
        <td><span>5 - محل صدور : </span><span class="bold">{{$employee->issue_city}}</span></td>
        <td><span>6 - کد ملی : </span><span class="bold">{{$employee->national_code}}</span></td>
    </tr>
    <tr>
        <td><span>7 - نام پدر : </span><span class="bold">{{$employee->father_name}}</span></td>
        <td><span>8 - محل تولد : </span><span class="bold">{{$employee->birth_city}}</span></td>
        <td><span>9 - عنوان شغل : </span><span class="bold">{{$employee->job_title}}</span></td>
    </tr>
    <tr>
        <td><span>10 - تعداد اولاد : </span><span class="bold">{{$employee->children_count}}</span></td>
        <td><span>11 - وضعیت تاهل : </span><span class="bold">{{$employee->marital_word}}</span></td>
        <td><span>12 - وضعیت سربازی : </span><span class="bold">{{$employee->military_word}}</span></td>
    </tr>
    <tr>
        <td><span>13 - شماره بیمه : </span><span class="bold">{{$employee->insurance_number}}</span></td>
        <td>14 - گروه شغلی : <span></span><span class="bold">{{$employee->active_salary_details()["occupational_group"]}}</span></td>
        <td><span>15 - محل خدمت : </span><span class="bold">{{$employee->contract->organization->name}}</span></td>
    </tr>
    <tr>
        <td><span>16- جنسیت : </span><span class="bold">{{$employee->gender_word}}</span></td>
        <td><span>17 - شماره پرسنلی : </span><span class="bold">{{$employee->id}}</span></td>
        <td><span>18 - آخرین مدرک تحصیلی : </span><span class="bold">{{$employee->education}}</span></td>
    </tr>
    <tr>
        <th rowspan="2" class="titr"><span style="font-weight: 500;font-size: 14px">ب) مشخصات کارفرما</span></th>
        <td colspan="2"><span>19 - نام شرکت : </span><span class="bold">{{$company->short_name}}</span></td>
        <td><span>{{ "20 - ".$company->ceo_title." : " }}</span><span class="bold">{{$company->ceo->name}}</span></td>
    </tr>
    <tr>
        <td colspan="2"><span>21 - نشانی قانونی : </span><span class="bold">{{$company->address}}</span></td>
        <td><span>22 - شماره ثبت : </span><span class="bold">{{$company->registration_number}}</span></td>
    </tr>
</table>
<table>
    <tbody>
    <tr>
        <td colspan="4">
            <span>23 - مدت قرارداد : </span>
            <span>از تاریخ  </span>
            <span class="bold">{{verta($employee->active_contract_date()["start"])->format("Y/m/d")}}</span>
            <span>  لغایت  </span>
            <span class="bold">{{verta($employee->active_contract_date()["end"])->format("Y/m/d")}}</span>
            <span>  تعیین می گردد. </span>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p>24 - ساعات کار قرارداد : کارگر موظف است در مقابل حق السعي دريافتي به طور منظم و مطابق با برنامه زمان بندي کارفرما در هفته 44 ساعت انجام وظيفه نمايد در صورت ارجاع کار اضافي در ايام تعطيل و غير تعطيل نسبت به پرداخت اضافه کار وفق مقررات اقدام مي گردد. بديهي است در صورت عدم انجام کار واگذاري به هر دليل کارفرما مجاز به فسخ قرارداد مي باشد و کارگر حق هرگونه اعتراض را از خود سلب و ساقط نمود.</p>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center"><span>25 - شرح قرارداد</span></td>
        <td colspan="2" style="text-align: center"><span>26 - دستمزد و مزایا(ماهانه)</span></td>
    </tr>
    <tr>
        <td colspan="2" style="width: 50%">
            <p>
                - اين قرارداد به استناد ماده 10 قانون کار في مابين شرکت
                <span class="bold">{{ $company->short_name }}</span>
                به نمايندگي جناب آقاي
                <span class="bold">{{ $company->ceo->name }}</span>
                به عنوان کارفرما و
                {{ $employee->gender_refer }}
                <span class="bold">{{ $employee->name }}</span>
                به عنوان کارگر می باشد.
            </p>
            <p>- چنانچه کار مشابه در جاي ديگر به کارگر ارجاع شود بدون هيچگونه عذري آنرا انجام خواهد داد. نوع کار و محل کار به کارگر اعلام شده و عدم قبول به منزله ترک کار و فسخ قرارداد مي باشد.</p>
            <p>- کارفرما هيچگونه تعهدي در قبال سرويس اياب و ذهاب ندارد.</p>
            <p>- به موجب ماده 148 قانون ، کارفرما مکلف است کارگر را نزد سازمان تامين اجتماعي يا ساير دستگاه هاي بيمه گذار بيمه نمايد.</p>
            <p>- ساير مواردي که در اين قرارداد پيش بيني نشده است تابع قانون کار و تأمين اجتماعي و مقررات تبعي آنهاست.</p>
            <p>
                - شرح حکم : تمامی موارد و مدت های مندرج در این قرارداد به تبع قرارداد شماره
                <span class="bold">{{ $number }}</span>
                مورخ
                <span class="bold">{{verta($employee->active_contract_date()["start"])->format("Y/m/d")}}</span>
                انجام پذیرفته است.
            </p>
        </td>
        <td style="vertical-align: top;width: 35%">
            <div>دستمزد روزانه</div>
            <div>دستمزد ماهانه</div>
            @if($employee->active_salary_details()["prior_service"] > 0)
                <div>پایه سنوات</div>
            @endif
            <div>دستمزد ماهیانه(مزد مبنا)</div>
            <div>حق اولاد ماهیانه</div>
            <div>کمک هزینه مسکن</div>
            <div>کمک هزینه اقلام مصرفی خانوار(بن ماهیانه)</div>
            @if(count($employee->active_salary_details()["advantages"]) > 0)
                @foreach($employee->active_salary_details()["advantages"] as $advantage)
                    <div>{{$advantage["title"]}}</div>
                @endforeach
            @endif
            <div>جمع کل مزایای ماهانه</div>
            <div>جمع حقوق و مزایای ماهانه</div>
        </td>
        <td style="vertical-align: top;text-align: center;width: 15%;">
            <div>{{number_format($employee->active_salary_details()["daily_wage"])." ریال"}}</div>
            <div>{{number_format($employee->active_salary_details()["base_salary"])." ریال"}}</div>
            @if($employee->active_salary_details()["prior_service"] > 0)
                <div>{{number_format($employee->active_salary_details()["prior_service"])." ریال"}}</div>
            @endif
            <div>{{number_format($employee->active_salary_details()["monthly_wage"])." ریال"}}</div>
            <div>{{number_format($employee->active_salary_details()["child_allowance"])." ریال"}}</div>
            <div>{{number_format($employee->active_salary_details()["housing_purchase_allowance"])." ریال"}}</div>
            <div>{{number_format($employee->active_salary_details()["household_consumables_allowance"])." ریال"}}</div>
            @if(count($employee->active_salary_details()["advantages"]) > 0)
                @foreach($employee->active_salary_details()["advantages"] as $advantage)
                    <div>{{number_format($advantage["value"])." ریال"}}</div>
                @endforeach
            @endif
            <div>{{number_format($employee->active_salary_details()["advantage_total"])." ریال"}}</div>
            <div>{{number_format($employee->active_salary_details()["salary_total"])." ریال"}}</div>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p>
                27 - وظیفه کارگر در این قرارداد عبارتست از انجام امور مربوط به وظایف شغل مندرج در بند 9 این قرارداد که بر اساس شرح وظایف مصوب شغل مندرج در طرح هماهنگ طبقه بندی مشاغل بوده این وظایف را در واحد محل خدمت تعیین شده طبق نظر کارفرما، تحت نظارت مافوق انجام دهد . نوبت کاری طبق ضوابط پرداخت می گردد . مزد و مزایای مندرج در این قرارداد بر مبنای سال 1401 تنظیم شده و از 1401/01/01 مصوبات قانونی مزد و مزایای بر روی آنها لحاظ خواهد شد . در صورت اضافه کاری و نوبت کاری و شب کاری مواد 56 و 58 و 59 قانون کار اعمال خواهد شد.
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <p>28 - شرایط فسخ قرارداد</p>
            <p>- فسخ قرارداد 3 روز قبل به طرف مقابل کتباً اعلام مي شود.</p>
            <p>- غيبت متوالي به مدت 3 روز درهفته يا 7 روز متناوب درماه و همچنين تأخيردر ورود و يا تعجيل در خروج از کارگاه جمعاً به مدت 10 ساعت در ماه موجب فسخ قرارداد مي باشد.</p>
            <p>- رد صلاحيت از طريق مراجع ذي صلاح و حراست سازمان.</p>
            <p>- ارتکاب اعمال خلاف قانون که موجب تعقيب از طريق مراجع قضايي و غير قضائي گردد.</p>
            <p>- اعتياد به هرگونه مواد مخدر گياهي، صنعتي و ... در صورت ارايه گزارش بازرسين يا با تاييد مراجع ذي صلاح.</p>
            <p>- تعديل نيرو يا عدم نياز به ادامه خدمت کارگر ناشي از تقليل فعاليت کارگاه و تغيير ساختار و همچنين عدم وجود تفاهم بين کارگر و کارفرما و لزوم حفظ نظم محل کار به تشخيص کارفرما.</p>
        </td>
    </tr>
    </tbody>
</table>
<table style="width: 100%;">
    <tr>
        <td style="height: 150px;text-align: center;vertical-align: middle;width: 10%">
            <div>شماره</div>
            <div>{{$number}}</div>
        </td>
        <td style="height: 150px;text-align: center;vertical-align: top;width: 45%">
            <span>امضاء و اثر انگشت کارگر</span>
        </td>
        <td style="height: {{count($employee->active_salary_details()["advantages"]) == 0 ? "200px" : (strval(200-(count($employee->active_salary_details()["advantages"]) * 5))) ."px"}};text-align: center;vertical-align: top;width: 45%">
            <span>نام، امضاء و مهر شرکت پیمانکار</span>
            @if($sign)
                @if($company->ceo->GetSign())
                    <br/>
                    <br/>
                    <div>
                        <img class="sign" src="{{$company->ceo->GetSign()}}" alt="همیاران شمال شرق">
                    </div>
                @endif
            @endif
        </td>
    </tr>
</table>
</body>
