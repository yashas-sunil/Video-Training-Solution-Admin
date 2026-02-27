<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (!Auth::check() || Auth::user()->role != 3) {
            return redirect()
                ->route('user.dashboard')
                ->with('error', 'You are not authorized to access the home page.');
        }
        
        $signUpCount = Student::whereDate('created_at', '>', Carbon::now()->subDays(7))->latest()->count();
        $purchaseCount = OrderItem::whereIn('payment_status', [
            OrderItem::PAYMENT_STATUS_PARTIALLY_PAID,
            OrderItem::PAYMENT_STATUS_FULLY_PAID
        ])->whereDate('created_at', '>', Carbon::now()->subDays(7))
            ->latest()
            ->count();
        $purchaseAmount = OrderItem::whereIn('payment_status', [
            OrderItem::PAYMENT_STATUS_PARTIALLY_PAID,
            OrderItem::PAYMENT_STATUS_FULLY_PAID
        ])->whereDate('created_at', '>', Carbon::now()->subDays(7))
            ->latest()
            ->sum('price');
        $draftedPackagesCount = Package::where('is_approved', false)->count();
        $publishedPackagesCount = Package::where('is_approved', true)->count();
        $preBookCount = OrderItem::where('is_prebook', true)
            ->where('payment_status', OrderItem::PAYMENT_STATUS_PARTIALLY_PAID)
            ->whereDate('created_at', '>', Carbon::now()->subDays(7))
            ->latest()
            ->count();
        $fullPaymentCount = OrderItem::where('is_prebook', true)
            ->where('payment_status', OrderItem::PAYMENT_STATUS_FULLY_PAID)
            ->whereDate('created_at', '>', Carbon::now()->subDays(7))
            ->latest()
            ->count();

        $orders = Order::where('third_party_id', Auth::id())->count();

        return view('home', compact(
            'signUpCount',
            'purchaseCount',
            'purchaseAmount',
            'draftedPackagesCount',
            'publishedPackagesCount',
            'preBookCount',
            'fullPaymentCount',
            'orders'
        ));
    }
}
