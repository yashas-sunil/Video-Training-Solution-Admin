<?php

use App\ActivityLog as AppActivityLog;
use Jenssegers\Agent\Agent;

function activityLog($module, $action, $message = null)
{
    if (!auth()->check()) return;

    $agent = new Agent();

    AppActivityLog::create([
        'user_id'    => auth()->id(),
        'role'       => auth()->user()->role ?? 'user',
        'module'     => $module,
        'action'     => $action,
        'message'    => $message,
        'ip_address' => request()->ip(),
        'device'     => $agent->device(),
        'browser'    => $agent->browser(),
        'platform'   => $agent->platform(),
        'url'        => request()->fullUrl(),
        'http_method'=> request()->method(),
    ]);
}