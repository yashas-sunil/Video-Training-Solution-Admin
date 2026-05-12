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

        $source = 'web';

        // Detect mobile app
        if (request()->header('X-App-Type') === 'android') {
            $source = 'android_app';
        }

        if (request()->header('X-App-Type') === 'ios') {
            $source = 'ios_app';
        }

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'role'       => auth()->user()->role ?? 'user',
            'module'     => $request->segment(1), // url ka first part
            'action'     => $request->method(),   // GET, POST
            'message'    => 'Visited '.$request->path(),
            'ip_address' => $request->ip(),
            'device'     => $source,
            'browser'    => $agent->browser(),
            'platform'   => $agent->platform(),
            'url'        => $request->fullUrl(),
            'http_method'=> $request->method(),
        ]);
    }

    return $response;
}
}
