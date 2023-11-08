<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;


class AttendanceExport implements FromView, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $data= $this->data;

            return view('exports.attendance-export', [
                'data' => $data
            ]);

    }
    public function headings(): array
    {
        return [
            'Name',
            'Date',
            'Checkin',
            'Checkout',
        ];
    }

    // public function collection()
    // {
    //     return Attendance::all();
    // }
}