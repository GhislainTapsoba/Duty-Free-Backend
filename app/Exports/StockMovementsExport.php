<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;


class StockMovementsExport implements FromCollection
{
    public function collection()
    {
        return StockMovement::all();
    }
}
