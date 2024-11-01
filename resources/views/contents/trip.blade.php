@extends('layouts.base')
<!-- @section('title', 'Perjalanan') -->
@section('content')
<!-- style custom -->
<style>
    th,
    td {
        white-space: nowrap;
    }

    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
    
    .status-process {
        display: inline-block;
        padding: 5px 10px;
        font-size: 11.5px;
        font-weight: bold;
        color: black;
        background-color: #D5D5D5;
        border-radius: 10px;
        text-align: center;
        width: 80px;
    }

    .status-pending {
        display: inline-block;
        padding: 5px 10px;
        font-size: 11.5px;
        font-weight: bold;
        color: black;
        background-color: #FFCA00;
        border-radius: 10px;
        text-align: center;
        width: 80px;
    }

    .status-paid {
        display: inline-block;
        padding: 5px 10px;
        font-size: 11.5px;
        font-weight: bold;
        color: green;
        background-color: #88F9A7;
        border-radius: 10px;
        text-align: center;
        width: 80px;
    }
</style>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Data Pengeluaran Perjalanan</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ url('dashboard') }}">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Trip</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="tripModal" tabindex="-1" role="dialog" aria-labelledby="tripModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="tripModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Perjalanan Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('trip-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Perjalanan</span>
                            </div>
                            
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama.."
                                 oninput="this.value = this.value.toUpperCase()" required />
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" required />
                                </div>
                                <div class="col-4">
                                    <label for="kota">Kota Tujuan</label>
                                    <input type="text" class="form-control" name="kota" id="kota" placeholder="Kota tujuan.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                                <div class="col-4">
                                    <label for="ket">Keterangan</label>
                                    <input type="text" class="form-control" name="ket" id="ket" placeholder="Keterangan.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="uraian">Uraian</label>
                                <input type="text" class="form-control" name="uraian" id="uraian" placeholder="Masukkan uraian.." required />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kendaraan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    @if (count($kendaraan) > 0)
                                        <label for="kendaraan">Nopol / Kode Unit</label>
                                        <select class="form-select form-control" name="kendaraan" id="kendaraan" onchange="updateMerk()">
                                            <option value="">...</option>
                                            @foreach ($kendaraan as $k)
                                                <option value="{{ $k->id_kendaraan }}" data-merk="{{ $k->merk }}">{{ $k->nopol }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <label>Nopol / Kode Unit</label>
                                        <select class="form-control" disabled>
                                            <option value="">Tidak ada data</option>
                                        </select>
                                    @endif
                                </div>
                                <div class="col-3">
                                    <label for="merk">Merk</label>
                                    <input type="text" class="form-control" name="merk" id="merk" placeholder="Masukkan merk.." 
                                    oninput="this.value = this.value.toUpperCase()" style="background-color: #fff !important;" readonly />
                                </div>
                                <div class="col-3">
                                    <label for="qty">Jumlah</label>
                                    <input type="text" class="form-control" name="qty" id="qty" placeholder="Masukkan jumlah.." required />
                                </div>
                                <div class="col-3">
                                    <label for="unit">Satuan</label>
                                    <input type="text" class="form-control" name="unit" id="unit" placeholder="Masukkan satuan.." value="LITER" 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kilometer</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="km_awal">KM Awal</label>
                                    <input type="number" class="form-control" name="km_awal" min="0" id="km_awal" placeholder="km awal.." required />
                                </div>
                                <div class="col-6">
                                    <label for="km_akhir">KM Akhir</label>
                                    <input type="number" class="form-control" name="km_akhir" min="0" id="km_akhir" placeholder="km akhir.." required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="km_isi_seb">KM Pengisian (Sebelumnya)</label>
                                    <input type="number" class="form-control" name="km_isi_seb" min="0" id="km_isi_seb" placeholder="Pengisian sebelumnya.." required />
                                </div>
                                <div class="col-4">
                                    <label for="km_isi_sek">KM Pengisian (Saat ini)</label>
                                    <input type="number" class="form-control" name="km_isi_sek" min="0" id="km_isi_sek" placeholder="Pengisian saat ini.." required />
                                </div>
                                <div class="col-4">
                                    <label for="km_ltr">KM/Liter</label>
                                    <input type="text" class="form-control" name="km_ltr" id="km_ltr" placeholder="KM per liter.." style="background-color: #fff !important;" readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="harga" placeholder="Masukkan harga.." required />
                                </div>
                                <div class="col-6">
                                    <label for="total">Total Harga</label>
                                    <input type="text" class="form-control" name="total" id="total" placeholder="Nilai total harga.." style="background-color: #fff !important;" required />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="file">Upload file</label>
                                <input type="file" class="form-control" name="file" accept="application/pdf, image/png, image/jpeg">
                            </div>
                        </div>
                        <div class="modal-footer border-0 mx-2">
                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit data -->
        <div class="modal fade" id="tripEditModal" tabindex="-1" role="dialog" aria-labelledby="tripEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="tripEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Perjalanan </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('trip-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_trip">
                            <input type="hidden" name="page" value="{{ request()->get('page', 1) }}">

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Perjalanan</span>
                            </div>
                            
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama.."
                                 oninput="this.value = this.value.toUpperCase()" required />
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="edit-tanggal" required />
                                </div>
                                <div class="col-4">
                                    <label for="kota">Kota Tujuan</label>
                                    <input type="text" class="form-control" name="kota" id="edit-kota" placeholder="Kota tujuan.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                                <div class="col-4">
                                    <label for="ket">Keterangan</label>
                                    <input type="text" class="form-control" name="ket" id="edit-ket" placeholder="Keterangan.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="uraian">Uraian</label>
                                <input type="text" class="form-control" name="uraian" id="edit-uraian" placeholder="Masukkan uraian.." required />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kendaraan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    @if (count($kendaraan) > 0)
                                        <label for="kendaraan">Nopol / Kode Unit</label>
                                        <select class="form-select form-control" name="kendaraan" id="edit-kendaraan" onchange="updateEditMerk()">
                                            <option value="">...</option>
                                            @foreach ($kendaraan as $k)
                                                <option value="{{ $k->id_kendaraan }}" data-merk="{{ $k->merk }}">{{ $k->nopol }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <label>Nopol / Kode Unit</label>
                                        <select class="form-control" disabled>
                                            <option value="">Tidak ada data</option>
                                        </select>
                                    @endif
                                </div>
                                <div class="col-3">
                                    <label for="merk">Merk</label>
                                    <input type="text" class="form-control" name="merk" id="edit-merk" placeholder="Masukkan merk.." 
                                    oninput="this.value = this.value.toUpperCase()" style="background-color: #fff !important;" readonly />
                                </div>
                                <div class="col-3">
                                    <label for="qty">Jumlah</label>
                                    <input type="text" class="form-control" name="qty" id="edit-qty" placeholder="Masukkan jumlah.." required />
                                </div>
                                <div class="col-3">
                                    <label for="unit">Satuan</label>
                                    <input type="text" class="form-control" name="unit" id="edit-unit" placeholder="Masukkan satuan.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kilometer</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="km_awal">KM Awal</label>
                                    <input type="number" class="form-control" name="km_awal" min="0" id="edit-km_awal" placeholder="km awal.." required />
                                </div>
                                <div class="col-6">
                                    <label for="km_akhir">KM Akhir</label>
                                    <input type="number" class="form-control" name="km_akhir" min="0" id="edit-km_akhir" placeholder="km akhir.." required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="km_isi_seb">KM Pengisian (Sebelumnya)</label>
                                    <input type="number" class="form-control" name="km_isi_seb" min="0" id="edit-km_isi_seb" placeholder="Pengisian sebelumnya.." required />
                                </div>
                                <div class="col-4">
                                    <label for="km_isi_sek">KM Pengisian (Saat ini)</label>
                                    <input type="number" class="form-control" name="km_isi_sek" min="0" id="edit-km_isi_sek" placeholder="Pengisian saat ini.." required />
                                </div>
                                <div class="col-4">
                                    <label for="km_ltr">KM/Liter</label>
                                    <input type="text" class="form-control" name="km_ltr" id="edit-km_ltr" placeholder="km per liter.." style="background-color: #fff !important;" readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="edit-harga" placeholder="Masukkan harga.." required />
                                </div>
                                <div class="col-6">
                                    <label for="total">Total Harga</label>
                                    <input type="text" class="form-control" name="total" id="edit-total" placeholder="Nilai total harga.." style="background-color: #fff !important;" required />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="file">Upload file</label>
                                <input type="file" class="form-control" name="file" accept="application/pdf, image/png, image/jpeg">

                                <!-- Menampilkan file yang sudah diunggah -->
                                <div id="current-file" style="margin-top: 10px;">
                                    <!-- Isi file akan di-update melalui JavaScript -->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 mx-2">
                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Export data -->
        <div class="modal fade" id="tripExportModal" tabindex="-1" role="dialog" aria-labelledby="tripExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="tripExportModal">
                            <span class="fw-light"> Export Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Trip </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('trip-export') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Pengaturan untuk menentukan format data pengeluaran sesuai keinginan
                            </p>

                            <div class="form-group">
                                <label>Metode Export Data</label>
                                <div id="radioError" class="text-danger d-none">Silakan pilih salah satu metode export data.</div>
                                <div class="d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metode_export" id="all_data" value="all_data" onclick="toggleCustomFields()" />
                                        <label class="form-check-label" for="all_data">
                                            Tampilkan semua data
                                        </label>
                                    </div>
                                    <div class="form-check ms-3">
                                        <input class="form-check-input" type="radio" name="metode_export" id="custom" value="custom" onclick="toggleCustomFields()" />
                                        <label class="form-check-label" for="custom">
                                            Custom
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="customFields" class="d-none">
                                <div class="form-group">
                                    <input type="hidden" id="start_date" name="start_date">
                                    <input type="hidden" id="end_date" name="end_date">

                                    <label>Rentang Tanggal</label>
                                    <div id="dateError" class="text-danger d-none">Silakan pilih rentang tanggal.</div>
                                    <div class="col-12" id="reportrange" style="background: #fff; cursor: pointer; 
                                        padding: 10px 10px; border: 1px solid #ccc; border-radius:5px;">
                                        <i class="fa fa-calendar"> </i>&nbsp;
                                        <span id="reportrange_display"> Menampilkan data berdasarkan rentang tanggal </span> 
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer border-0 mx-2">
                            <button type="submit" class="btn btn-primary btn-sm">Export</button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- LogError -->
        @if(session()->has('logErrors'))
        <div class="row">
            <div class="col-md-12" style="max-height: 350px; overflow-y: auto;">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-sm text-danger">Error Log</h5>
                        @if(is_array(session('logErrors')))
                        @foreach(session('logErrors') as $logError)
                        {{ $logError }} <br>
                        @endforeach
                        @else
                        {{ session('logErrors') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Notify -->
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="card-title">List Pengeluaran Perjalanan</div>
                            <input type="hidden" id="allSelectRow" name="ids" value="">
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#tripEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('trip-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="deleteAllSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            @if (Auth::user()->level == 0)
                            <form id="statusPendingForm" method="POST" action="{{ url('trip-status_pending') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="pendingAllSelectRow" name="ids" value="">
                                <button id="statusPendingButton" type="button" class="btn btn-warning btn-round ms-5 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Pending
                                </button>
                            </form>
                            <form id="statusProcessForm" method="POST" action="{{ url('trip-status_process') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="processAllSelectRow" name="ids" value="">
                                <button id="statusProcessButton" type="button" class="btn btn-black btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Processing
                                </button>
                            </form>
                            <form id="statusPaidForm" method="POST" action="{{ url('trip-status_paid') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="paidAllSelectRow" name="ids" value="">
                                <button id="statusPaidButton" type="button" class="btn btn-success btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Paid
                                </button>
                            </form>
                            @endif
                            <div class="ms-auto d-flex align-items-center">
                                @if (count($trips) > 0)
                                    <button class="btn btn-success btn-round ms-2 btn-sm" data-bs-toggle="modal" data-bs-target="#tripExportModal">
                                        <i class="fa fa-file-excel"></i>
                                        Export data
                                    </button>
                                @endif
                                <button class="btn btn-primary btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#tripModal">
                                    <i class="fa fa-plus"></i>
                                    Tambah data
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (count($trips) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th width=5%>
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">Tanggal</th>
                                        <th class="text-xxs-bold">Kota</th>
                                        <th class="text-xxs-bold">Nama</th>
                                        <th class="text-xxs-bold">Keterangan</th>
                                        <th class="text-xxs-bold">Uraian</th>
                                        <th class="text-xxs-bold">Nopol <br> / Kode Unit</th>
                                        <th class="text-xxs-bold">Merk</th>
                                        <th class="text-xxs-bold">Jumlah</th>
                                        <!-- <th class="text-xxs-bold">Satuan</th> -->
                                        <th class="text-xxs-bold">KM Awal</th>
                                        <th class="text-xxs-bold">KM Pengisian <br> (Sebelumnya)</th>
                                        <th class="text-xxs-bold">KM Pengisian <br> (Saat ini)</th>
                                        <th class="text-xxs-bold">KM Akhir</th>
                                        <th class="text-xxs-bold">KM/Liter</th>
                                        <!-- <th class="text-xxs-bold">Harga</th> -->
                                        <th class="text-xxs-bold">Total</th>
                                        <th class="text-xxs-bold">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trips as $t)
                                    <tr data-id="{{ $t->id_trip }}" 
                                        data-tanggal="{{ $t->tanggal }}" 
                                        data-kota="{{ $t->kota }}"
                                        data-nama="{{ $t->nama }}"
                                        data-ket="{{ $t->ket }}"
                                        data-uraian="{{ $t->uraian }}"
                                        data-kendaraan="{{ $t->id_kendaraan }}"
                                        data-merk="{{ $merkKendaraan[$t->id_kendaraan] ?? '-' }}"
                                        data-qty="{{ $t->qty }}"
                                        data-unit="{{ $t->unit }}"
                                        data-km_awal="{{ $t->km_awal }}"
                                        data-km_isi_seb="{{ $t->km_isi_seb }}"
                                        data-km_isi_sek="{{ $t->km_isi_sek }}"
                                        data-km_akhir="{{ $t->km_akhir }}"
                                        data-km_ltr="{{ $t->km_ltr }}"
                                        data-harga="{{ 'Rp ' . number_format($t->harga ?? 0, 0, ',', '.') }}"
                                        data-total="{{ 'Rp ' . number_format($t->total ?? 0, 0, ',', '.') }}"
                                        data-file="{{ $t->file }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $t->tanggal)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $t->kota ?? '-' }}</td>
                                        <td>{{ $t->nama ?? '-' }}</td>
                                        <td>{{ $t->ket ?? '-' }}</td>
                                        <td class="uraian">{{ $t->uraian ?? '-' }}</td>
                                        @if ($t->file)
                                            <td>
                                                <a href="{{ asset('storage/' . $t->file) }}" target="_blank">{{ $nopolKendaraan[$t->id_kendaraan] ?? '-' }}</a>
                                            </td>
                                        @else
                                            <td>{{ $nopolKendaraan[$t->id_kendaraan] ?? '-' }}</td>
                                        @endif
                                        <td>{{ $merkKendaraan[$t->id_kendaraan] ?? '-' }}</td>
                                        <td>{{ $t->qty ? $t->qty : '-' }}</td>
                                        <!-- <td>{{ $t->unit ?? '-' }}</td> -->
                                        <td>{{ $t->km_awal ? $t->km_awal : '-' }}</td>
                                        <td>{{ $t->km_isi_seb ? $t->km_isi_seb : '-' }}</td>
                                        <td>{{ $t->km_isi_sek ? $t->km_isi_sek : '-' }}</td>
                                        <td>{{ $t->km_akhir ? $t->km_akhir : '-' }}</td>
                                        <td>{{ $t->km_ltr ? $t->km_ltr : '-' }}</td>
                                        <!-- <td>{{ 'Rp ' . number_format($t->harga ?? 0, 0, ',', '.') }}</td> -->
                                        <td>{{ 'Rp ' . number_format($t->total ?? 0, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($t->status) {
                                                    'pending' => 'status-pending',
                                                    'processing' => 'status-process',
                                                    default => 'status-paid',
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ ucfirst($t->status) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="total-wrapper">
                            <strong>Total Pengeluaran: </strong> <span id="total-sum">Rp 0</span>
                        </div>
                    </div>
                    @else
                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-0">
                            <span class="text-xs mb-3"><i>Tidak ada data yang bisa ditampilkan..</i></span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    // Currency
    function formatCurrency(num) {
        num = num.toString().replace(/[^\d-]/g, '');

        num = num.replace(/-+/g, (match, offset) => offset > 0 ? "" : "-");

        let isNegative = false;
        if (num.startsWith("-")) {
            isNegative = true;
            num = num.slice(1);
        }

        let formattedNum = "Rp " + Math.abs(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        if (isNegative) {
            formattedNum = "-" + formattedNum;
        }

        return formattedNum;
    }

    // Sum total
    function calculateTotal() {
        let totalSum = 0;
        document.querySelectorAll('#basic-datatables tbody tr').forEach(row => {
            if (row.querySelector('td:nth-child(16)')) {
                const totalText = row.querySelector('td:nth-child(16)').innerText;
                const totalValue = parseInt(totalText.replace(/[^0-9,-]+/g, ""));
                totalSum += totalValue;
            }
        });
        if (document.getElementById('total-sum')) {
            document.getElementById('total-sum').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalSum);
        }
    }

    function toggleCustomFields() {
        const isCustomChecked = document.getElementById('custom').checked;
        const customFields = document.getElementById('customFields');

        if (isCustomChecked) {
            setFieldsRequired(customFields, true);
            customFields.classList.remove('d-none');

        } else {
            setFieldsRequired(customFields, false);
            customFields.classList.add('d-none');
        }
    }

    function setFieldsRequired(fieldsContainer, isRequired) {
        const selects = fieldsContainer.querySelectorAll('select');
        selects.forEach(select => {
            if (isRequired) {
                select.setAttribute('required', 'required');
            } else {
                select.removeAttribute('required');
            }
        });
    }

    function validateForm() {
        const allData = document.getElementById('all_data');
        const custom = document.getElementById('custom');
        const radioError = document.getElementById('radioError');
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const dateError = document.getElementById('dateError');

        if (!allData.checked && !custom.checked) {
            radioError.classList.remove('d-none');
            return false;
        }

        if (custom.checked) {
            if (!startDate || !endDate) {
                dateError.classList.remove('d-none');
                return false;
            }
        }

        dateError.classList.add('d-none');
        radioError.classList.add('d-none');
        return true;
    }

    function updateMerk() {
        var kendaraanSelect = document.getElementById('kendaraan');
        var selectedOption = kendaraanSelect.options[kendaraanSelect.selectedIndex];
        var merk = selectedOption.getAttribute('data-merk');
        
        var merkInput = document.getElementById('merk');
        merkInput.value = merk ? merk.toUpperCase() : '';
        
        // Cari data kendaraan pada tabel berdasarkan ID kendaraan yang dipilih
        const kendaraanId = selectedOption.value;
        var table = $('#basic-datatables').DataTable();
        // const rows = document.querySelectorAll('#basic-datatables tbody tr');
        let lastData = null;
        let lastDataIsi = null;

        if (table && table.rows()) {
            table.rows().every(function() {
                const row = $(this.node());
                const rowKendaraanId = row.attr('data-kendaraan');
                const rowKMIsiSek = row.attr('data-km_isi_sek');

                if (rowKendaraanId === kendaraanId) {
                    lastData = row;
                }

                if (rowKendaraanId === kendaraanId && rowKMIsiSek && rowKMIsiSek !== '0') {
                    lastDataIsi = row;
                }
            });

            // Jika data ditemukan, isi otomatis kolom-kolom lain
            if (lastData) {
                document.getElementById('km_awal').value = lastData.attr('data-km_akhir');

            } else {
                document.getElementById('km_awal').value = null;
            }

            if (lastDataIsi) {
                document.getElementById('km_isi_seb').value = lastDataIsi.attr('data-km_isi_sek');

            } else {
                document.getElementById('km_isi_seb').value = null;
            }
        }
    }

    function updateEditMerk() {
        var kendaraanEditSelect = document.getElementById('edit-kendaraan');
        var selectedEditOption = kendaraanEditSelect.options[kendaraanEditSelect.selectedIndex];
        var merk = selectedEditOption.getAttribute('data-merk');
        
        var merkEditInput = document.getElementById('edit-merk');
        merkEditInput.value = merk ? merk.toUpperCase() : '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tableData = $('#basic-datatables').DataTable();
        tableData.on('draw.dt', function() {
            calculateTotal();
        });

        calculateTotal();

        var batasKarakter = 35;
        var cells = document.querySelectorAll('td.uraian');

        cells.forEach(function(cell) {
            var text = cell.textContent;
            var words = text.split(' ');
            var newText = '';
            var line = '';

            words.forEach(function(word) {
                if ((line + word).length > batasKarakter) {
                    newText += line.trim() + '<br>';
                    line = word + ' ';
                } else {
                    line += word + ' ';
                }
            });

            newText += line.trim();
            cell.innerHTML = newText;
        });

        let qtyElements = document.querySelectorAll("#qty, #edit-qty");
        let kmAwalElements = document.querySelectorAll("#km_awal, #edit-km_awal");
        let kmIsiSebElements = document.querySelectorAll("#km_isi_seb, #edit-km_isi_seb");
        let kmIsiSekElements = document.querySelectorAll("#km_isi_sek, #edit-km_isi_sek");
        let kmAkhirElements = document.querySelectorAll("#km_akhir, #edit-km_akhir");
        let kmLiterElements = document.querySelectorAll("#km_ltr, #edit-km_ltr");
        let hargaTrip = document.querySelectorAll("#harga, #edit-harga");
        let totalElements = document.querySelectorAll("#total, #edit-total");
    
        // Function to calculate km/liter
        function calculateKmLiter() {
            qtyElements.forEach((qty, index) => {
                let kmAwal = kmAwalElements[index];
                let kmIsiSeb = kmIsiSebElements[index];
                let kmIsiSek = kmIsiSekElements[index];
                let kmAkhir = kmAkhirElements[index];
                let kmLiter = kmLiterElements[index];
                let harga = hargaTrip[index];
                let qtyValue = parseFloat(qty.value.replace(',', '.'));
    
                if (qtyValue && kmIsiSek.value && kmIsiSeb.value) {
                    let valueKmLiter = (kmIsiSek.value - kmIsiSeb.value) / qtyValue;
                    kmLiter.value = parseFloat(valueKmLiter.toFixed(3));

                } else {
                    kmLiter.value = null;
                }

                if (harga.value && qtyValue) {
                    let totalValue = (harga.value.replace(/[^0-9]/g, "")) * qtyValue;
                    totalElements[index].value = formatCurrency(totalValue);
                }
            });
        }
    
        [...qtyElements, ...kmIsiSebElements, ...kmIsiSekElements, ...kmAkhirElements].forEach(element => {
            element.addEventListener("change", calculateKmLiter);
        });

        // Harga
        hargaTrip.forEach(function(hargaSp, index) {
            hargaSp.addEventListener("input", function() {
                this.value = formatCurrency(this.value);

                let qtyValue = parseFloat(qtyElements[index].value.replace(',', '.'));
                if (qtyValue) {
                    let totalValue = (this.value.replace(/[^0-9]/g, "")) * qtyValue;
                    totalElements[index].value = formatCurrency(totalValue);
                }
            });
        });

        // Total harga
        totalElements.forEach(function(totHarga, index) {
            totHarga.addEventListener("input", function() {
                this.value = formatCurrency(this.value);
            });
        });

        // Datepicker
        var reportrange = document.getElementById('reportrange');
        var span = reportrange.querySelector('span');
        var startInput = document.getElementById('start_date');
        var endInput = document.getElementById('end_date');
        var reportrangeDisplay = document.getElementById('reportrange_display');

        var start = moment().subtract(29, 'days');
        var end = null;

        function cb(start, end) {
            if (start.isValid() && end.isValid()) {
                var rangeText = span.innerHTML = start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY');
                startInput.value = start.format('YYYY-MM-DD');
                endInput.value = end.format('YYYY-MM-DD');
            }
        }

        function applyDateRangePicker() {
            new daterangepicker(reportrange, {
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), 
                        moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
                
            }, cb);
        }

        applyDateRangePicker();

        // Checkbox
        var page = new URLSearchParams(window.location.search).get('page');
        var table = $('#basic-datatables').DataTable();
        var selectAllCheckbox = document.getElementById('selectAllCheckbox');
        var allSelectRowInput = document.getElementById('allSelectRow');
        var deleteAllSelectRowInput = document.getElementById('deleteAllSelectRow');
        var pendingAllSelectRowInput = document.getElementById('pendingAllSelectRow');
        var processAllSelectRowInput = document.getElementById('processAllSelectRow');
        var paidAllSelectRowInput = document.getElementById('paidAllSelectRow');
        var editButton = document.getElementById('editButton');
        var deleteButton = document.getElementById('deleteButton');
        var pendingButton = document.getElementById('statusPendingButton');
        var processButton = document.getElementById('statusProcessButton');
        var paidButton = document.getElementById('statusPaidButton');

        if (table && selectAllCheckbox) {
            if (page) {
                table.page(parseInt(page) - 1).draw(false);
            }

            // Event listener untuk checkbox "Select All"
            selectAllCheckbox.addEventListener('change', function() {
                table.rows({ page: 'current' }).nodes().to$().find('.select-checkbox').each(function() {
                    this.checked = selectAllCheckbox.checked;
                    var row = this.closest('tr');
                    row.classList.toggle('selected', this.checked);
                });

                // Update button visibility
                updateButtonVisibility();

                // Ambil dan simpan ID semua baris yang terpilih ke dalam input hidden
                updateAllSelectRow();
            });

            // Event listener untuk checkbox di setiap baris
            table.on('change', '.select-checkbox', function() {
                var row = $(this).closest('tr');
                row.toggleClass('selected', this.checked);

                // Update button visibility
                updateButtonVisibility();

                // Atur status checkbox "Select All"
                var allChecked = table.rows({ page: 'current' }).nodes().to$().find('.select-checkbox').toArray().every(function(checkbox) {
                    return checkbox.checked;
                });

                selectAllCheckbox.checked = allChecked;

                // Ambil dan simpan ID semua baris yang terpilih ke dalam input hidden
                updateAllSelectRow();
            });

            // Fungsi untuk mengambil dan menyimpan ID semua baris yang terpilih
            function updateAllSelectRow() {
                var selectedIds = table.rows({ search: 'applied' }).nodes().to$().find('.select-checkbox:checked').map(function() {
                    return $(this).closest('tr').data('id');
                }).get();

                var idsString = selectedIds.join(',');

                // Update each hidden input with the selected IDs
                allSelectRowInput.value = idsString;
                deleteAllSelectRowInput.value = idsString;

                if (pendingAllSelectRowInput) {
                    pendingAllSelectRowInput.value = idsString;
                    processAllSelectRowInput.value = idsString;
                    paidAllSelectRowInput.value = idsString;
                }
            }

            // Fungsi untuk mengatur visibilitas tombol
            function updateButtonVisibility() {
                var selectedCheckboxes = table.rows({ search: 'applied' }).nodes().to$().find('.select-checkbox:checked').length;

                if (selectedCheckboxes === 1) {
                    editButton.style.display = 'inline-block';
                    deleteButton.style.display = 'inline-block';
                    if (pendingButton) {
                        pendingButton.style.display = 'inline-block';
                        processButton.style.display = 'inline-block';
                        paidButton.style.display = 'inline-block';
                    }
                    deleteButton.classList.remove('ms-5');
                    deleteButton.classList.add('ms-3');
                } else if (selectedCheckboxes > 1) {
                    editButton.style.display = 'none';
                    deleteButton.style.display = 'inline-block';
                    if (pendingButton) {
                        pendingButton.style.display = 'inline-block';
                        processButton.style.display = 'inline-block';
                        paidButton.style.display = 'inline-block';
                    }
                    deleteButton.classList.remove('ms-3');
                    deleteButton.classList.add('ms-5');
                } else {
                    editButton.style.display = 'none';
                    deleteButton.style.display = 'none';
                    if (pendingButton) {
                        pendingButton.style.display = 'none';
                        processButton.style.display = 'none';
                        paidButton.style.display = 'none';
                    }
                }
            }

            // Event listener for the delete button
            deleteButton.addEventListener('click', function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.submit();
                    }
                });
            });
            
            // Event listener untuk tombol update status
            if (pendingButton) {
                pendingButton.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You'll update the status to pending!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, update it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            statusPendingForm.submit();
                        }
                    });
                });
            }

            if (processButton) {
                processButton.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You'll update the status to processing!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, update it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            statusProcessForm.submit();
                        }
                    });
                });
            }

            if (paidButton) {
                paidButton.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You'll update the status to paid!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, update it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            statusPaidForm.submit();
                        }
                    });
                });
            }

            editButton.addEventListener('click', function () {
                // Tambahkan nomor halaman ke form edit
                var currentPage = table.page.info().page + 1;
                document.querySelector('input[name="page"]').value = currentPage;
                
                var selectedId = allSelectRowInput.value.split(',')[0];
                if (selectedId) {
                    var row = $('tr[data-id="' + selectedId + '"]');
                    
                    $('#edit-id').val(selectedId);
                    $('#edit-tanggal').val(row.data('tanggal'));
                    $('#edit-kota').val(row.data('kota'));
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-ket').val(row.data('ket'));
                    $('#edit-uraian').val(row.data('uraian'));
                    $('#edit-kendaraan').val(row.data('kendaraan'));
                    $('#edit-merk').val(row.data('merk'));
                    $('#edit-qty').val(row.data('qty'));
                    $('#edit-unit').val(row.data('unit'));
                    $('#edit-km_awal').val(row.data('km_awal'));
                    $('#edit-km_isi_seb').val(row.data('km_isi_seb'));
                    $('#edit-km_isi_sek').val(row.data('km_isi_sek'));
                    $('#edit-km_akhir').val(row.data('km_akhir'));
                    $('#edit-km_ltr').val(row.data('km_ltr'));
                    $('#edit-harga').val(row.data('harga'));
                    $('#edit-total').val(row.data('total'));
                    
                    // Menampilkan file yang diunggah
                    var file = row.data('file');
                    if (file) {
                        var fileUrl = "{{ asset('storage/') }}" + "/" + file;
                        $('#current-file').html('<a href="' + fileUrl + '" target="_blank">' + file + '</a>');
                    } else {
                        $('#current-file').html('<span>Tidak ada file yang diunggah</span>');
                    }
                }
            });
        }

    });
</script>
@endsection