@extends('adminlte::page')

@section('title', 'EduEdgePro Admin')

@section('content_header')
    <h1 class="m-0 text-dark">Dashboard</h1>
@stop

<style>
    .small-box p {
        font-size: 14px!important;
        font-weight: bold;
        color: white;
    }
    .small-box h3 {
        color: white;
    }
    .small-box a {
        color: white!important;
    }
</style>

@section('content')
    <div class="row">
    @if(\Illuminate\Support\Facades\Auth::user()->role==13)

    @else
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(\Illuminate\Support\Facades\Auth::user()->role==12)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $orders }}</h3>
                                        <p>Orders</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user"></i>
                                    </div>
{{--                                    <a href="{{ url('students') }}" class="small-box-footer">--}}
{{--                                        More Info <i class="fas fa-arrow-circle-right"></i>--}}
{{--                                    </a>--}}
                                </div>
                            </div>
                        </div>
                    @else
                    <div class="row">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $signUpCount }}</h3>
                                    <p>SIGN-UPS (Last 7 Days)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <a href="{{ url('students') }}" class="small-box-footer">
                                    More Info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $purchaseCount }}</h3>
                                    <p>PURCHASES (Last 7 Days)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <a href="{{ url('orders') }}" class="small-box-footer">
                                    More Info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>₹{{ $purchaseAmount }}</h3>
                                    <p>PURCHASES (Last 7 Days)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-rupee-sign"></i>
                                </div>
                                <a href="{{ url('orders') }}" class="small-box-footer">
                                    More Info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-blue">
                                <div class="inner">
                                    <h3>{{ $draftedPackagesCount }}</h3>
                                    <p>DRAFTED PACKAGES</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-photo-video"></i>
                                </div>
                                <a href="{{ url('package-reports?published=false') }}" class="small-box-footer">
                                    More Info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $publishedPackagesCount }}</h3>
                                    <p>PUBLISHED PACKAGES</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-photo-video"></i>
                                </div>
                                <a href="{{ url('package-reports?published=true') }}" class="small-box-footer">
                                    More Info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-purple">
                                <div class="inner">
                                    <h3>{{ $preBookCount }}</h3>
                                    <p>PREBOOKING (Last 7 Days)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-file"></i>
                                </div>
                                <a href="{{ url('sales?p_date=2') }}" class="small-box-footer">
                                    More Info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-orange">
                                <div class="inner">
                                    <h3>{{ $fullPaymentCount }}</h3>
                                    <p>PREBOOK FULL PAYMENTS (Last 7 Days)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <a href="{{ url('sales?p_date=1') }}" class="small-box-footer">
                                    More Info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                        @endif

                </div>
            </div>
        </div>
        @endif
    </div>
@stop
