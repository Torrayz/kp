<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Visitor;
use Carbon\Carbon;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        // Skip tracking untuk admin routes dan assets
        if ($request->is('admin/*') || 
            $request->is('login') ||
            $request->is('*.css') || 
            $request->is('*.js') || 
            $request->is('*.png') || 
            $request->is('*.jpg')) {
            return $next($request);
        }

        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $pageVisited = $request->fullUrl();
        $referrer = $request->header('referer');
        $visitDate = Carbon::today();

        // Cek apakah IP ini sudah mengunjungi halaman ini hari ini
        $existingVisit = Visitor::where('ip_address', $ipAddress)
                               ->where('page_visited', $pageVisited)
                               ->whereDate('visit_date', $visitDate)
                               ->first();

        if (!$existingVisit) {
            Visitor::create([
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'page_visited' => $pageVisited,
                'referrer' => $referrer,
                'visited_at' => Carbon::now(),
                'visit_date' => $visitDate
            ]);
        }

        return $next($request);
    }
}
