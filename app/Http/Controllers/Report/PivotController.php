<?php
namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PivotController extends Controller
{
    public function pivotTableAgronomi(Request $request)
    {

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $chartDataQuery = DB::table('agrohdr')
            ->join('agrolst', function ($join) {
                $join->on('agrohdr.nosample', '=', 'agrolst.nosample')
                    ->on('agrohdr.companycode', '=', 'agrolst.companycode');
            })
            ->join('company', 'agrohdr.companycode', '=', 'company.companycode')
            ->join('plot', function ($join) {
                $join->on('agrohdr.plot', '=', 'plot.plot')
                    ->on('agrohdr.companycode', '=', 'plot.companycode');
            })
            ->leftJoin('blok', function ($join) {
                $join->on('agrohdr.blok', '=', 'blok.blok')
                    ->whereColumn('agrohdr.companycode', '=', 'blok.companycode');
            })
            ->where('agrolst.companycode', session('companycode'))
            ->where('agrohdr.companycode', session('companycode'))
            ->where('agrohdr.status', '=', 'Posted')
            ->where('agrolst.status', '=', 'Posted')
            ->select(
                'company.name as company',
                'blok.blok as Blok',
                'plot.plot as Plot',
                DB::raw("MONTH(agrohdr.tanggalpengamatan) as Bulan"),
                DB::raw("AVG(per_germinasi) as Germinasi"),
                DB::raw("AVG(per_gap) as GAP"),
                DB::raw("AVG(populasi) as Populasi"),
                DB::raw("AVG(per_gulma) as PenutupanGulma"),
                DB::raw("AVG(ph_tanah) as PHTanah")
            )
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('agrohdr.tanggalpengamatan', [$startDate, $endDate]);
            })
            ->groupBy('Bulan', 'company', 'Blok', 'Plot')
            ->orderBy('Bulan', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pivot Table');

        $headers = ['Kebun', 'Blok', 'Plot', 'Bulan', 'Average of %Germinasi', 'Average of %GAP', 'Average of Populasi', 'Average of %Penutupan Gulma', 'Average of pH Tanah'];
        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ADD8E6'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $columnIndex = 1;
        foreach ($headers as $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            $columnIndex++;
        }
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $row = 2;
        $previousCompany = null;
        $previousBlok = null;
        $previousPlot = null;
        $startMergeCompanyRow = 2;
        $startMergeBlokRow = 2;
        $startMergePlotRow = 2;
        foreach ($chartDataQuery as $index => $data) {
            $currentCompany = $data->company;
            $currentBlok = $data->Blok;
            $currentPlot = $data->Plot;

            if ($previousCompany !== null && $currentCompany === $previousCompany) {
                $sheet->setCellValue("A$row", '');
            } else {
                $sheet->setCellValue("A$row", $currentCompany);
                $startMergeCompanyRow = $row;
            }

            if ($previousBlok !== null && $currentBlok === $previousBlok && $currentCompany === $previousCompany) {
                $sheet->setCellValue("B$row", '');
            } else {
                $sheet->setCellValue("B$row", $currentBlok);
                $startMergeBlokRow = $row;
            }

            if ($previousPlot !== null && $currentPlot === $previousPlot && $currentBlok === $previousBlok && $currentCompany === $previousCompany) {
                $sheet->setCellValue("C$row", '');
            } else {
                $sheet->setCellValue("C$row", $currentPlot);
                $startMergePlotRow = $row;
            }

            $sheet->fromArray([
                '',
                '',
                '',
                Carbon::createFromFormat('m', $data->Bulan)->translatedFormat('F'),
                round($data->Germinasi, 4),
                round($data->GAP, 4),
                round($data->Populasi, 4),
                round($data->PenutupanGulma, 4),
                round($data->PHTanah, 4),
            ], NULL, "A$row");

            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

            if (!isset($chartDataQuery[$index + 1]) || $chartDataQuery[$index + 1]->company !== $currentCompany) {
                if ($row > $startMergeCompanyRow) {
                    $sheet->mergeCells("A{$startMergeCompanyRow}:A{$row}");
                    $sheet->getStyle("A{$startMergeCompanyRow}:A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }

            if (!isset($chartDataQuery[$index + 1]) || $chartDataQuery[$index + 1]->Blok !== $currentBlok || $chartDataQuery[$index + 1]->company !== $currentCompany) {
                if ($row > $startMergeBlokRow) {
                    $sheet->mergeCells("B{$startMergeBlokRow}:B{$row}");
                    $sheet->getStyle("B{$startMergeBlokRow}:B{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }

            if (!isset($chartDataQuery[$index + 1]) || $chartDataQuery[$index + 1]->Plot !== $currentPlot || $chartDataQuery[$index + 1]->Blok !== $currentBlok || $chartDataQuery[$index + 1]->company !== $currentCompany) {
                if ($row > $startMergePlotRow) {
                    $sheet->mergeCells("C{$startMergePlotRow}:C{$row}");
                    $sheet->getStyle("C{$startMergePlotRow}:C{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }

            $previousCompany = $currentCompany;
            $previousBlok = $currentBlok;
            $previousPlot = $currentPlot;

            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        if ($startDate && $endDate) {
            $fileName = "AgronomiPivot_{$startDate}_sd_{$endDate}.xlsx";
        } else {
            $fileName = "AgronomiPivot.xlsx";
        }
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function pivotTableHPT(Request $request)
    {
        $kdCompAgronomi = $request->input('companycode', []);
        $kdBlokAgronomi = $request->input('blok', []);
        $kdPlotAgronomi = $request->input('plot', []);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query untuk mendapatkan data pivot table yang sama dengan fungsi agronomi
        $chartDataQuery = DB::table('hpthdr')
            ->join('hptlst', function ($join) {
                $join->on('hpthdr.nosample', '=', 'hptlst.nosample')
                    ->on('hpthdr.companycode', '=', 'hptlst.companycode');
            })
            ->join('plot', function ($join) {
                $join->on('hpthdr.plot', '=', 'plot.plot')
                    ->on('hpthdr.companycode', '=', 'plot.companycode');
            })
            ->join('company', 'hpthdr.companycode', '=', 'company.companycode')
            ->leftJoin('blok', function ($join) {
                $join->on('hpthdr.blok', '=', 'blok.blok')
                    ->whereColumn('hpthdr.companycode', '=', 'blok.companycode');
            })
            ->select(
                'company.name as company',
                'blok.blok as Blok',
                'plot.plot as Plot',
                DB::raw("MONTH(hpthdr.tanggalpengamatan) as Bulan"),
                DB::raw("AVG(per_ppt) as PPT"),
                DB::raw("AVG(per_pbt) as PBT"),
                DB::raw("AVG(dh) as DH"),
                DB::raw("AVG(dt) as DT"),
                DB::raw("AVG(kbp) as KBP"),
                DB::raw("AVG(kbb) as KBB"),
                DB::raw("AVG(kp) as KP"),
                DB::raw("AVG(cabuk) as Cabuk"),
                DB::raw("AVG(belalang) as Belalang"),
                DB::raw("AVG(jum_grayak) as Grayak"),
                DB::raw("AVG(serang_smut) as SMUT"),
            )
            ->when($kdCompAgronomi, function ($query) use ($kdCompAgronomi) {
                return $query->whereIn('hpthdr.companycode', $kdCompAgronomi);
            })
            ->when($kdBlokAgronomi, function ($query) use ($kdBlokAgronomi) {
                return $query->whereIn('hpthdr.blok', $kdBlokAgronomi);
            })
            ->when($kdPlotAgronomi, function ($query) use ($kdPlotAgronomi) {
                return $query->whereIn('hpthdr.plot', $kdPlotAgronomi);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('hpthdr.tanggalpengamatan', [$startDate, $endDate]);
            })
            ->groupBy('Bulan', 'company', 'Blok', 'Plot')
            ->orderBy('company', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pivot Table');

        $headers = [
            'Kebun',
            'Blok',
            'Plot',
            'Bulan',
            'Average of %PPT',
            'Average of %PBT',
            'Average of Dead Heart',
            'Average of Dead Top',
            'Average of Kutu Bulu Putih',
            'Average of Kutu Bulu Babi',
            'Average of Kutu Perisai',
            'Average of Cabuk',
            'Average of Belalang',
            'Average of Ulat Grayak',
            'Average of BTG Terserang SMUT',
        ];
        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $row = 2;
        foreach ($chartDataQuery as $data) {
            $sheet->fromArray([
                $data->company,
                $data->Blok,
                $data->Plot,
                Carbon::createFromFormat('m', $data->Bulan)->translatedFormat('F'),
                round($data->PPT, 4),
                round($data->PBT, 4),
                round($data->DH, 4),
                round($data->DT, 4),
                round($data->KBP, 4),
                round($data->KBB, 4),
                round($data->KP, 4),
                round($data->Cabuk, 4),
                round($data->Belalang, 4),
                round($data->Grayak, 4),
                round($data->SMUT, 4),
            ], NULL, "A$row");
            $sheet->getStyle('E' . $row)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getStyle('F' . $row)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

            $row++;
        }

        if ($startDate && $endDate) {
            $fileName = "HPT_PivotTable_{$startDate}_sd_{$endDate}.xlsx";
        } else {
            $fileName = "HPT_PivotTable.xlsx";
        }
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
