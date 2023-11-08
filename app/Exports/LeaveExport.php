<?php

namespace App\Exports;

use App\Models\LeaveRequestDetails;
use Maatwebsite\Excel\Concerns\FromCollection;

class LeaveExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LeaveRequestDetails::all();
    }
}
