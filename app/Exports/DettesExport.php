<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class DettesExport implements FromCollection
{
    public function collection()
    {
        // Retourner les données à exporter
        return collect([
            ['Header1', 'Header2'],
            ['Row1-Cell1', 'Row1-Cell2'],
        ]);
    }
}
