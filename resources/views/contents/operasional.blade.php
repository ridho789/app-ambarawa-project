@extends('layouts.base')
<!-- @section('title', 'Operasional') -->
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
            <h3 class="fw-bold mb-3">Data Pengeluaran Operasional</h3>
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
                    <a href="#">Operasional</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="operasionalModal" tabindex="-1" role="dialog" aria-labelledby="operasionalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="operasionalModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Operasional Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('operasional-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Pemesanan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" required />
                                </div>
                                <div class="col-8">
                                    <label for="uraian">Uraian</label>
                                    <input type="text" class="form-control" name="uraian" id="uraian" placeholder="Masukkan uraian.." required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="nama">Nama / Pemesan</label>
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                                <div class="col-8">
                                    <label for="deskripsi">Deskripsi</label>
                                    <input type="text" class="form-control" name="deskripsi" id="deskripsi" placeholder="Masukkan deskripsi.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="lokasi">Lokasi <span class="text-info">(Opsional)</span></label>
                                <input type="text" class="form-control" name="lokasi" id="lokasi" placeholder="Masukkan lokasi.." 
                                oninput="this.value = this.value.toUpperCase()" />
                            </div>

                            <div class="form-group">
                                <label>Metode Pembelian</label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check mr-3">
                                        <input class="form-check-input" type="radio" name="metode_pembelian" id="is_online" value="online" onclick="toggleFields()" />
                                        <label class="form-check-label" for="is_online">
                                            Online
                                        </label>
                                    </div>
                                    <div class="form-check mr-3">
                                        <input class="form-check-input" type="radio" name="metode_pembelian" id="is_offline" value="offline" onclick="toggleFields()" />
                                        <label class="form-check-label" for="is_offline">
                                            Offline
                                        </label>
                                    </div>
                                    <div>
                                        <span type="button" class="badge badge-black" style="margin-bottom: 8px;" onclick="resetFields()">Reset</span>
                                    </div>
                                </div>
                            </div>

                            <div id="barang" class="d-none">
                                <div class="form-group">
                                    <span class="h5 fw-mediumbold">Informasi Barang</span>
                                </div>

                                <div id="barangFields" class="form-group row">
                                    <!-- Template for Barang Fields -->
                                    <div class="barang-item">
                                        <div class="form-group row">
                                            <div class="col-4">
                                                <label for="nama_barang">Barang ke-1</label>
                                                <input type="text" class="form-control" name="nama_barang[]" 
                                                oninput="this.value = this.value.toUpperCase()" placeholder="Masukkan nama barang.." />
                                            </div>
                                            <div class="col-2">
                                                <label for="qty">Jumlah</label>
                                                <input type="text" class="form-control" name="qty[]" id="qty" placeholder="Jumlah.." />
                                            </div>
                                            <div class="col-2">
                                                @if (count($satuan) > 0)
                                                    <label for="satuan">Satuan</label>
                                                    <select class="form-select form-control" name="unit[]" id="unit">
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
                                            <div class="col-4">
                                                <label for="harga">Harga</label>
                                                <input type="text" class="form-control" name="harga[]" id="harga" placeholder="Masukkan harga.." />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="addBarang()">Tambah Barang</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div id="onlineFields" class="d-none">
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="diskon">Diskon</label>
                                        <input type="text" class="form-control" name="diskon" id="diskon" placeholder="Diskon.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="ongkir">Ongkir</label>
                                        <input type="text" class="form-control" name="ongkir" id="ongkir" placeholder="Ongkir.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="asuransi">Asuransi</label>
                                        <input type="text" class="form-control" name="asuransi" id="asuransi" placeholder="Asuransi.." />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="b_proteksi">Biaya Proteksi</label>
                                        <input type="text" class="form-control" name="b_proteksi" id="b_proteksi" placeholder="Biaya proteksi.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="p_member">Potongan Member</label>
                                        <input type="text" class="form-control" name="p_member" id="p_member" placeholder="Potongan member.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="b_aplikasi">Biaya Aplikasi</label>
                                        <input type="text" class="form-control" name="b_aplikasi" id="b_aplikasi" placeholder="Biaya aplikasi.." />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="total" placeholder="Nilai total harga.." style="background-color: #fff !important;" required />
                            </div>

                            <div class="form-group">
                                @if (count($toko) > 0)
                                    <label for="toko">Toko <span class="text-info">(Opsional)</span></label>
                                    <select class="form-select form-control" name="toko" id="toko">
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

        <!-- Modal Edit data -->
        <div class="modal fade" id="operasionalEditModal" tabindex="-1" role="dialog" aria-labelledby="operasionalEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="operasionalEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Operasional </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('operasional-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_operasional">
                            <input type="hidden" name="page" value="{{ request()->get('page', 1) }}">

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Pemesanan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="edit-tanggal" required />
                                </div>
                                <div class="col-8">
                                    <label for="uraian">Uraian</label>
                                    <input type="text" class="form-control" name="uraian" id="edit-uraian" placeholder="Masukkan uraian.." required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="nama">Nama / Pemesan</label>
                                    <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                                <div class="col-8">
                                    <label for="deskripsi">Deskripsi</label>
                                    <input type="text" class="form-control" name="deskripsi" id="edit-deskripsi" placeholder="Masukkan deskripsi.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="lokasi">Lokasi <span class="text-info">(Opsional)</span></label>
                                <input type="text" class="form-control" name="lokasi" id="edit-lokasi" placeholder="Masukkan lokasi.." 
                                oninput="this.value = this.value.toUpperCase()" />
                            </div>

                            <div class="form-group">
                                <label>Metode Pembelian</label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check mr-3">
                                        <input class="form-check-input" type="radio" name="metode_pembelian" id="edit-is_online" value="online" onclick="toggleFieldsEdit()" />
                                        <label class="form-check-label" for="edit-is_online">
                                            Online
                                        </label>
                                    </div>
                                    <div class="form-check mr-3">
                                        <input class="form-check-input" type="radio" name="metode_pembelian" id="edit-is_offline" value="offline" onclick="toggleFieldsEdit()" />
                                        <label class="form-check-label" for="edit-is_offline">
                                            Offline
                                        </label>
                                    </div>
                                    <!-- <div>
                                        <span type="button" class="badge badge-black" style="margin-bottom: 8px;" onclick="resetFieldsEdit()">Reset</span>
                                    </div> -->
                                </div>
                            </div>

                            <div id="barangEdit">
                                <!-- barang -->
                            </div>

                            <!-- <div class="form-group">
                                <button type="button" class="btn btn-primary btn-sm" onclick="addBarangEdit()">Tambah Barang</button>
                            </div> -->

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div id="onlineFieldsEdit" class="d-none">
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="diskon">Diskon</label>
                                        <input type="text" class="form-control" name="diskon" id="edit-diskon" placeholder="Diskon.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="ongkir">Ongkir</label>
                                        <input type="text" class="form-control" name="ongkir" id="edit-ongkir" placeholder="Ongkir.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="asuransi">Asuransi</label>
                                        <input type="text" class="form-control" name="asuransi" id="edit-asuransi" placeholder="Asuransi.." />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="b_proteksi">Biaya Proteksi</label>
                                        <input type="text" class="form-control" name="b_proteksi" id="edit-b_proteksi" placeholder="Biaya proteksi.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="p_member">Potongan Member</label>
                                        <input type="text" class="form-control" name="p_member" id="edit-p_member" placeholder="Potongan member.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="b_aplikasi">Biaya Aplikasi</label>
                                        <input type="text" class="form-control" name="b_aplikasi" id="edit-b_aplikasi" placeholder="Biaya aplikasi.." />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="edit-total" placeholder="Nilai total harga.." style="background-color: #fff !important;" required />
                            </div>

                            <div class="form-group">
                                @if (count($toko) > 0)
                                    <label for="toko">Toko <span class="text-info">(Opsional)</span></label>
                                    <select class="form-select form-control" name="toko" id="edit-toko">
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
        <div class="modal fade" id="operasionalExportModal" tabindex="-1" role="dialog" aria-labelledby="operasionalExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="operasionalExportModal">
                            <span class="fw-light"> Export Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Operasional </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('operasional-export') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Pengaturan untuk menentukan format data pengeluaran sesuai keinginan
                            </p>

                            <div class="form-group">
                                <label>Metode Pembelian</label>
                                <select class="form-select form-control" id="metode_pembelian" name="metode_pembelian" required>
                                    <option value="">...</option>
                                    @if (count($opsOnline) > 0)
                                        <option value="online">Online</option>
                                    @endif
                                    @if (count($opsOffline) > 0)
                                        <option value="offline">Offline</option>
                                    @endif
                                    @if (count($opsOnline) > 0 && count($opsOffline) > 0)
                                        <option value="online_dan_offline">Offline dan Online</option>
                                    @endif
                                </select>
                            </div>

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
                            <div class="card-title">List Pengeluaran Operasional</div>
                            <input type="hidden" id="allSelectRow" name="ids" value="">
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#operasionalEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('operasional-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="deleteAllSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            @if (Auth::user()->level == 0)
                            <form id="statusPendingForm" method="POST" action="{{ url('operasional-status_pending') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="pendingAllSelectRow" name="ids" value="">
                                <button id="statusPendingButton" type="button" class="btn btn-warning btn-round ms-5 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Pending
                                </button>
                            </form>
                            <form id="statusProcessForm" method="POST" action="{{ url('operasional-status_process') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="processAllSelectRow" name="ids" value="">
                                <button id="statusProcessButton" type="button" class="btn btn-black btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Processing
                                </button>
                            </form>
                            <form id="statusPaidForm" method="POST" action="{{ url('operasional-status_paid') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="paidAllSelectRow" name="ids" value="">
                                <button id="statusPaidButton" type="button" class="btn btn-success btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Paid
                                </button>
                            </form>
                            @endif
                            <div class="ms-auto d-flex align-items-center">
                                @if (count($operasional) > 0)
                                    <button class="btn btn-success btn-round ms-2 btn-sm" data-bs-toggle="modal" data-bs-target="#operasionalExportModal">
                                        <i class="fa fa-file-excel"></i>
                                        Export data
                                    </button>
                                @endif
                                <button class="btn btn-primary btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#operasionalModal">
                                    <i class="fa fa-plus"></i>
                                    Tambah data
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (count($operasional) > 0)
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
                                        <th class="text-xxs-bold">Uraian</th>
                                        <th class="text-xxs-bold">Deskripsi</th>
                                        <th class="text-xxs-bold">Lokasi</th>
                                        <th class="text-xxs-bold">Pemesan</th>
                                        <th class="text-xxs-bold">Barang</th>
                                        <!-- <th class="text-xxs-bold">Jumlah</th>
                                        <th class="text-xxs-bold">Satuan</th> -->
                                        <!-- <th class="text-xxs-bold">Harga Toko</th>
                                        <th class="text-xxs-bold">Diskon</th>
                                        <th class="text-xxs-bold">Harga Online</th>
                                        <th class="text-xxs-bold">Ongkir</th>
                                        <th class="text-xxs-bold">Asuransi</th>
                                        <th class="text-xxs-bold">Biaya Proteksi</th>
                                        <th class="text-xxs-bold">Potongan Member</th>
                                        <th class="text-xxs-bold">Biaya Aplikasi</th> -->
                                        <th class="text-xxs-bold">Total Harga</th>
                                        <th class="text-xxs-bold">Toko</th>
                                        <th class="text-xxs-bold">Via</th>
                                        <th class="text-xxs-bold">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($operasional as $o)
                                    <tr data-id="{{ $o->id_operasional }}" 
                                        data-tanggal="{{ $o->tanggal }}" 
                                        data-uraian="{{ $o->uraian }}"
                                        data-deskripsi="{{ $o->deskripsi }}"
                                        data-lokasi="{{ $o->lokasi }}"
                                        data-nama="{{ $o->nama }}"
                                        data-diskon="{{ 'Rp ' . number_format($o->diskon ?? 0, 0, ',', '.') }}"
                                        data-ongkir="{{ 'Rp ' . number_format($o->ongkir ?? 0, 0, ',', '.') }}"
                                        data-asuransi="{{ 'Rp ' . number_format($o->asuransi ?? 0, 0, ',', '.') }}"
                                        data-b_proteksi="{{ 'Rp ' . number_format($o->b_proteksi ?? 0, 0, ',', '.') }}"
                                        data-p_member="{{ 'Rp ' . number_format($o->p_member ?? 0, 0, ',', '.') }}"
                                        data-b_aplikasi="{{ 'Rp ' . number_format($o->b_aplikasi ?? 0, 0, ',', '.') }}"
                                        data-total="{{ 'Rp ' . number_format($o->total ?? 0, 0, ',', '.') }}"
                                        data-toko="{{ $o->id_toko }}"
                                        data-metode_pembelian="{{ $o->metode_pembelian }}"
                                        data-file="{{ $o->file }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $o->tanggal)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $o->uraian ?? '-' }}</td>
                                        <td class="deskripsi">{{ $o->deskripsi ?? '-' }}</td>
                                        <td>{{ $o->lokasi ?? '-' }}</td>
                                        @if ($o->file)
                                            <td>
                                                <a href="{{ asset('storage/' . $o->file) }}" target="_blank">{{ $o->nama ?? '-' }}</a>
                                            </td>
                                        @else
                                            <td>{{ $o->nama ?? '-' }}</td>
                                        @endif
                                        @php
                                            $databarang = $barang->where('id_relasi', $o->id_operasional);
                                            if ($databarang->count() > 0) {
                                                // Menggabungkan nama barang dengan koma hanya jika ada lebih dari satu barang
                                                $listBarang = $databarang->pluck('nama')->count() > 1 
                                                    ? $databarang->pluck('nama')->join(', ') 
                                                    : $databarang->pluck('nama')->first();
                                            } else {
                                                $listBarang = '-';
                                            }
                                        @endphp
                                        <td>{{ $listBarang }}</td>
                                        <!-- <td>{{ $o->qty ?? '-' }}</td>
                                        <td>{{ $o->unit ?? '-' }}</td> -->
                                        <!-- <td>{{ 'Rp ' . number_format($o->harga_toko ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($o->diskon ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($o->harga_onl ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($o->ongkir ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($o->asuransi ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($o->b_proteksi ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($o->p_member ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($o->b_aplikasi ?? 0, 0, ',', '.') }}</td> -->
                                        <td>{{ 'Rp ' . number_format($o->total ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $namaToko[$o->id_toko] ?? '-' }}</td>
                                        <td>{{ ucfirst($o->metode_pembelian) ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($o->status) {
                                                    'pending' => 'status-pending',
                                                    'processing' => 'status-process',
                                                    default => 'status-paid',
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ ucfirst($o->status) }}</span>
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
            if (row.querySelector('td:nth-child(9)')) {
                const totalText = row.querySelector('td:nth-child(9)').innerText;
                const totalValue = parseInt(totalText.replace(/[^0-9,-]+/g, ""));
                totalSum += totalValue;
            }
        });
        if (document.getElementById('total-sum')) {
            document.getElementById('total-sum').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalSum);
        }
    }

    let itemCount = 2;

    function addBarang() {
        var container = document.getElementById('barangFields');
        var newItem = document.createElement('div');
        newItem.className = 'barang-item';
        newItem.innerHTML = `
            <div class="form-group row">
                <div class="col-4">
                    <label for="nama_barang">Barang ke-${itemCount}</label>
                    <input type="text" class="form-control" name="nama_barang[]" id="nama_barang" id="barang" placeholder="Masukkan nama barang.." required />
                </div>
                <div class="col-2">
                    <label for="qty">Jumlah</label>
                    <input type="text" class="form-control" name="qty[]" id="qty" placeholder="Jumlah.." required />
                </div>
                <div class="col-2">
                    @if (count($satuan) > 0)
                        <label for="satuan">Satuan</label>
                        <select class="form-select form-control" name="unit[]" id="unit" required>
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
                <div class="col-4">
                    <label for="harga">Harga</label>
                    <input type="text" class="form-control" name="harga[]" id="harga" placeholder="Masukkan harga.." required />
                </div>
            </div>
        `;
        container.appendChild(newItem);
        itemCount++;
        attachEventListeners();
    }

    let itemCountEdit = 1;

    function addBarangEdit() {
        var container = document.getElementById('barangFieldsEdit');
        var newItem = document.createElement('div');
        newItem.className = 'barangEdit-item';

        // Temukan indeks terakhir dari barang yang ada
        var lastItemIndex = container.querySelectorAll('.barangEdit-item').length;
        var newIndex = lastItemIndex + 1;

        newItem.innerHTML = `
            <div class="form-group row">
                <div class="col-4">
                    <label for="nama_barang">Barang ke-${newIndex}</label>
                    <input type="text" class="form-control" name="nama_barang[]" id="nama_barang" placeholder="Masukkan nama barang.." required />
                </div>
                <div class="col-2">
                    <label for="qty">Jumlah</label>
                    <input type="text" class="form-control" name="qty[]" id="qty" placeholder="Jumlah.." required />
                </div>
                <div class="col-2">
                    @if (count($satuan) > 0)
                        <label for="satuan">Satuan</label>
                        <select class="form-select form-control" name="unit[]" id="unit" required>
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
                <div class="col-4">
                    <label for="harga">Harga</label>
                    <input type="text" class="form-control" name="harga[]" id="harga" placeholder="Masukkan harga.." required />
                </div>
            </div>
        `;
        container.appendChild(newItem);
        itemCountEdit = newIndex;
        attachEventListeners();
    }

    function parseFraction(fraction) {
        let parts = fraction.split('/');
        if (parts.length === 2) {
            return parseFloat(parts[0]) / parseFloat(parts[1]);
        } else {
            return parseFloat(fraction);
        }
    }

    function calculateSubtotal() {
        let hargaElements = document.querySelectorAll("#harga");
        let qtyElements = document.querySelectorAll("#qty");
        let subtotal = 0;

        hargaElements.forEach((hargaElem, index) => {
            let hargaValue = parseInt(hargaElem.value.replace(/[^0-9]/g, ""), 10) || 0;
            let qtyValue = parseFraction(qtyElements[index].value.replace(',', '.')) || 0;
            subtotal += hargaValue * qtyValue;
        });

        return subtotal;
    }

    function updateTotal() {
        let hargaElements = document.querySelectorAll("#harga");
        let qtyElements = document.querySelectorAll("#qty");
        let diskonElements = document.querySelectorAll("#diskon");
        let ongkirElements = document.querySelectorAll("#ongkir");
        let asuransiElements = document.querySelectorAll("#asuransi");
        let proteksiElements = document.querySelectorAll("#b_proteksi");
        let memberElements = document.querySelectorAll("#p_member");
        let aplikasiElements = document.querySelectorAll("#b_aplikasi");
        let totalElements = document.querySelectorAll("#total");
        let totalElement = document.getElementById("total");
        
        // Calculate subtotal
        let subtotal = calculateSubtotal();

        // Initialize total value
        let totalValue = subtotal;

        let ongkirlValue = parseInt(ongkirElements[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let asuransiValue = parseInt(asuransiElements[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let proteksiValue = parseInt(proteksiElements[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let memberValue = parseInt(memberElements[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let aplikasiValue = parseInt(aplikasiElements[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let diskonValue = parseInt(diskonElements[0].value.replace(/[^0-9]/g, ""), 10) || 0;

        totalValue = subtotal + (ongkirlValue + asuransiValue + proteksiValue + aplikasiValue) - (diskonValue + memberValue);
        totalElements[0].value = formatCurrency(totalValue);
        // totalElement.value = formatCurrency(totalValue);

    }

    function attachEventListeners() {
        let hargaElements = document.querySelectorAll("#harga");
        let qtyElements = document.querySelectorAll("#qty");

        hargaElements.forEach(harga => {
            harga.addEventListener("input", function() {
                this.value = formatCurrency(this.value);
                updateTotal();
            });
        });

        qtyElements.forEach(function(qty) {
            qty.addEventListener("input", updateTotal);
        });
    }

    function toggleFields() {
        const isOnlineChecked = document.getElementById('is_online').checked;
        const barangDiv = document.getElementById('barang');
        const onlineFields = document.getElementById('onlineFields');
        const inputs = onlineFields.querySelectorAll('input');

        if (isOnlineChecked) {
            setFieldsRequired(onlineFields, true);
            barangDiv.classList.remove('d-none');
            onlineFields.classList.remove('d-none');

        } else {
            // Clear online fields
            inputs.forEach(input => input.value = '');
            
            // Update total based on non-online fields
            updateTotal();
            
            setFieldsRequired(onlineFields, false);
            barangDiv.classList.remove('d-none');
            onlineFields.classList.add('d-none');
        }
    }

    function toggleFieldsEdit() {
        const isOnlineEditChecked = document.getElementById('edit-is_online').checked;
        const barangDivEdit = document.getElementById('barangEdit');
        const onlineFieldsEdit = document.getElementById('onlineFieldsEdit');
        const inputs = onlineFieldsEdit.querySelectorAll('input');

        if (isOnlineEditChecked) {
            setFieldsRequired(onlineFieldsEdit, true);
            barangDivEdit.classList.remove('d-none');
            onlineFieldsEdit.classList.remove('d-none');

        } else {
            // Clear online fields
            inputs.forEach(input => input.value = '');

            // Update total based on non-online fields
            updateTotalEdit();
            
            setFieldsRequired(onlineFieldsEdit, false);
            barangDivEdit.classList.remove('d-none');
            onlineFieldsEdit.classList.add('d-none');
        }
    }

    function calculateSubtotalEdit() {
        let hargaElementsEdit = document.querySelectorAll("#edit-harga");
        let qtyElementsEdit = document.querySelectorAll("#edit-qty");
        let subtotalEdit = 0;

        hargaElementsEdit.forEach((hargaElem, index) => {
            let hargaValue = parseInt(hargaElem.value.replace(/[^0-9]/g, ""), 10) || 0;
            let qtyValue = parseFraction(qtyElementsEdit[index].value.replace(',', '.')) || 0;
            subtotalEdit += hargaValue * qtyValue;
        });

        return subtotalEdit;
    }

    function updateTotalEdit() {
        let hargaElementsEdit = document.querySelectorAll("#edit-harga");
        let qtyElementsEdit = document.querySelectorAll("#edit-qty");
        let diskonElementsEdit = document.querySelectorAll("#edit-diskon");
        let ongkirElementsEdit = document.querySelectorAll("#edit-ongkir");
        let asuransiElementsEdit = document.querySelectorAll("#edit-asuransi");
        let proteksiElementsEdit = document.querySelectorAll("#edit-b_proteksi");
        let memberElementsEdit = document.querySelectorAll("#edit-p_member");
        let aplikasiElementsEdit = document.querySelectorAll("#edit-b_aplikasi");
        let totalElementsEdit = document.querySelectorAll("#edit-total");
        let totalElement = document.getElementById("#edit-total");
        
        // Calculate subtotalEdit
        let subtotalEdit = calculateSubtotalEdit();

        // Initialize total value
        let totalValue = subtotalEdit;

        let ongkirlValue = parseInt(ongkirElementsEdit[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let asuransiValue = parseInt(asuransiElementsEdit[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let proteksiValue = parseInt(proteksiElementsEdit[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let memberValue = parseInt(memberElementsEdit[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let aplikasiValue = parseInt(aplikasiElementsEdit[0].value.replace(/[^0-9]/g, ""), 10) || 0;
        let diskonValue = parseInt(diskonElementsEdit[0].value.replace(/[^0-9]/g, ""), 10) || 0;

        totalValue = subtotalEdit + (ongkirlValue + asuransiValue + proteksiValue + aplikasiValue) - (diskonValue + memberValue);
        if (totalValue != '0') {
            totalElementsEdit[0].value = formatCurrency(totalValue);
            // totalElement.value = formatCurrency(totalValue);
        }

    }

    function setFieldsRequired(fieldsContainer, isRequired) {
        const inputs = fieldsContainer.querySelectorAll('input');
        inputs.forEach(input => {
            if (isRequired) {
                input.setAttribute('required', 'required');
            } else {
                input.removeAttribute('required');
            }
        });
    }

    // Reset radio buttons
    function resetFields() {
        document.getElementById('is_online').checked = false;
        document.getElementById('is_offline').checked = false;
        document.getElementById('barang').classList.add('d-none');
        setFieldsRequired(document.getElementById('onlineFields'), false);
        document.getElementById('onlineFields').classList.add('d-none');
        
        var barangFieldsContainer = document.getElementById('barangFields');
        while (barangFieldsContainer.firstChild) {
            barangFieldsContainer.removeChild(barangFieldsContainer.firstChild);
        }
        
        itemCount = 1;
        addBarang();
        document.getElementById('total').value = '';
    }

    // function resetFieldsEdit() {
    //     document.getElementById('edit-is_online').checked = false;
    //     document.getElementById('edit-is_offline').checked = false;
    //     document.getElementById('barangEdit').classList.add('d-none');
    //     setFieldsRequired(document.getElementById('onlineFieldsEdit'), false);
    //     document.getElementById('onlineFieldsEdit').classList.add('d-none');

    //     var barangFieldsContainerEdit = document.getElementById('barangFieldsEdit');
    //     while (barangFieldsContainerEdit.firstChild) {
    //         barangFieldsContainerEdit.removeChild(barangFieldsContainerEdit.firstChild);
    //     }
        
    //     itemCount = 1;
    //     addBarangEdit();
    //     document.getElementById('edit-total').value = '';
    // }

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

    function setSelectFieldsRequired(fieldsContainer, isRequired) {
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

    document.addEventListener('DOMContentLoaded', function() {
        const tableData = $('#basic-datatables').DataTable();
        tableData.on('draw.dt', function() {
            calculateTotal();
        });

        calculateTotal();

        var batasKarakter = 55;
        var cells = document.querySelectorAll('td.deskripsi');

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

        let hargaElements = document.querySelectorAll("#harga");
        let qtyElements = document.querySelectorAll("#qty");
        let diskonElements = document.querySelectorAll("#diskon");
        let ongkirElements = document.querySelectorAll("#ongkir");
        let asuransiElements = document.querySelectorAll("#asuransi");
        let proteksiElements = document.querySelectorAll("#b_proteksi");
        let memberElements = document.querySelectorAll("#p_member");
        let aplikasiElements = document.querySelectorAll("#b_aplikasi");
        let totalElements = document.querySelectorAll("#total");
        let isOnlineChecked = document.getElementById('is_online')?.checked || false;

        let subtotal = calculateSubtotal();
        let totalValue = subtotal;

        if (!isOnlineChecked) {
            [diskonElements, ongkirElements, asuransiElements, proteksiElements, memberElements, aplikasiElements].forEach((elements) => {
                elements.forEach((element, index) => {
                    element.addEventListener("input", function() {
                        this.value = formatCurrency(this.value.replace(/[^0-9]/g, ""));
                        updateTotal();
                    });
                });
            });
        }

        qtyElements.forEach((jml, index) => {
            jml.addEventListener("input", function() {
                let harga = hargaElements[index];
                let total = totalElements[0];
                let jmlValue = parseFraction(jml.value.replace(',', '.'));

                if (harga) {
                    let hargaValue = parseInt(harga.value.replace(/[^0-9]/g, ""), 10) || 0;
                    let totalValue = hargaValue * jmlValue;
                    total.value = formatCurrency(totalValue);

                    updateTotal();
                }
            });
        });

        hargaElements.forEach((harga, index) => {
            harga.addEventListener("input", function() {
                let jml = qtyElements[index];
                let total = totalElements[0];
                let hargaValueFormatted = this.value.replace(/[^0-9]/g, "") || 0;
                this.value = formatCurrency(hargaValueFormatted);
                let jmlValue = parseFraction(jml.value.replace(',', '.'));

                if (jmlValue) {
                    let hargaValue = parseInt(hargaValueFormatted, 10) || 0;
                    let totalValue = hargaValue * jmlValue;
                    total.value = formatCurrency(totalValue);

                    updateTotal();
                }
            });
        });

        // Add currency formatter to all relevant elements
        [hargaElements, diskonElements, ongkirElements, asuransiElements, proteksiElements, memberElements, aplikasiElements, totalElements].forEach(elements => {
            elements.forEach(element => {
                element.addEventListener("input", function() {
                    this.value = formatCurrency(this.value.replace(/[^0-9]/g, ""));
                });
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
                    $('#edit-uraian').val(row.data('uraian'));
                    $('#edit-deskripsi').val(row.data('deskripsi'));
                    $('#edit-lokasi').val(row.data('lokasi'));
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-total').val(row.data('total'));
                    $('#edit-toko').val(row.data('toko'));

                    if (row.data('metode_pembelian') == 'online') {
                        $('#edit-is_online').prop('checked', true);
                        toggleFieldsEdit();

                        $('#edit-diskon').val(row.data('diskon'));
                        $('#edit-ongkir').val(row.data('ongkir'));
                        $('#edit-asuransi').val(row.data('asuransi'));
                        $('#edit-b_proteksi').val(row.data('b_proteksi'));
                        $('#edit-p_member').val(row.data('p_member'));
                        $('#edit-b_aplikasi').val(row.data('b_aplikasi'));
                    }

                    if (row.data('metode_pembelian') == 'offline') {
                        $('#edit-is_offline').prop('checked', true);
                        toggleFieldsEdit();
                    }

                    // Menampilkan file yang diunggah
                    var file = row.data('file');
                    if (file) {
                        var fileUrl = "{{ asset('storage/') }}" + "/" + file;
                        $('#current-file').html('<a href="' + fileUrl + '" target="_blank">' + file + '</a>');
                    } else {
                        $('#current-file').html('<span>Tidak ada file yang diunggah</span>');
                    }
                    
                    // Barang
                    var barangEdit = document.getElementById('barangEdit');
                    barangEdit.innerHTML = '';
                    barangEdit.innerHTML += `
                        <div class="form-group">
                            <span class="h5 fw-mediumbold">Informasi Barang</span>
                        </div>
                        <div id="barangFieldsEdit" class="form-group row">
                        </div>`;

                    // Generate fields for barang data
                    var barangData = @json($barang->toArray());
                    var satuanData = @json($satuan->toArray());
                    var barang = barangData.filter(item => item.id_relasi == selectedId);
                    barang.forEach(function(item, index) {
                        barangEdit.innerHTML += `
                            <div class="barangEdit-item">
                                <div class="form-group row">
                                    <input type="hidden" id="edit-id_barang" name="id_barang[]" value="${item.id_barang}">
                                    <div class="col-4">
                                        <label for="nama_barang">Barang ke-${index + 1}</label>
                                        <input type="text" class="form-control" name="nama_barang[]" id="edit-barang"
                                        value="${item.nama}" oninput="this.value = this.value.toUpperCase()" placeholder="Masukkan nama barang.." required />
                                    </div>
                                    <div class="col-2">
                                        <label for="qty">Jumlah</label>
                                        <input type="text" class="form-control" name="qty[]" value="${item.jumlah}" id="edit-qty" placeholder="Jumlah.." required />
                                    </div>
                                    <div class="col-2">
                                        @if (count($satuan) > 0)
                                            <label for="satuan">Satuan</label>
                                            <select class="form-select form-control" name="unit[]" id="edit-unit" required>
                                                <option value="">...</option>
                                                @foreach ($satuan as $s)
                                                    <option value="{{ $s->id_satuan }}" ${item.id_satuan == '{{ $s->id_satuan }}' ? 'selected' : ''}>{{ $s->nama }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <label>Satuan</label>
                                            <select class="form-control" disabled>
                                                <option value="">Tidak ada data</option>
                                            </select>
                                        @endif
                                    </div>
                                    <div class="col-4">
                                        <label for="harga">Harga</label>
                                        <input type="text" class="form-control" name="harga[]" value="${formatCurrency(item.harga)}" id="edit-harga" placeholder="Masukkan harga.." required />
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    // Attach event listeners to dynamically added elements
                    let hargaElementsEdit = document.querySelectorAll("#edit-harga");
                    let qtyElementsEdit = document.querySelectorAll("#edit-qty");
                    let diskonElementsEdit = document.querySelectorAll("#edit-diskon");
                    let ongkirElementsEdit = document.querySelectorAll("#edit-ongkir");
                    let asuransiElementsEdit = document.querySelectorAll("#edit-asuransi");
                    let proteksiElementsEdit = document.querySelectorAll("#edit-b_proteksi");
                    let memberElementsEdit = document.querySelectorAll("#edit-p_member");
                    let aplikasiElementsEdit = document.querySelectorAll("#edit-b_aplikasi");
                    let totalElementsEdit = document.querySelectorAll("#edit-total");
                    let editIsOnlineChecked = document.getElementById('edit-is_online')?.checked || false;
                    let editIsOfflineChecked = document.getElementById('edit-is_offline')?.checked || false;

                    let subtotalEdit = calculateSubtotalEdit();
                    let totalValueEdit = subtotalEdit;

                    if (!editIsOnlineChecked) {
                        [diskonElementsEdit, ongkirElementsEdit, asuransiElementsEdit, proteksiElementsEdit, memberElementsEdit, aplikasiElementsEdit].forEach((elements) => {
                            elements.forEach((element, index) => {
                                element.addEventListener("input", function() {
                                    this.value = formatCurrency(this.value.replace(/[^0-9]/g, ""));
                                    updateTotalEdit();
                                });
                            });
                        });
                    }

                    qtyElementsEdit.forEach((jml, index) => {
                        jml.addEventListener("input", function() {
                            let harga = hargaElementsEdit[index];
                            let total = totalElementsEdit[0];
                            let jmlValue = parseFraction(jml.value.replace(',', '.'));

                            if (harga) {
                                let hargaValue = parseInt(harga.value.replace(/[^0-9]/g, ""), 10) || 0;
                                let totalValueEdit = hargaValue * jmlValue;
                                total.value = formatCurrency(totalValueEdit);

                                updateTotalEdit();
                            }
                        });
                    });

                    hargaElementsEdit.forEach((harga, index) => {
                        harga.addEventListener("input", function() {
                            let jml = qtyElementsEdit[index];
                            let total = totalElementsEdit[0];
                            let hargaValueFormatted = this.value.replace(/[^0-9]/g, "") || 0;
                            this.value = formatCurrency(hargaValueFormatted);
                            let jmlValue = parseFraction(jml.value.replace(',', '.'));

                            if (jmlValue) {
                                let hargaValue = parseInt(hargaValueFormatted, 10) || 0;
                                let totalValue = hargaValue * jmlValue;
                                total.value = formatCurrency(totalValue);

                                updateTotalEdit();
                            }
                        });
                    });

                    // Add currency formatter to all relevant elements
                    [hargaElementsEdit, diskonElementsEdit, ongkirElementsEdit, asuransiElementsEdit, proteksiElementsEdit, memberElementsEdit, aplikasiElementsEdit, totalElementsEdit].forEach(elements => {
                        elements.forEach(element => {
                            element.addEventListener("input", function() {
                                this.value = formatCurrency(this.value.replace(/[^0-9]/g, ""));
                                updateTotalEdit();
                            });
                        });
                    });
                }
            });
        }

    });
</script>
@endsection