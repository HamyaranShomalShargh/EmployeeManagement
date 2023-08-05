<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>همیاران شمال شرق - داشبورد</title>
    <link href="{{ asset('css/app.css?v='.time()) }}" rel="stylesheet">
    <style>
        body{
            background: rgba(240,240,240,1);
            padding-top: 70px;
            padding-bottom: 40px;
            text-align: center;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        body::-webkit-scrollbar {
            display: none;
        }
        .print-page{
            @if($config != null)
                @if($config["orientation"] == "landscape")
                    @if($config["page"] == "A4")
                        width: calc(297mm * {{intval($config["percent"]) / 100}});
            height: calc(210mm * {{intval($config["percent"]) / 100}});
            @elseif($config["page"] == "A5")
width: calc(210mm * {{intval($config["percent"]) / 100}});
            height: calc(148mm * {{intval($config["percent"]) / 100}});
            @endif
        @elseif($config["orientation"] == "portrait")
            @if($config["page"] == "A4")
width: calc(210mm * {{intval($config["percent"]) / 100}});
            height: calc(297mm * {{intval($config["percent"]) / 100}});
            @elseif($config["page"] == "A5")
width: calc(148mm * {{intval($config["percent"]) / 100}});
            height: calc(210mm * {{intval($config["percent"]) / 100}});
            @endif
        @endif
    @else
width: calc(210mm * 0.75);
            height: calc(297mm * 0.75);
            @endif
            border: 1px solid #c0c0c0;
            border-radius: 10px;
            box-shadow: 0 0 15px 1px #cecece;
            page-break-after: avoid;
        }
        .print-toolbar{
            top:0;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            width: 100vw;
            right: 0;
        }
        .print-toolbar-icon{
            transition: all 0.2s linear;
            cursor: pointer;
        }
        .print-toolbar-icon:hover{
            color: #08c9ff;
        }
        .doc-img{
            @if($config != null) @if($config["orientation"] == "landscape") max-height: 100%; width: auto; @elseif($config["orientation"] == "portrait") max-width: 100%; height: auto; @endif @else max-width: 100%; height: auto; @endif
        }
        @media print {
            html,body{
                padding: 0;
                margin: 0;
                @if($config != null)
                    @if($config["orientation"] == "landscape")
                        @if($config["page"] == "A4")
                            width: 297mm;
                height: 210mm;
                @elseif($config["page"] == "A5")
width: 210mm;
                height: 148mm;
                @endif
            @elseif($config["orientation"] == "portrait")
                @if($config["page"] == "A4")
width: 210mm;
                height: 297mm;
                @elseif($config["page"] == "A5")
width: 148mm;
                height: 210mm;
                @endif
           @endif
       @else
width: 210mm;
                height: 297mm;
                @endif
border: none;
                border-radius: 0;
                box-shadow: none;
            }
            .print-page{
                background: #ffffff;
                padding: 0!important;
                margin: 0!important;
                overflow: hidden;
                @if($config != null)
                    @if($config["orientation"] == "landscape")
                        @if($config["page"] == "A4")
                            width: 297mm;
                height: 210mm;
                @elseif($config["page"] == "A5")
width: 210mm;
                height: 148mm;
                @endif
            @elseif($config["orientation"] == "portrait")
                @if($config["page"] == "A4")
width: 210mm;
                height: 297mm;
                @elseif($config["page"] == "A5")
width: 148mm;
                height: 210mm;
                @endif
           @endif
       @else
width: 210mm;
                height: 297mm;
            @endif
            border: none;
                border-radius: 0;
                box-shadow: none;
}
            .print-toolbar{
                display: none!important;
            }
            .doc-image{
                padding: 0;
                margin: 0;
            }
        }
    </style>
    <script>
        let page_config = @json($config);
        let action_route = '{{ route("print_docs",["path" => $path]) }}';
    </script>
</head>
<body>
<div id="app">
    <loading v-show="show_loading" v-cloak></loading>
    <div class="print-toolbar position-fixed p-3 d-flex flex-row align-items-center justify-content-between gap-4">
        <i class="print-toolbar-icon fa fa-print fa-2x white-color" v-on:click="print_current_page"></i>
        <div class="d-flex flex-row align-items-center justify-content-center gap-3">
            <div>
                <select class="form-control iransans selectpicker-select" v-model="print_page_size" v-on:change="page_setup">
                    <option data-icon="fa fa-file fa-1-2x" @if($config != null && $config["page"] == 'A4') selected @endif value="A4">صفحه A4</option>
                    <option data-icon="fa fa-file fa-1-2x" @if($config != null && $config["page"] == 'A5') selected @endif value="A5">صفحه A5</option>
                </select>
            </div>
            <div>
                <select class="form-control iransans selectpicker-select" v-model="print_page_orientation" v-on:change="page_setup">
                    <option data-icon="fa fa-retweet fa-1-2x" @if($config != null && $config["orientation"] == 'portrait') selected @endif value="portrait">عمودی</option>
                    <option data-icon="fa fa-retweet fa-1-2x" @if($config != null && $config["orientation"] == 'landscape') selected @endif value="landscape">افقی</option>
                </select>
            </div>
            <div>
                <select class="form-control iransans selectpicker-select" v-model="print_preview_percent" v-on:change="page_setup">
                    <option data-icon="fa fa-display fa-1-2x" @if($config != null && $config["percent"] == 75) selected @endif value="75">75% ابعاد اصلی</option>
                    <option data-icon="fa fa-display fa-1-2x" @if($config != null && $config["percent"] == 100) selected @endif value="100">100% ابعاد اصلی</option>
                    <option data-icon="fa fa-display fa-1-2x" @if($config != null && $config["percent"] == 125) selected @endif value="125">125% ابعاد اصلی</option>
                </select>
            </div>
        </div>
    </div>
    <div class="bg-white text-center print-page m-auto">
        <img alt="همیاران شمال شرق" class="doc-img" src="{{ "data:image/png;base64,$image" }}"/>
    </div>
</div>
<script src="{{ asset('/js/app.js?v='.time()) }}"></script>
</body>
</html>
