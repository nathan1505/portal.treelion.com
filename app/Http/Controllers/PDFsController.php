<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PDFsController extends Controller
{
    public function pdfContactList(Request $request){
        return response()->file("/www/wwwroot/portal.treelion.com/resources/views/contact_list.pdf");
    }
    
    public function pdfDayoff(Request $request){
        return response()->file("/www/wwwroot/portal.treelion.com/resources/views/dayoff_application.pdf");
    }
}