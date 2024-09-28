@extends('layouts.base')
<!-- @section('title', 'Bubut') -->
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
            <h3 class="fw-bold mb-3">Data Pengeluaran Bubut</h3>
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
                    <a href="#">Bubut</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="bubutModal" tabindex="-1" role="dialog" aria-labelledby="bubutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="bubutModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Bubut Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('bubut-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <div class="form-group">
                                <label for="lokasi">Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" id="lokasi" placeholder="Masukkan lokasi.." 
                                oninput="this.value = this.value.toUpperCase()" required />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kendaraan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
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
                                <div class="col-6">
                                    <label for="merk">Merk</label>
                                    <input type="text" class="form-control" name="merk" id="merk" placeholder="Masukkan merk.." 
                                    oninput="this.value = this.value.toUpperCase()" style="background-color: #fff !important;" readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Pemesanan</span>
                            </div>

                            <div class="form-group">
                                <label for="pemesan">Pemesan / Dipesan oleh</label>
                                <input type="text" class="form-control" name="pemesan" id="pemesan" placeholder="Masukkan nama pemesan.." 
                                oninput="this.value = this.value.toUpperCase()" required />
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="tgl_order">Tanggal Order</label>
                                    <input type="date" class="form-control" name="tgl_order" id="tgl_order" required />
                                </div>
                                <div class="col-4">
                                    <label for="tgl_invoice">Tanggal Invoice</label>
                                    <input type="date" class="form-control" name="tgl_invoice" id="tgl_invoice" required />
                                </div>
                                <div class="col-4">
                                    <label for="no_inventaris">No. Inventaris</label>
                                    <input type="text" class="form-control" name="no_inventaris" id="no_inventaris" placeholder="Masukkan no. inventaris.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="nama">Nama (Barang)</label>
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                                <div class="col-4">
                                    <label for="kategori">Kategori</label>
                                    <select class="form-select form-control" id="kategori" name="kategori" required>
                                        <option value="">...</option>
                                        <option value="Aset">Aset</option>
                                        <option value="Stok">Stok</option>
                                        <option value="Langsung Pakai">Langsung Pakai</option>
                                        <option value="Jasa">Jasa</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="masa_pakai">Masa Pakai</label>
                                    <div class="d-flex">
                                        <input type="number" class="form-control" name="masa" id="masa" placeholder="..." required />
                                        <select class="form-control ms-2" name="waktu" id="waktu" required>
                                            <option value="" disabled selected>...</option>
                                            <option value="HARI">HARI</option>
                                            <option value="MINGGU">MINGGU</option>
                                            <option value="BULAN">BULAN</option>
                                            <option value="TAHUN">TAHUN</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="dipakai_untuk">Dipakai untuk</label>
                                <input type="text" class="form-control" name="dipakai_untuk" id="dipakai_untuk" placeholder="Dipakai untuk.." required />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="jml">Jumlah</label>
                                    <input type="number" class="form-control" name="jml" min="1" id="jml" placeholder="Jumlah.." required />
                                </div>
                                <div class="col-3">
                                    @if (count($satuan) > 0)
                                        <label for="satuan">Satuan</label>
                                        <select class="form-select form-control" name="unit" id="unit" required>
                                            <option value="">...</option>
                                            @foreach ($satuan as $s)
                                                <option value="{{ $s->id_satuan }}">{{ $s->nama }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <label>Satuan</label>
                                        <select class="form-control" disabled>
                                            <option value="">Tidak ada data</option>
                                        </select>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="harga" placeholder="Masukkan harga.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="total" placeholder="Nilai total harga.." required />
                            </div>

                            <div class="form-group">
                                @if (count($toko) > 0)
                                    <label for="toko">Toko</label>
                                    <select class="form-select form-control" name="toko" id="toko" required>
                                        <option value="">...</option>
                                        @foreach ($toko as $s)
                                            <option value="{{ $s->id_toko }}">{{ $s->nama }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <label>Toko</label>
                                    <select class="form-control" disabled>
                                        <option value="">Tidak ada data</option>
                                    </select>
                                @endif
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

        <!-- Modal Edit Data -->
        <div class="modal fade" id="bubutEditModal" tabindex="-1" role="dialog" aria-labelledby="bubutEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="bubutEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Bubut </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('bubut-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_tagihan_amb">

                            <div class="form-group">
                                <label for="lokasi">Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" id="edit-lokasi" placeholder="Masukkan lokasi.." 
                                oninput="this.value = this.value.toUpperCase()" required />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kendaraan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
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
                                <div class="col-6">
                                    <label for="merk">Merk</label>
                                    <input type="text" class="form-control" name="merk" id="edit-merk" placeholder="Masukkan merk.." 
                                    oninput="this.value = this.value.toUpperCase()" style="background-color: #fff !important;" readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Pemesanan</span>
                            </div>

                            <div class="form-group">
                                <label for="pemesan">Pemesan / Dipesan oleh</label>
                                <input type="text" class="form-control" name="pemesan" id="edit-pemesan" placeholder="Masukkan nama pemesan.." 
                                oninput="this.value = this.value.toUpperCase()" required />
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="tgl_order">Tanggal Order</label>
                                    <input type="date" class="form-control" name="tgl_order" id="edit-tgl_order" required />
                                </div>
                                <div class="col-4">
                                    <label for="tgl_invoice">Tanggal Invoice</label>
                                    <input type="date" class="form-control" name="tgl_invoice" id="edit-tgl_invoice" required />
                                </div>
                                <div class="col-4">
                                    <label for="no_inventaris">No. Inventaris</label>
                                    <input type="text" class="form-control" name="no_inventaris" id="edit-no_inventaris" placeholder="Masukkan no. inventaris.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="nama">Nama (Barang)</label>
                                    <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                                <div class="col-4">
                                    <label for="kategori">Kategori</label>
                                    <select class="form-select form-control" id="edit-kategori" name="kategori" required>
                                        <option value="">...</option>
                                        <option value="Aset">Aset</option>
                                        <option value="Stok">Stok</option>
                                        <option value="Langsung Pakai">Langsung Pakai</option>
                                        <option value="Jasa">Jasa</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="masa_pakai">Masa Pakai</label>
                                    <div class="d-flex">
                                        <input type="number" class="form-control" name="masa" id="edit-masa" placeholder="..." required />
                                        <select class="form-control ms-2" name="waktu" id="edit-waktu" required>
                                            <option value="" disabled selected>...</option>
                                            <option value="HARI">HARI</option>
                                            <option value="MINGGU">MINGGU</option>
                                            <option value="BULAN">BULAN</option>
                                            <option value="TAHUN">TAHUN</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="dipakai_untuk">Dipakai untuk</label>
                                <input type="text" class="form-control" name="dipakai_untuk" id="edit-dipakai_untuk" placeholder="Dipakai untuk.." required />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="jml">Jumlah</label>
                                    <input type="number" class="form-control" name="jml" min="0" id="edit-jml" placeholder="Jumlah.." required />
                                </div>
                                <div class="col-3">
                                    @if (count($satuan) > 0)
                                        <label for="satuan">Satuan</label>
                                        <select class="form-select form-control" name="unit" id="edit-unit" required>
                                            <option value="">...</option>
                                            @foreach ($satuan as $s)
                                                <option value="{{ $s->id_satuan }}">{{ $s->nama }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <label>Satuan</label>
                                        <select class="form-control" disabled>
                                            <option value="">Tidak ada data</option>
                                        </select>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="edit-harga" placeholder="Masukkan harga.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="edit-total" placeholder="Nilai total harga.." required />
                            </div>

                            <div class="form-group">
                                @if (count($toko) > 0)
                                    <label for="toko">Toko</label>
                                    <select class="form-select form-control" name="toko" id="edit-toko" required>
                                        <option value="">...</option>
                                        @foreach ($toko as $s)
                                            <option value="{{ $s->id_toko }}">{{ $s->nama }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <label>Toko</label>
                                    <select class="form-control" disabled>
                                        <option value="">Tidak ada data</option>
                                    </select>
                                @endif
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
        <div class="modal fade" id="bubutExportModal" tabindex="-1" role="dialog" aria-labelledby="bubutExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="bubutExportModal">
                            <span class="fw-light"> Export Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Bubut </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('bubut-export') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
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
                            <div class="card-title">List Pengeluaran Bubut</div>
                            <input type="hidden" id="allSelectRow" name="ids" value="">
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#bubutEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('bubut-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="deleteAllSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            @if (Auth::user()->level == 0)
                            <form id="statusPendingForm" method="POST" action="{{ url('bubut-status_pending') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="pendingAllSelectRow" name="ids" value="">
                                <button id="statusPendingButton" type="button" class="btn btn-warning btn-round ms-5 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Pending
                                </button>
                            </form>
                            <form id="statusProcessForm" method="POST" action="{{ url('bubut-status_process') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="processAllSelectRow" name="ids" value="">
                                <button id="statusProcessButton" type="button" class="btn btn-black btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Processing
                                </button>
                            </form>
                            <form id="statusPaidForm" method="POST" action="{{ url('bubut-status_paid') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="paidAllSelectRow" name="ids" value="">
                                <button id="statusPaidButton" type="button" class="btn btn-success btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Paid
                                </button>
                            </form>
                            @endif
                            <div class="ms-auto d-flex align-items-center">
                                @if (count($bubut) > 0)
                                    <button class="btn btn-success btn-round ms-2 btn-sm" data-bs-toggle="modal" data-bs-target="#bubutExportModal">
                                        <i class="fa fa-file-excel"></i>
                                        Export data
                                    </button>
                                @endif
                                <button class="btn btn-primary btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#bubutModal">
                                    <i class="fa fa-plus"></i>
                                    Tambah data
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (count($bubut) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th width=5%>
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">Lokasi</th>
                                        <th class="text-xxs-bold">Nopol <br> / Kode Unit</th>
                                        <!-- <th class="text-xxs-bold">Nopol</th>
                                        <th class="text-xxs-bold">Kode Unit</th>
                                        <th class="text-xxs-bold">Merk</th> -->
                                        <th class="text-xxs-bold">Pemesan</th>
                                        <th class="text-xxs-bold">Tgl. Order</th>
                                        <th class="text-xxs-bold">Tgl. Invoice</th>
                                        <th class="text-xxs-bold">No. Inventaris</th>
                                        <th class="text-xxs-bold">Nama (Barang)</th>
                                        <th class="text-xxs-bold">Kategori</th>
                                        <!-- <th class="text-xxs-bold">Keperluan</th> -->
                                        <th class="text-xxs-bold">Masa Pakai</th>
                                        <!-- <th class="text-xxs-bold">Qty</th> -->
                                        <!-- <th class="text-xxs-bold">Unit</th> -->
                                        <th class="text-xxs-bold">Harga</th>
                                        <th class="text-xxs-bold">Total</th>
                                        <th class="text-xxs-bold">Toko</th>
                                        <th class="text-xxs-bold">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bubut as $b)
                                    <tr data-id="{{ $b->id_tagihan_amb }}" 
                                        data-lokasi="{{ $b->lokasi }}" 
                                        data-kendaraan="{{ $b->id_kendaraan }}"
                                        data-merk="{{ $merkKendaraan[$b->id_kendaraan] ?? '-' }}"
                                        data-pemesan="{{ $b->pemesan }}"
                                        data-tgl_order="{{ $b->tgl_order }}"
                                        data-tgl_invoice="{{ $b->tgl_invoice }}"
                                        data-no_inventaris="{{ $b->no_inventaris }}"
                                        data-nama="{{ $b->nama }}"
                                        data-kategori="{{ $b->kategori }}"
                                        data-masa_pakai="{{ $b->masa_pakai }}"
                                        data-dipakai_untuk="{{ $b->dipakai_untuk }}"
                                        data-jml="{{ $b->jml }}"
                                        data-unit="{{ $b->id_satuan }}"
                                        data-harga="{{ 'Rp ' . number_format($b->harga ?? 0, 0, ',', '.') }}"
                                        data-total="{{ 'Rp ' . number_format($b->total ?? 0, 0, ',', '.') }}"
                                        data-toko="{{ $b->id_toko }}"
                                        data-file="{{ $b->file }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $b->lokasi ?? '-' }}</td>
                                        <td>{{ $nopolKendaraan[$b->id_kendaraan] ?? '-' }}</td>
                                        <!-- <td>{{ $b->nopol ?? '-' }}</td>
                                        <td>{{ $b->kode_unit ?? '-' }}</td>
                                        <td>{{ $b->merk ?? '-' }}</td> -->
                                        @if ($b->file)
                                            <td>
                                                <a href="{{ asset('storage/' . $b->file) }}" target="_blank">{{ $b->pemesan ?? '-' }}</a>
                                            </td>
                                        @else
                                            <td>{{ $b->pemesan ?? '-' }}</td>
                                        @endif
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $b->tgl_order)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $b->tgl_invoice)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $b->no_inventaris ?? '-' }}</td>
                                        <td>{{ $b->nama ?? '-' }}</td>
                                        <td>{{ $b->kategori ?? '-' }}</td>
                                        <!-- <td>{{ $b->dipakai_untuk ?? '-' }}</td> -->
                                        <td>{{ $b->masa_pakai ?? '-' }}</td>
                                        <!-- <td>{{ $b->jml ?? '-' }}</td> -->
                                        <!-- <td>{{ $b->id_satuan ?? '-' }}</td> -->
                                        <td>{{ 'Rp ' . number_format($b->harga ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($b->total ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $namaToko[$b->id_toko] ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($b->status) {
                                                    'pending' => 'status-pending',
                                                    'processing' => 'status-process',
                                                    default => 'status-paid',
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ ucfirst($b->status) }}</span>
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
            if (row.querySelector('td:nth-child(13)')) {
                const totalText = row.querySelector('td:nth-child(13)').innerText;
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

        // Harga
        let hargaElements = document.querySelectorAll("#harga");
        let jmlElements = document.querySelectorAll("#jml");
        let totalElements = document.querySelectorAll("#total");

        jmlElements.forEach(function(jml, index) {
            jml.addEventListener("input", function() {
                let harga = hargaElements[index];
                let total = totalElements[index];

                if (harga) {
                    let hargaValue = parseInt(harga.value.replace(/[^0-9]/g, ""), 10) || 0;
                    let jmlValue = parseFloat(jml.value) || 0;
                    let totalValue = hargaValue * jmlValue;
                    total.value = formatCurrency(totalValue);
                }
            });
        });

        hargaElements.forEach(function(harga, index) {
            harga.addEventListener("input", function() {
                this.value = formatCurrency(this.value);

                let jml = jmlElements[index];
                let total = totalElements[index];

                if (jml) {
                    let hargaValue = parseInt(this.value.replace(/[^0-9]/g, ""), 10) || 0;
                    let jmlValue = parseFloat(jml.value) || 0;
                    let totalValue = hargaValue * jmlValue;
                    total.value = formatCurrency(totalValue);
                }
            });
        });

        totalElements.forEach(function(totHarga, index) {
            totHarga.addEventListener("input", function() {
                this.value = formatCurrency(this.value);
            });
        });

        // Harga Edit
        let hargaEditElements = document.querySelectorAll("#edit-harga");
        let jmlEditElements = document.querySelectorAll("#edit-jml");
        let totalEditElements = document.querySelectorAll("#edit-total");

        jmlEditElements.forEach(function(jml, index) {
            jml.addEventListener("input", function() {
                let harga = hargaEditElements[index];
                let total = totalEditElements[index];

                if (harga) {
                    let hargaValue = parseInt(harga.value.replace(/[^0-9]/g, ""), 10) || 0;
                    let jmlValue = parseFloat(jml.value) || 0;
                    let totalValue = hargaValue * jmlValue;
                    total.value = formatCurrency(totalValue);
                }
            });
        });

        hargaEditElements.forEach(function(harga, index) {
            harga.addEventListener("input", function() {
                this.value = formatCurrency(this.value);

                let jml = jmlEditElements[index];
                let total = totalEditElements[index];

                if (jml) {
                    let hargaValue = parseInt(this.value.replace(/[^0-9]/g, ""), 10) || 0;
                    let jmlValue = parseFloat(jml.value) || 0;
                    let totalValue = hargaValue * jmlValue;
                    total.value = formatCurrency(totalValue);
                }
            });
        });

        totalEditElements.forEach(function(totHarga, index) {
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
                var selectedId = allSelectRowInput.value.split(',')[0];
                if (selectedId) {
                    var row = $('tr[data-id="' + selectedId + '"]');
                    var masaPakai = row.data('masa_pakai');

                    // Memisahkan nilai menjadi jumlah dan unit
                    var parts = masaPakai.split(' ');
                    var jumlah = parts[0];
                    var waktu = parts[1];
                    
                    $('#edit-id').val(selectedId);
                    $('#edit-lokasi').val(row.data('lokasi'));
                    $('#edit-kendaraan').val(row.data('kendaraan'));
                    $('#edit-merk').val(row.data('merk'));
                    $('#edit-pemesan').val(row.data('pemesan'));
                    $('#edit-tgl_order').val(row.data('tgl_order'));
                    $('#edit-tgl_invoice').val(row.data('tgl_invoice'));
                    $('#edit-no_inventaris').val(row.data('no_inventaris'));
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-kategori').val(row.data('kategori'));
                    $('#edit-masa').val(jumlah);
                    $('#edit-waktu').val(waktu);
                    $('#edit-dipakai_untuk').val(row.data('dipakai_untuk'));
                    $('#edit-jml').val(row.data('jml'));
                    $('#edit-unit').val(row.data('unit'));
                    $('#edit-harga').val(row.data('harga'));
                    $('#edit-total').val(row.data('total'));
                    $('#edit-toko').val(row.data('toko'));

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