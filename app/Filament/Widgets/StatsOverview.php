<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;


class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $pemasukan = Transaction::incomes()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');
        $pengeluaran = Transaction::expenses()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');


        return [
            Stat::make('Total Pemasukan', 'Rp' . ' ' . $pemasukan)
                ->description('Peningkatan')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([1, 2, 10, 17])
                ->color('success'),
            Stat::make('Total Pengeluaran', 'Rp' . ' ' . $pengeluaran)
                ->description('Penurunan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([4, 3])
                ->color('danger'),
            Stat::make('Selisih', 'Rp' . ' ' . $pemasukan - $pengeluaran)
                ->description('3% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
