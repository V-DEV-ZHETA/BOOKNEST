<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyTransactionsChart extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return 'Statistik Peminjaman Bulanan ' . date('Y');
    }

    protected function getData(): array
    {
        $currentYear = date('Y');
        
        $stats = Peminjaman::select(
            DB::raw('MONTH(borrow_date) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('borrow_date', $currentYear)
        ->where('confirmation_status', 'approved')
        ->groupBy(DB::raw('MONTH(borrow_date)'))
        ->orderBy('month')
        ->get();

        $data = array_fill(0, 12, 0);
        foreach ($stats as $stat) {
            $data[$stat->month - 1] = $stat->count;
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        return [
            'datasets' => [
                [
                    'label' => 'Peminjaman',
                    'data' => $data,
                    'backgroundColor' => 'rgba(78, 115, 223, 0.2)',
                    'borderColor' => 'rgba(78, 115, 223, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.05)',
                    ],
                ],
            ],
        ];
    }
}

