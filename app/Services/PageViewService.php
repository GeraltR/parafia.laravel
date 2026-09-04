<?php

namespace App\Services;

use App\Models\PageView;
use Illuminate\Support\Carbon;

class PageViewService
{
    public function record(string $path, ?string $referrer, string $ip, ?string $userAgent): void
    {
        PageView::create([
            'path' => $path,
            'referrer' => $referrer,
            'visitor_hash' => hash('sha256', $ip.'|'.($userAgent ?? '').'|'.config('app.key')),
        ]);
    }

    public function summary(int $days): array
    {
        $from = Carbon::today()->subDays($days - 1);

        $dailyRows = PageView::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as unique_visitors')
            ->where('created_at', '>=', $from)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $daily = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $from->copy()->addDays($i)->toDateString();
            $row = $dailyRows->get($date);
            $daily[] = [
                'date' => $date,
                'views' => (int) ($row->views ?? 0),
                'uniqueVisitors' => (int) ($row->unique_visitors ?? 0),
            ];
        }

        $topPaths = PageView::query()
            ->selectRaw('path, COUNT(*) as views')
            ->where('created_at', '>=', $from)
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(fn ($row) => ['path' => $row->path, 'views' => (int) $row->views])
            ->all();

        return [
            'days' => $days,
            'totalViews' => (int) PageView::where('created_at', '>=', $from)->count(),
            'totalUniqueVisitors' => (int) PageView::where('created_at', '>=', $from)->distinct('visitor_hash')->count('visitor_hash'),
            'daily' => $daily,
            'topPaths' => $topPaths,
        ];
    }
}
