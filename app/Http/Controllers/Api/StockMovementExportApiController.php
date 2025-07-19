<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\StockMovementsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class StockMovementExportApiController extends Controller
{
    public function export()
    {
        return Excel::download(new StockMovementsExport, 'stock_movements.xlsx');
    }
}
