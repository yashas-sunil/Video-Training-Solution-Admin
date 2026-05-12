<?php

namespace App\Http\Middleware;

use App\ActivityLog;
use Closure;
use Jenssegers\Agent\Agent;
class ActivityLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
   public function handle($request, Closure $next)
{
    $response = $next($request);

    if (auth()->check()) {

        $agent = new Agent();

        $source = 'ios_app';

        // Detect mobile app
        if (request()->header('sec-ch-ua-platform') === '"Android"') {
            $source = 'android_app';
        }

        if (request()->header('sec-ch-ua-platform') === '"Windows"') {
            $source = 'Windows';
        }

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'role'       => auth()->user()->role ?? 'user',
            'module'     => $request->segment(1), 
            'action'     => $request->method(),   // GET, POST
            'message'    => 'Visited '.$request->path(),
            'ip_address' => $request->ip(),
            'device'     => $request->userAgent(),
            'browser'    => $agent->browser(),
            'platform'   => $agent->platform(),
            'url'        => $request->fullUrl(),
            'http_method'=> $request->method(),
        ]);
    }

    return $response;
}
}
