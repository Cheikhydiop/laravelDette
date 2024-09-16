<?php
namespace App\Http\Controllers;
use App\Exports\DettesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function export()
    {
        return Excel::download(new DettesExport, 'dettes.xlsx');
    }
}
