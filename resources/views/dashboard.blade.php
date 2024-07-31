@extends('layouts.base')
<!-- @section('title', 'Dashboard') -->
@section('content')
<div class="container">
    <div class="page-inner">
        <style>
            .card {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.6s ease-out, transform 0.6s ease-out;
            }

            .card.show {
                opacity: 1;
                transform: translateY(0);
            }
        </style>
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Dashboard</h3>
                <h6 class="op-7 mb-2">List Pengeluaran Ambarawa</h6>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/ac') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-snowflake"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran AC</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($ac ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/bbm') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-burn"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran BBM</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($bbm ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/bubut') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-screwdriver"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Bubut</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($bubut ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/cat') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-paint-roller"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Cat</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($cat ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/operasional') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Operasional</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($ops ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/poles') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-car-alt"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Poles Kaca Mobil</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($poles ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/sembako') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-shopping-basket"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Sembako</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($sembako ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/sparepartamb') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-cogs"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Sparepart</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($sparepart ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/batu') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                    <i class="fas fa-chess-board"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Batu</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($batu ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/besi') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                    <i class="fas fa-chess-board"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Besi</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($besi ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/pasir') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                    <i class="fas fa-chess-board"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Pasir</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($pasir ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/pengurugan') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                    <i class="fas fa-chess-board"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Pengurugan</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($pengurugan ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <a href="{{ url('/trip') }}" class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-shuttle-van"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pengeluaran Perjalanan</p>
                                    <h4 class="card-title">{{ 'Rp ' . number_format($trip ?? 0, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');

        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('show');
            }, index * 200);
        });
    });
</script>
@endsection