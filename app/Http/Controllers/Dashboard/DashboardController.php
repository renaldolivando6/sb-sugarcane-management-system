<?php
namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        View::share([
            'navbar' => 'Dashboard',
        ]);

    }
    public function agronomi(Request $request)
    {
        $kdCompAgronomi = $request->input('companycode', []);
        $kdBlokAgronomi = $request->input('blok', []);
        $kdPlotAgronomi = $request->input('plot', []);
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        $title = "Dashboard Agronomi";
        $nav = "Agronomi";

        $verticalField = $request->input('vertical', 'per_germinasi');

        $verticalLabels = [
            'per_germinasi' => '% Germinasi',
            'per_gap' => '% GAP',
            'populasi' => 'Populasi',
            'per_gulma' => '% Penutupan Gulma',
            'ph_tanah' => 'pH Tanah',
        ];
        $verticalLabel = $verticalLabels[$verticalField] ?? ucfirst($verticalField);

        $chartData = [];
        $xAxis = [];

        $months = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12
        ];

        $monthsLabel = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $startMonthNum = $months[$startMonth] ?? null;
        $endMonthNum = $months[$endMonth] ?? null;

        if (!empty($kdCompAgronomi) || !empty($kdBlokAgronomi) || !empty($kdPlotAgronomi) || ($startMonthNum && $endMonthNum)) {
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
                ->select(
                    DB::raw("MONTH(agrohdr.tanggalpengamatan) as bln_amat"),
                    DB::raw("MIN(agrohdr.tanggaltanam) as tanggaltanam"),
                    'agrohdr.kat',
                    DB::raw("CASE
                        WHEN '$verticalField' IN ('populasi', 'ph_tanah')
                        THEN AVG($verticalField)
                        ELSE AVG($verticalField) * 100
                    END as total"),
                    'company.name as company_nama',
                    'blok.blok as blok_nama',
                    'plot.plot as plot_nama'
                )
                ->when($kdCompAgronomi, function ($query) use ($kdCompAgronomi) {
                    return $query->whereIn('agrohdr.companycode', $kdCompAgronomi);
                })
                ->when($kdBlokAgronomi, function ($query) use ($kdBlokAgronomi) {
                    return $query->whereIn('agrohdr.blok', $kdBlokAgronomi);
                })
                ->when($kdPlotAgronomi, function ($query) use ($kdPlotAgronomi) {
                    return $query->whereIn('agrohdr.plot', $kdPlotAgronomi);
                })
                ->when($startMonthNum && $endMonthNum, function ($query) use ($startMonthNum, $endMonthNum) {
                    return $query->whereBetween(DB::raw("MONTH(agrohdr.tanggalpengamatan)"), [$startMonthNum, $endMonthNum]);
                })
                ->groupBy('bln_amat', 'kat', 'company.name', 'blok.blok', 'plot.plot')
                ->orderBy('kat');

            $chartDataResult = $chartDataQuery->get();
            $chartDataResult->transform(function ($item) {
                $item->umur_tanam = ceil(Carbon::parse($item->tanggaltanam)->diffInMonths(Carbon::now())) . ' Bulan';
                return $item;
            });

            $xAxis = $chartDataResult->map(function ($item) use ($kdCompAgronomi, $kdBlokAgronomi, $kdPlotAgronomi) {
                if (!empty($kdCompAgronomi) && empty($kdBlokAgronomi) && empty($kdPlotAgronomi)) {
                    return $item->umur_tanam . ' - ' . $item->kat . ' - ' . $item->company_nama;
                } elseif (empty($kdCompAgronomi) && !empty($kdBlokAgronomi) && empty($kdPlotAgronomi)) {
                    return $item->umur_tanam . ' - ' . $item->kat . ' - ' . $item->blok_nama;
                } elseif (empty($kdCompAgronomi) && empty($kdBlokAgronomi) && !empty($kdPlotAgronomi)) {
                    return $item->plot_nama . ' - ' . $item->umur_tanam . ' - ' . $item->kat;
                } elseif (!empty($kdCompAgronomi) && empty($kdBlokAgronomi) && !empty($kdPlotAgronomi)) {
                    return $item->plot_nama . ' - ' . $item->umur_tanam . ' - ' . $item->kat . ' - ' . $item->company_nama;
                } elseif (empty($kdCompAgronomi) && !empty($kdBlokAgronomi) && !empty($kdPlotAgronomi)) {
                    return $item->plot_nama . ' - ' . $item->umur_tanam . ' - ' . $item->kat . ' - ' . $item->blok_nama;
                } elseif (!empty($kdCompAgronomi) && !empty($kdBlokAgronomi) && empty($kdPlotAgronomi)) {
                    return $item->umur_tanam . ' - ' . $item->kat . ' - ' . $item->blok_nama . ' - ' . $item->company_nama;
                } else {
                    return $item->plot_nama . ' - ' . $item->umur_tanam . ' - ' . $item->kat . ' - ' . $item->blok_nama . ' - ' . $item->company_nama;
                }
            })->unique()->values();

            $legends = $chartDataResult->pluck('bln_amat')->unique();

            foreach ($legends as $legend) {
                $data = [];

                foreach ($xAxis as $x) {
                    $data[] = round(
                        $chartDataResult->filter(function ($item) use ($legend, $x, $kdCompAgronomi, $kdBlokAgronomi, $kdPlotAgronomi) {

                            if (empty($kdCompAgronomi) && !empty($kdBlokAgronomi) && empty($kdPlotAgronomi)) {
                                $umur_tanam = explode(' - ', $x)[0];
                                $kat = explode(' - ', $x)[1];
                                $blok = explode(' - ', $x)[2];
                                return $item->bln_amat == $legend && $item->umur_tanam == $umur_tanam && $item->kat == $kat && $item->blok_nama == $blok;
                            } elseif (!empty($kdCompAgronomi) && empty($kdBlokAgronomi) && empty($kdPlotAgronomi)) {
                                $umur_tanam = explode(' - ', $x)[0];
                                $kat = explode(' - ', $x)[1];
                                $company = explode(' - ', $x)[2];
                                return $item->bln_amat == $legend && $item->umur_tanam == $umur_tanam && $item->kat == $kat && $item->company_nama == $company;
                            } elseif (empty($kdCompAgronomi) && empty($kdBlokAgronomi) && !empty($kdPlotAgronomi)) {
                                $plot = explode(' - ', $x)[0];
                                $umur_tanam = explode(' - ', $x)[1];
                                $kat = explode(' - ', $x)[2];
                                return $item->bln_amat == $legend && $item->plot_nama == $plot && $item->umur_tanam == $umur_tanam && $item->kat == $kat;
                            } elseif (!empty($kdCompAgronomi) && empty($kdBlokAgronomi) && !empty($kdPlotAgronomi)) {
                                $plot = explode(' - ', $x)[0];
                                $umur_tanam = explode(' - ', $x)[1];
                                $kat = explode(' - ', $x)[2];
                                $company = explode(' - ', $x)[3];
                                return $item->bln_amat == $legend && $item->plot_nama == $plot && $item->umur_tanam == $umur_tanam && $item->kat == $kat && $item->company_nama == $company;
                            } elseif (empty($kdCompAgronomi) && !empty($kdBlokAgronomi) && !empty($kdPlotAgronomi)) {
                                $plot = explode(' - ', $x)[0];
                                $umur_tanam = explode(' - ', $x)[1];
                                $kat = explode(' - ', $x)[2];
                                $blok = explode(' - ', $x)[3];
                                return $item->bln_amat == $legend && $item->plot_nama == $plot && $item->umur_tanam == $umur_tanam && $item->kat == $kat && $item->blok_nama == $blok;
                            } elseif (!empty($kdCompAgronomi) && !empty($kdBlokAgronomi) && empty($kdPlotAgronomi)) {
                                $umur_tanam = explode(' - ', $x)[0];
                                $kat = explode(' - ', $x)[1];
                                $blok = explode(' - ', $x)[2];
                                $company = explode(' - ', $x)[3];
                                return $item->bln_amat == $legend && $item->umur_tanam == $umur_tanam && $item->kat == $kat && $item->blok_nama == $blok && $item->company_nama == $company;
                            } else {
                                $plot = explode(' - ', $x)[0];
                                $umur_tanam = explode(' - ', $x)[1];
                                $kat = explode(' - ', $x)[2];
                                $blok = explode(' - ', $x)[3];
                                $company = explode(' - ', $x)[4];
                                return $item->bln_amat == $legend && $item->plot_nama == $plot && $item->umur_tanam == $umur_tanam && $item->kat == $kat && $item->blok_nama == $blok && $item->company_nama == $company;
                            }
                        })->avg('total'),
                        2
                    );
                }

                $monthName = Carbon::createFromFormat('m', $legend)->translatedFormat('F');

                $chartData[] = [
                    'label' => $monthName,
                    'data' => $data,
                ];
            }
        }

        $kdCompAgroOpt = DB::table('company')
            ->join('agrohdr', 'company.companycode', '=', 'agrohdr.companycode')
            ->select('company.companycode', 'company.name')
            ->distinct()
            ->get();
        $kdBlokAgroOpt = DB::table('blok')
            ->join('agrohdr', 'blok.blok', '=', 'agrohdr.blok')
            ->select('blok.blok')
            ->distinct()
            ->get();
        $kdPlotAgroOpt = DB::table('plot')
            ->join('agrohdr', 'plot.plot', '=', 'agrohdr.plot')
            ->select('plot.plot')
            ->orderByRaw("LEFT(plot.plot, 1), CAST(SUBSTRING(plot.plot, 2) AS UNSIGNED)")
            ->distinct()
            ->get();

        if (!empty($kdCompAgronomi) && empty($kdBlokAgronomi) && empty($kdPlotAgronomi)) {
            $horizontalLabel = 'Umur - Kategori - Kebun';
        } elseif (empty($kdCompAgronomi) && !empty($kdBlokAgronomi) && empty($kdPlotAgronomi)) {
            $horizontalLabel = 'Umur - Kategori - Blok';
        } elseif (empty($kdCompAgronomi) && empty($kdBlokAgronomi) && !empty($kdPlotAgronomi)) {
            $horizontalLabel = 'Plot - Umur - Kategori';
        } elseif (!empty($kdCompAgronomi) && empty($kdBlokAgronomi) && !empty($kdPlotAgronomi)) {
            $horizontalLabel = 'Plot - Umur - Kategori - Kebun';
        } elseif (empty($kdCompAgronomi) && !empty($kdBlokAgronomi) && !empty($kdPlotAgronomi)) {
            $horizontalLabel = 'Plot - Umur - Kategori - Blok';
        } elseif (!empty($kdCompAgronomi) && !empty($kdBlokAgronomi) && empty($kdPlotAgronomi)) {
            $horizontalLabel = 'Umur - Kategori - Blok - Kebun';
        } else {
            $horizontalLabel = 'Plot - Umur - Kategori - Blok - Kebun';
        }

        return view('dashboard.agronomi.index', compact(
            'chartData',
            'xAxis',
            'verticalField',
            'verticalLabel',
            'verticalLabels',
            'horizontalLabel',
            'kdCompAgronomi',
            'kdBlokAgronomi',
            'kdPlotAgronomi',
            'kdCompAgroOpt',
            'kdBlokAgroOpt',
            'kdPlotAgroOpt',
            'title',
            'nav',
            'startMonth',
            'endMonth',
            'monthsLabel'
        ));
    }

    public function hpt(Request $request)
    {
        $kdCompHPT = $request->input('companycode', []);
        $kdBlokHPT = $request->input('blok', []);
        $kdPlotHPT = $request->input('plot', []);
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        $title = "Dashboard HPT";
        $nav = "HPT";

        $verticalField = $request->input('vertical', 'per_ppt');

        $verticalLabels = [
            'per_ppt' => '% PPT',
            'per_PBT' => '% PBT',
            'dh' => 'Dead Heart',
            'dt' => 'Dead Top',
            'kbp' => 'Kutu Bulu Putih',
            'kbb' => 'Kutu Bulu Babi',
            'kp' => 'Kutu Perisai',
            'cabuk' => 'Cabuk',
            'belalang' => 'Belalang',
            'jum_grayak' => 'Ulat Grayak',
            'serang_smut' => 'SMUT',
        ];
        $verticalLabel = $verticalLabels[$verticalField] ?? ucfirst($verticalField);

        $chartData = [];
        $xAxis = [];

        $months = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12
        ];

        $monthsLabel = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $startMonthNum = $months[$startMonth] ?? null;
        $endMonthNum = $months[$endMonth] ?? null;

        if (!empty($kdCompHPT) || !empty($kdBlokHPT) || !empty($kdPlotHPT) || ($startMonthNum && $endMonthNum)) {
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
                    DB::raw("MONTH(hpthdr.tanggalpengamatan) as bln_amat"),
                    DB::raw("MIN(hpthdr.tanggaltanam) as tanggaltanam"),
                    DB::raw("CASE
                        WHEN '$verticalField' IN ('per_ppt', 'per_pbt')
                        THEN AVG($verticalField) * 100
                        ELSE AVG($verticalField)
                    END as total"),
                    'company.name as company_nama',
                    'blok.blok as blok_nama',
                    'plot.plot as plot_nama'
                )
                ->when($kdCompHPT, function ($query) use ($kdCompHPT) {
                    return $query->whereIn('hpthdr.companycode', $kdCompHPT);
                })
                ->when($kdBlokHPT, function ($query) use ($kdBlokHPT) {
                    return $query->whereIn('hpthdr.blok', $kdBlokHPT);
                })
                ->when($kdPlotHPT, function ($query) use ($kdPlotHPT) {
                    return $query->whereIn('hpthdr.plot', $kdPlotHPT);
                })
                ->when($startMonthNum && $endMonthNum, function ($query) use ($startMonthNum, $endMonthNum) {
                    return $query->whereBetween(DB::raw("MONTH(hpthdr.tanggalpengamatan)"), [$startMonthNum, $endMonthNum]);
                })
                ->groupBy('company.name', 'blok.blok', 'plot.plot', 'bln_amat')
                ->orderBy('plot_nama');


            $chartDataResult = $chartDataQuery->get();
            $chartDataResult->transform(function ($item) {
                $item->umur_tanam = ceil(Carbon::parse($item->tanggaltanam)->diffInMonths(Carbon::now())) . ' Bulan';
                return $item;
            });

            $xAxis = $chartDataResult->map(function ($item) use ($kdCompHPT, $kdBlokHPT, $kdPlotHPT) {
                if (!empty($kdCompHPT) && empty($kdBlokHPT) && empty($kdPlotHPT)) {
                    return $item->umur_tanam . ' - ' . $item->company_nama;
                } elseif (empty($kdCompHPT) && !empty($kdBlokHPT) && empty($kdPlotHPT)) {
                    return $item->umur_tanam . ' - ' . $item->blok_nama;
                } elseif (empty($kdCompHPT) && empty($kdBlokHPT) && !empty($kdPlotHPT)) {
                    return $item->plot_nama . ' - ' . $item->umur_tanam;
                } elseif (!empty($kdCompHPT) && empty($kdBlokHPT) && !empty($kdPlotHPT)) {
                    return $item->plot_nama . ' - ' . $item->umur_tanam . ' - ' . $item->company_nama;
                } elseif (empty($kdCompHPT) && !empty($kdBlokHPT) && !empty($kdPlotHPT)) {
                    return $item->plot_nama . ' - ' . $item->umur_tanam . ' - ' . $item->blok_nama;
                } elseif (!empty($kdCompHPT) && !empty($kdBlokHPT) && empty($kdPlotHPT)) {
                    return $item->umur_tanam . ' - ' . $item->blok_nama . ' - ' . $item->company_nama;
                } else {
                    return $item->plot_nama . ' - ' . $item->umur_tanam . ' - ' . $item->blok_nama . ' - ' . $item->company_nama;
                }
            })->unique()->values();

            $legends = $chartDataResult->pluck('bln_amat')->unique();

            foreach ($legends as $legend) {
                $data = [];

                foreach ($xAxis as $x) {
                    $data[] = round(
                        $chartDataResult->filter(function ($item) use ($legend, $x, $kdCompHPT, $kdBlokHPT, $kdPlotHPT) {

                            if (empty($kdCompHPT) && !empty($kdBlokHPT) && empty($kdPlotHPT)) {
                                $umur_tanam = explode(' - ', $x)[0];
                                $blok = explode(' - ', $x)[1];
                                return $item->bln_amat == $legend && $item->umur_tanam == $umur_tanam && $item->blok_nama == $blok;
                            } elseif (!empty($kdCompHPT) && empty($kdBlokHPT) && empty($kdPlotHPT)) {
                                $umur_tanam = explode(' - ', $x)[0];
                                $company = explode(' - ', $x)[1];
                                return $item->bln_amat == $legend && $item->umur_tanam == $umur_tanam && $item->company_nama == $company;
                            } elseif (empty($kdCompHPT) && empty($kdBlokHPT) && !empty($kdPlotHPT)) {
                                $plot = explode(' - ', $x)[0];
                                $umur_tanam = explode(' - ', $x)[1];
                                return $item->bln_amat == $legend && $item->plot_nama == $plot && $item->umur_tanam == $umur_tanam;
                            } elseif (!empty($kdCompHPT) && empty($kdBlokHPT) && !empty($kdPlotHPT)) {
                                $plot = explode(' - ', $x)[0];
                                $umur_tanam = explode(' - ', $x)[1];
                                $company = explode(' - ', $x)[2];
                                return $item->bln_amat == $legend && $item->plot_nama == $plot && $item->umur_tanam == $umur_tanam && $item->company_nama == $company;
                            } elseif (empty($kdCompHPT) && !empty($kdBlokHPT) && !empty($kdPlotHPT)) {
                                $plot = explode(' - ', $x)[0];
                                $umur_tanam = explode(' - ', $x)[1];
                                $blok = explode(' - ', $x)[2];
                                return $item->bln_amat == $legend && $item->plot_nama == $plot && $item->umur_tanam == $umur_tanam && $item->blok_nama == $blok;
                            } elseif (!empty($kdCompHPT) && !empty($kdBlokHPT) && empty($kdPlotHPT)) {
                                $umur_tanam = explode(' - ', $x)[0];
                                $blok = explode(' - ', $x)[1];
                                $company = explode(' - ', $x)[2];
                                return $item->bln_amat == $legend && $item->umur_tanam == $umur_tanam && $item->blok_nama == $blok && $item->company_nama == $company;
                            } else {
                                $plot = explode(' - ', $x)[0];
                                $umur_tanam = explode(' - ', $x)[1];
                                $blok = explode(' - ', $x)[2];
                                $company = explode(' - ', $x)[3];
                                return $item->bln_amat == $legend && $item->plot_nama == $plot && $item->umur_tanam == $umur_tanam && $item->blok_nama == $blok && $item->company_nama == $company;
                            }
                        })->avg('total'),
                        2
                    );
                }

                $monthName = Carbon::createFromFormat('m', $legend)->translatedFormat('F');

                $chartData[] = [
                    'label' => $monthName,
                    'data' => $data,
                ];
            }
        }

        $kdCompHPTOpt = DB::table('company')
            ->join('hpthdr', 'company.companycode', '=', 'hpthdr.companycode')
            ->select('company.companycode', 'company.name')
            ->distinct()
            ->get();
        $kdBlokHPTOpt = DB::table('blok')
            ->join('hpthdr', 'blok.blok', '=', 'hpthdr.blok')
            ->select('blok.blok')
            ->distinct()
            ->get();
        $kdPlotHPTOpt = DB::table('plot')
            ->join('hpthdr', 'plot.plot', '=', 'hpthdr.plot')
            ->select('plot.plot')
            ->orderByRaw("LEFT(plot.plot, 1), CAST(SUBSTRING(plot.plot, 2) AS UNSIGNED)")
            ->distinct()
            ->get();

        if (!empty($kdCompHPT) && empty($kdBlokHPT) && empty($kdPlotHPT)) {
            $horizontalLabel = 'Umur - Kebun';
        } elseif (empty($kdCompHPT) && !empty($kdBlokHPT) && empty($kdPlotHPT)) {
            $horizontalLabel = 'Umur - Blok';
        } elseif (empty($kdCompHPT) && empty($kdBlokHPT) && !empty($kdPlotHPT)) {
            $horizontalLabel = 'Plot - Umur';
        } elseif (!empty($kdCompHPT) && empty($kdBlokHPT) && !empty($kdPlotHPT)) {
            $horizontalLabel = 'Plot - Umur - Kebun';
        } elseif (empty($kdCompHPT) && !empty($kdBlokHPT) && !empty($kdPlotHPT)) {
            $horizontalLabel = 'Plot - Umur - Blok';
        } elseif (!empty($kdCompHPT) && !empty($kdBlokHPT) && empty($kdPlotHPT)) {
            $horizontalLabel = 'Umur - Blok - Kebun';
        } else {
            $horizontalLabel = 'Plot - Umur - Blok - Kebun';
        }

        return view('dashboard.hpt.index', compact(
            'chartData',
            'xAxis',
            'verticalField',
            'verticalLabel',
            'verticalLabels',
            'horizontalLabel',
            'kdCompHPT',
            'kdBlokHPT',
            'kdPlotHPT',
            'kdCompHPTOpt',
            'kdBlokHPTOpt',
            'kdPlotHPTOpt',
            'title',
            'nav',
            'startMonth',
            'endMonth',
            'monthsLabel'
        ));
    }
}
