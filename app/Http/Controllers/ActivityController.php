<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class ActivityController extends Controller
{
    public function index(Builder $builder){
        $users = DB::table('users')->select('id', 'name')->get();
        if (request()->ajax()) {
            $query = DB::table('activity_logs as al')
            ->leftJoin('users as u', 'u.id', '=', 'al.user_id')
            ->leftJoin('roles as r', 'r.id', '=', 'al.role')
            ->select(
                'al.id',
                'al.user_id',
                'u.name as user_name',
                'r.name as role',
                'al.module',
                'al.action',
                'al.message',
                'al.ip_address',
                'al.device',
                'al.browser',
                'al.platform',
                'al.url',
                'al.http_method',
                'al.created_at',
                'al.updated_at'
            )
            ->orderBy('al.id', 'desc');
            
            return DataTables::of($query)
            ->addIndexColumn() 
            ->filter(function($query) {
               if (request()->has('filter') && !empty(request('filter.search'))) {
    $users = request('filter.search');
    if (!empty($users)) {
    $users = array_filter($users); // removes "", null

        if (count($users) > 0) {
            $query->whereIn('al.user_id', $users);
        }
    }
}
                if (request()->filled('filter.date')) {
                    $dateRange = request()->input('filter.date');
                    $explodedDates = explode(' - ', $dateRange);
                    $fromDate = Carbon::createFromFormat('d/m/Y', $explodedDates[0]);
                    $toDate = Carbon::createFromFormat('d/m/Y', $explodedDates[1]);
                    $from= date("Y-m-d",strtotime($fromDate)).''.' 00:00:00';
                    $to= date("Y-m-d",strtotime($toDate)).''.' 23:59:59';
                    $query->whereBetween('al.created_at', [$from, $to]);
                }
        })
        ->editColumn('url', function($row) {

        return strtok($row->url, '?');

    })
            ->addColumn('created_at', function($query) {
                if(!empty($query->created_at)){
                    $datetime = explode(" ",$query->created_at);
                    $date = date("d-m-Y", strtotime($datetime[0]));
                    $date_time = $date.' '.$datetime[1];
                    return $date_time;
                }else{
                    return '-';
                }
                
            })
            ->make(true);
        }
        
        $table = $builder->columns([
    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SR No', 'orderable' => false],
    ['data' => 'user_name', 'name' => 'u.name', 'title' => 'User Name' , 'defaultContent' => '-', 'orderable' => false],
    ['data' => 'role', 'name' => 'al.role', 'title' => 'Role' , 'defaultContent' => '-', 'orderable' => false],
    ['data' => 'module', 'name' => 'al.module', 'title' => 'Module' , 'defaultContent' => '-', 'orderable' => false],
    ['data' => 'action', 'name' => 'al.action', 'title' => 'Action' , 'defaultContent' => '-', 'orderable' => false],
    ['data' => 'message', 'name' => 'al.message', 'title' => 'Message' , 'defaultContent' => '-', 'orderable' => false],
    ['data' => 'ip_address', 'name' => 'al.ip_address', 'title' => 'IP' , 'defaultContent' => '-', 'orderable' => false],
    ['data' => 'device', 'name' => 'al.device', 'title' => 'Device' , 'defaultContent' => '-', 'orderable' => false],
    ['data' => 'browser', 'name' => 'al.browser', 'title' => 'Browser' , 'defaultContent' => '-', 'orderable' => false],
    ['data' => 'platform', 'name' => 'al.platform', 'title' => 'Platform' , 'defaultContent' => '-', 'orderable' => false],
    ['data' => 'url', 'name' => 'al.url', 'title' => 'URL' , 'defaultContent' => '-', 'orderable' => false, 'className' => 'text-wrap-url'],
    ['data' => 'created_at', 'name' => 'al.created_at', 'title' => 'Created Date', 'orderable' => false],
     
        ])->parameters([
            'searching' => false,
            'ordering' => false,
            'processing' =>true,
            'lengthChange' => false,
            'stateSave'=>true,
            'bInfo' => false,
            'pageLength'=> 75,
        ])->orderBy(0, 'desc');

        return view('pages.activity-report.index', compact('table','users'));
    }
}
