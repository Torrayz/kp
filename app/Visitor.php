<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Visitor extends Model
{
    protected $fillable = [
        'ip_address',
        'user_agent', 
        'page_visited',
        'referrer',
        'visited_at',
        'visit_date'
    ];

    protected $dates = [
        'visited_at',
        'visit_date'
    ];

    // Get total unique visitors
    public static function getTotalVisitors()
    {
        return self::distinct('ip_address')->count();
    }

    // Get visitors data for chart (last 6 months)
    public static function getChartData()
    {
        $data = [];
        $labels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $visitors = self::whereYear('visit_date', $date->year)
                           ->whereMonth('visit_date', $date->month)
                           ->distinct('ip_address')
                           ->count();
            
            $labels[] = $monthName;
            $data[] = $visitors;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}