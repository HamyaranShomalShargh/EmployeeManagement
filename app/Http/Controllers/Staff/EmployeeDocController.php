<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class EmployeeDocController extends Controller
{
    public function image_view($path): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::disk("employee_docs")->download(str_replace("@","/",$path));
    }
    public function download_application($path): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $file = Storage::disk("temporarily")->path(str_replace("@","/",$path));
        return Response::file($file,[
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline;'
        ])->deleteFileAfterSend(true);
    }
    public function print_docs($path,$config = null){
        $pdf = PDF::loadView('layouts.pdf.doc_print', ["image" => base64_encode(Storage::disk("employee_docs")->get(str_replace("@","/",$path)))],[], [
            'format' => 'A4-P'
        ]);
        Session::put('page-config',collect(json_decode($config,true)));
        Session::put('print-url',$path);
        return Response::download($pdf->stream('doc.pdf'),'doc.pdf',[],'inline');
    }
}
