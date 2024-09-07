@extends('layouts.base')
<!-- @section('title', 'Permintaan Barang') -->
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

    .status-waiting {
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

    .status-approved {
        display: inline-block;
        padding: 5px 10px;
        font-size: 11.5px;
        font-weight: bold;
        color: white;
        background-color: #6861CE;
        border-radius: 10px;
        text-align: center;
        width: 80px;
    }
</style>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Data Permintaan Barang</h3>
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
                    <a href="#">Permintaan Barang</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah Data -->
        <div class="modal fade" id="permintaanBarangModal" tabindex="-1" role="dialog" aria-labelledby="permintaanBarangModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="permintaanBarangModal">
                            <span class="fw-light"> Form</span>
                            <span class="fw-mediumbold"> Permintaan Barang </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('permintaan_barang-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="user" name="user" value="{{ auth()->user()->name }}">

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="noform">No. Form</label>
                                    <input type="text" class="form-control" name="noform" id="noform" oninput="this.value = this.value.toUpperCase()" 
                                    placeholder="Masukkan no. form.." required />
                                </div>

                                <div class="col-6">
                                    <label for="tgl_order">Tanggal</label>
                                    <input type="date" class="form-control" name="tgl_order" id="tgl_order" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="nama">Nama</label>
                                    <input type="text" class="form-control" name="nama" id="nama" oninput="this.value = this.value.toUpperCase()" 
                                    placeholder="Masukkan nama.." required />
                                </div>

                                <div class="col-6">
                                    <label for="jabatan">Jabatan</label>
                                    <input type="text" class="form-control" name="jabatan" id="jabatan" placeholder="Masukkan jabatan.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="kategori">Kategori Barang</label>
                                    <select class="form-control" name="kategori" id="kategori" required>
                                        <option value="" disabled selected>...</option>
                                        <option value="BESI">BESI</option>
                                        <option value="CAT">CAT</option>
                                        <option value="MATERIAL">MATERIAL</option>
                                        <option value="SPAREPART">SPAREPART</option>
                                    </select>
                                </div>

                                <div class="col-6" id="div_sub_kategori" style="display: none;">
                                    <label for="sub_kategori">Kategori Material</label>
                                    <input type="text" class="form-control" name="sub_kategori" id="sub_kategori" placeholder="Kategori material cth: pasir, kerikil, batu atau lainnya.." 
                                    oninput="this.value = this.value.toUpperCase()" />
                                </div>

                                <div class="col-6" id="div_kegunaan">
                                    <label for="kegunaan">Digunakan untuk</label>
                                    <input type="text" class="form-control" name="kegunaan" id="kegunaan" placeholder="Digunakan untuk.." required />
                                </div>
                            </div>

                            <div class="form-group" id="div_row_kegunaan" style="display: none;">
                                <label for="row_kegunaan">Digunakan untuk</label>
                                <input type="text" class="form-control" name="row_kegunaan" id="row_kegunaan" placeholder="Digunakan untuk.." />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Barang</span>
                            </div>

                            <div class="form-group">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <td class="text-center fw-mediumbold">No.</td>
                                            <td class="text-center fw-mediumbold">Jumlah</td>
                                            <td class="text-center fw-mediumbold">Satuan</td>
                                            <td class="text-center fw-mediumbold">Nama Barang</td>
                                            <td class="text-center fw-mediumbold">Keterangan</td>
                                            <td width=5%></td>
                                        </tr>
                                    </thead>
                                    <tbody id="dataBarang">
                                        <tr>
                                            <td style="padding: 7.5px 2.5px !important;">
                                                <div class="text-center text-sm">
                                                    1.
                                                </div>
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;" width=8%>
                                                <input type="text" class="form-control text-center" name="jumlah[]" style="border: 0px; padding: .1rem 0.5rem;" 
                                                placeholder="..." required>
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;" width=18%>
                                                @if (count($satuan) > 0)
                                                    <select class="form-select form-control" name="unit[]" id="unit" style="border: 0px;" required>
                                                        <option class="text-center" value="">...</option>
                                                        @foreach ($satuan as $s)
                                                            <option class="text-center" value="{{ $s->id_satuan }}">{{ $s->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="text" class="form-control text-center" placeholder="Tidak ada data" 
                                                    style="border: 0px; background-color: #fff !important;" disabled>
                                                @endif
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;">
                                                <input type="text" class="form-control" name="nama_barang[]" oninput="this.value = this.value.toUpperCase()" 
                                                style="border: 0px; padding: .1rem 0.5rem;" placeholder="Masukkan nama barang.." required>
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;">
                                                <input type="text" class="form-control" name="ket[]" placeholder="Masukkan keterangan.." style="border: 0px; padding: .1rem 0.5rem;" required>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div>
                                    <button id="addRowButton" class="btn btn-outline-primary btn-sm" type="button" style="border: none;">
                                        <span ><u>+</u> Tambah baris baru</span>
                                    </button>
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

        <!-- Modal Edit Data -->
        <div class="modal fade" id="permintaanBarangEditModal" tabindex="-1" role="dialog" aria-labelledby="permintaanBarangEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="permintaanBarangEditModal">
                            <span class="fw-light"> Form</span>
                            <span class="fw-mediumbold"> Permintaan Barang </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('permintaan_barang-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_permintaan_barang">
                            <input type="hidden" id="edit-nama_kategori" name="nama_kategori">
                            <input type="hidden" id="user" name="user" value="{{ auth()->user()->name }}">

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="noform">No. Form</label>
                                    <input type="text" class="form-control" name="noform" id="edit-noform" oninput="this.value = this.value.toUpperCase()" 
                                    placeholder="Masukkan no. form.." readonly />
                                </div>

                                <div class="col-6">
                                    <label for="tgl_order">Tanggal</label>
                                    <input type="date" class="form-control" name="tgl_order" id="edit-tgl_order" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="nama">Nama</label>
                                    <input type="text" class="form-control" name="nama" id="edit-nama" oninput="this.value = this.value.toUpperCase()" 
                                    placeholder="Masukkan nama.." required />
                                </div>

                                <div class="col-6">
                                    <label for="jabatan">Jabatan</label>
                                    <input type="text" class="form-control" name="jabatan" id="edit-jabatan" placeholder="Masukkan jabatan.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="kategori">Kategori Barang</label>
                                    <select class="form-control" name="kategori" id="edit-kategori" disabled>
                                        <option value="" disabled selected>...</option>
                                        <option value="BESI">BESI</option>
                                        <option value="CAT">CAT</option>
                                        <option value="MATERIAL">MATERIAL</option>
                                        <option value="SPAREPART">SPAREPART</option>
                                    </select>
                                </div>

                                <div class="col-6" id="edit-div_sub_kategori" style="display: none;">
                                    <label for="sub_kategori">Kategori Material</label>
                                    <input type="text" class="form-control" name="sub_kategori" id="edit-sub_kategori" placeholder="Kategori material cth: pasir, kerikil, batu atau lainnya.." 
                                    oninput="this.value = this.value.toUpperCase()" readonly />
                                </div>

                                <div class="col-6" id="edit-div_kegunaan">
                                    <label for="kegunaan">Digunakan untuk</label>
                                    <input type="text" class="form-control" name="kegunaan" id="edit-kegunaan" placeholder="Digunakan untuk.." required />
                                </div>
                            </div>

                            <div class="form-group" id="edit-div_row_kegunaan" style="display: none;">
                                <label for="row_kegunaan">Digunakan untuk</label>
                                <input type="text" class="form-control" name="row_kegunaan" id="edit-row_kegunaan" placeholder="Digunakan untuk.." />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Barang</span>
                            </div>

                            <div id="barangFieldsEdit" class="form-group">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <td class="text-center fw-mediumbold">No.</td>
                                            <td class="text-center fw-mediumbold">Jumlah</td>
                                            <td class="text-center fw-mediumbold">Satuan</td>
                                            <td class="text-center fw-mediumbold">Nama Barang</td>
                                            <td class="text-center fw-mediumbold">Keterangan</td>
                                        </tr>
                                    </thead>
                                    <tbody id="dataBarangEdit">
                                    </tbody>
                                </table>
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
        <div class="modal fade" id="permintaanBarangExportModal" tabindex="-1" role="dialog" aria-labelledby="permintaanBarangExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="permintaanBarangExportModal">
                            <span class="fw-light"> Export Data</span>
                            <span class="fw-mediumbold"> Permintaan Barang </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('permintaan_barang-export') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
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
                            <div class="card-title">List Permintaan Barang</div>
                            <input type="hidden" id="allSelectRow" name="ids" value="">
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#permintaanBarangEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('permintaan_barang-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="deleteAllSelectRow" name="ids" value="">
                                <input type="hidden" id="allNoFormRow" name="multi_noform" value="">
                                <input type="hidden" id="allKategoriRow" name="multi_kategori" value="">
                                <input type="hidden" id="user" name="user" value="{{ auth()->user()->name }}">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            @if (Auth::user()->level == 0)
                            <form id="statusPendingForm" method="POST" action="{{ url('permintaan_barang-status_pending') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="pendingAllSelectRow" name="ids" value="">
                                <button id="statusPendingButton" type="button" class="btn btn-warning btn-round ms-5 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Pending
                                </button>
                            </form>
                            <form id="statusWaitingForm" method="POST" action="{{ url('permintaan_barang-status_waiting') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="waitingAllSelectRow" name="ids" value="">
                                <button id="statusWaitingButton" type="button" class="btn btn-black btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Waiting
                                </button>
                            </form>
                            <form id="statusApprovedForm" method="POST" action="{{ url('permintaan_barang-status_approved') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="approvedAllSelectRow" name="ids" value="">
                                <button id="statusApprovedButton" type="button" class="btn btn-secondary btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Approved
                                </button>
                            </form>
                            @endif
                            <div class="ms-auto d-flex align-items-center">
                                @if (count($permintaan_barang) > 0)
                                    <button class="btn btn-success btn-round ms-2 btn-sm" data-bs-toggle="modal" data-bs-target="#permintaanBarangExportModal">
                                        <i class="fa fa-file-excel"></i>
                                        Export data
                                    </button>
                                @endif
                                <button class="btn btn-primary btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#permintaanBarangModal">
                                    <i class="fa fa-plus"></i>
                                    Tambah data
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (count($permintaan_barang) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th width=5%>
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">No. Form</th>
                                        <th class="text-xxs-bold">Nama</th>
                                        <th class="text-xxs-bold">Jabatan</th>
                                        <th class="text-xxs-bold">Kategori</th>
                                        <th class="text-xxs-bold">Tanggal Order</th>
                                        <th class="text-xxs-bold">Digunakan</th>
                                        <th class="text-xxs-bold">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permintaan_barang as $pb)
                                    <tr data-id="{{ $pb->id_permintaan_barang }}" 
                                        data-noform="{{ $pb->noform }}" 
                                        data-nama="{{ $pb->nama }}"
                                        data-jabatan="{{ $pb->jabatan }}"
                                        data-kategori="{{ $pb->kategori }}"
                                        data-sub_kategori="{{ $pb->sub_kategori }}"
                                        data-tgl_order="{{ $pb->tgl_order }}"
                                        data-kegunaan="{{ $pb->kegunaan }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $pb->noform ?? '-' }}</td>
                                        <td>{{ $pb->nama ?? '-' }}</td>
                                        <td>{{ $pb->jabatan ?? '-' }}</td>
                                        <td>{{ $pb->kategori ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $pb->tgl_order)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $pb->kegunaan ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($pb->status) {
                                                    'pending' => 'status-pending',
                                                    'waiting' => 'status-waiting',
                                                    default => 'status-approved',
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ ucfirst($pb->status) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
    // Function display kategori
    const kategoriSelect = document.querySelector('#kategori');
    const subKategoriDiv = document.querySelector('#div_sub_kategori');
    const kegunaanDiv = document.querySelector('#div_kegunaan');
    const rowKegunaanDiv = document.querySelector('#div_row_kegunaan');

    const subKategori = document.querySelector('#sub_kategori');
    const kegunaan = document.querySelector('#kegunaan');
    const rowKegunaan = document.querySelector('#row_kegunaan');

    function updateKategoriDisplay(selectedValue) {
        if (selectedValue === 'MATERIAL') {
            subKategoriDiv.style.display = 'block';
            subKategori.setAttribute('required', 'required');

            rowKegunaanDiv.style.display = 'block';
            rowKegunaan.setAttribute('required', 'required');

            kegunaanDiv.style.display = 'none';
            kegunaan.value = '';
            kegunaan.removeAttribute('required');

        } else {
            subKategoriDiv.style.display = 'none';
            subKategori.value = '';
            subKategori.removeAttribute('required');

            rowKegunaanDiv.style.display = 'none';
            rowKegunaan.value = '';
            rowKegunaan.removeAttribute('required');

            kegunaanDiv.style.display = 'block';
            kegunaan.setAttribute('required', 'required');
        }
    }

    const kategoriSelectEdit = document.querySelector('#edit-kategori');
    const subKategoriDivEdit = document.querySelector('#edit-div_sub_kategori');
    const kegunaanDivEdit = document.querySelector('#edit-div_kegunaan');
    const rowKegunaanDivEdit = document.querySelector('#edit-div_row_kegunaan');

    const subKategoriEdit = document.querySelector('#edit-sub_kategori');
    const kegunaanEdit = document.querySelector('#edit-kegunaan');
    const rowKegunaanEdit = document.querySelector('#edit-row_kegunaan');

    function updateKategoriDisplayEdit(selectedValue) {
        if (selectedValue === 'MATERIAL') {
            subKategoriDivEdit.style.display = 'block';
            rowKegunaanDivEdit.style.display = 'block';

            kegunaanDivEdit.style.display = 'none';
            kegunaanEdit.value = '';
            kegunaanEdit.removeAttribute('required');

        } else {
            subKategoriDivEdit.style.display = 'none';
            subKategoriEdit.value = '';
            subKategoriEdit.removeAttribute('required');

            rowKegunaanDivEdit.style.display = 'none';
            rowKegunaanEdit.value = '';
            rowKegunaanEdit.removeAttribute('required');

            kegunaanDivEdit.style.display = 'block';
            kegunaanEdit.setAttribute('required', 'required');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tableData = $('#basic-datatables').DataTable();

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
        var waitingAllSelectRowInput = document.getElementById('waitingAllSelectRow');
        var approvedAllSelectRowInput = document.getElementById('approvedAllSelectRow');
        var editButton = document.getElementById('editButton');
        var deleteButton = document.getElementById('deleteButton');
        var pendingButton = document.getElementById('statusPendingButton');
        var waitingButton = document.getElementById('statusWaitingButton');
        var approvedButton = document.getElementById('statusApprovedButton');

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
                var selectedIds = [];
                var selectedNoForms = [];
                var selectedKategoris = [];

                table.rows({ search: 'applied' }).nodes().to$().find('.select-checkbox:checked').each(function() {
                    var $row = $(this).closest('tr');
                    selectedIds.push($row.data('id'));
                    selectedNoForms.push($row.data('noform'));
                    selectedKategoris.push($row.data('kategori'));
                });

                allSelectRowInput.value = selectedIds.join(',');
                allNoFormRow.value = selectedNoForms.join(',');
                allKategoriRow.value = selectedKategoris.join(',');

                var idsString = selectedIds.join(',');

                // Update each hidden input with the selected IDs
                deleteAllSelectRowInput.value = idsString;

                if (pendingAllSelectRowInput) {
                    pendingAllSelectRowInput.value = idsString;
                    waitingAllSelectRowInput.value = idsString;
                    approvedAllSelectRowInput.value = idsString;
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
                        waitingButton.style.display = 'inline-block';
                        approvedButton.style.display = 'inline-block';
                    }
                    deleteButton.classList.remove('ms-5');
                    deleteButton.classList.add('ms-3');
                } else if (selectedCheckboxes > 1) {
                    editButton.style.display = 'none';
                    deleteButton.style.display = 'inline-block';
                    if (pendingButton) {
                        pendingButton.style.display = 'inline-block';
                        waitingButton.style.display = 'inline-block';
                        approvedButton.style.display = 'inline-block';
                    }
                    deleteButton.classList.remove('ms-3');
                    deleteButton.classList.add('ms-5');
                } else {
                    editButton.style.display = 'none';
                    deleteButton.style.display = 'none';
                    if (pendingButton) {
                        pendingButton.style.display = 'none';
                        waitingButton.style.display = 'none';
                        approvedButton.style.display = 'none';
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

            if (waitingButton) {
                waitingButton.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You'll update the status to waiting!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, update it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            statusWaitingForm.submit();
                        }
                    });
                });
            }

            if (approvedButton) {
                approvedButton.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You'll update the status to approved!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, update it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            statusApprovedForm.submit();
                        }
                    });
                });
            }

            editButton.addEventListener('click', function () {
                var selectedId = allSelectRowInput.value.split(',')[0];
                if (selectedId) {
                    var row = $('tr[data-id="' + selectedId + '"]');
                    $('#edit-id').val(selectedId);
                    $('#edit-noform').val(row.data('noform'));
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-jabatan').val(row.data('jabatan'));
                    $('#edit-kategori').val(row.data('kategori'));
                    $('#edit-nama_kategori').val(row.data('kategori'));
                    $('#edit-tgl_order').val(row.data('tgl_order'));

                    if (row.data('kategori') == 'MATERIAL') {
                        $('#edit-row_kegunaan').val(row.data('kegunaan'));
                        $('#edit-sub_kategori').val(row.data('sub_kategori'));

                    } else {
                        $('#edit-kegunaan').val(row.data('kegunaan'));
                    }
                }

                updateKategoriDisplayEdit(row.data('kategori'));

                // Generate fields for barang data
                var barangData = null;
                if (row.data('kategori') == 'BESI' || (row.data('kategori') == 'MATERIAL')) {
                    barangData = @json($dataPembangunan->toArray());
                }

                if (row.data('kategori') == 'CAT' || (row.data('kategori') == 'SPAREPART')) {
                    barangData = @json($dataTagihanAMB->toArray());
                }

                if (barangData) {
                    var satuanData = @json($satuan->toArray());
                    var barang = barangData.filter(item => item.noform == row.data('noform'));
                    var dataBarang = document.getElementById('dataBarangEdit');
                    dataBarang.innerHTML = '';
                    barang.forEach(function(item, index) {
                        dataBarang.innerHTML += `
                            <input type="hidden" id="edit-id_barang" name="id_barang[]" value="${
                                (row.data('kategori') == 'BESI' || row.data('kategori') == 'MATERIAL') 
                                    ? item.id_pembangunan 
                                    : (row.data('kategori') == 'CAT' || row.data('kategori') == 'SPAREPART') 
                                        ? item.id_tagihan_amb 
                                        : ''
                            }">
                            <tr>
                                <td style="padding: 7.5px 2.5px !important;">
                                    <div class="text-center text-sm row-number">
                                        ${index + 1}.
                                    </div>
                                </td>
                                <td style="padding: 7.5px 2.5px !important;" width=8%>
                                    <input type="text" class="form-control text-center" name="jumlah[]" value="${
                                        (row.data('kategori') == 'BESI' || row.data('kategori') == 'MATERIAL') 
                                            ? item.jumlah 
                                            : (row.data('kategori') == 'CAT' || row.data('kategori') == 'SPAREPART') 
                                                ? item.jml 
                                                : ''
                                    }" 
                                    style="border: 0px; padding: .1rem 0.5rem;" 
                                    placeholder="..." required>
                                </td>
                                <td style="padding: 7.5px 2.5px !important;" width=18%>
                                    @if (count($satuan) > 0)
                                        <select class="form-select form-control" name="unit[]" id="unit" style="border: 0px;" required>
                                            <option class="text-center" value="">...</option>
                                            @foreach ($satuan as $s)
                                                <option class="text-center" value="{{ $s->id_satuan }}" ${item.id_satuan == '{{ $s->id_satuan }}' ? 'selected' : ''}>{{ $s->nama }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control text-center" name="unit" placeholder="Tidak ada data" 
                                        style="border: 0px; background-color: #fff !important;" disabled>
                                    @endif
                                </td>
                                <td style="padding: 7.5px 2.5px !important;">
                                    <input type="text" class="form-control" name="nama_barang[]" value="${item.nama}" oninput="this.value = this.value.toUpperCase()" 
                                    style="border: 0px; padding: .1rem 0.5rem;" placeholder="Masukkan nama barang.." required>
                                </td>
                                <td style="padding: 7.5px 2.5px !important;">
                                    <input type="text" class="form-control" name="ket[]" value="${
                                        (row.data('kategori') == 'BESI' || row.data('kategori') == 'MATERIAL') 
                                            ? item.deskripsi 
                                            : (row.data('kategori') == 'CAT' || row.data('kategori') == 'SPAREPART') 
                                                ? item.dipakai_untuk 
                                                : ''
                                    }" 
                                    placeholder="Masukkan keterangan.." style="border: 0px; padding: .1rem 0.5rem;" required>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
        }

        // Panggil fungsi saat kategori berubah
        kategoriSelect.addEventListener('change', function () {
            updateKategoriDisplay(this.value);
        });

    });

    document.getElementById('addRowButton').addEventListener('click', function () {
        const tableBody = document.getElementById('dataBarang');
        const rowCount = tableBody.rows.length + 1;

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
        <input type="hidden" name="id_permintaan_barang[]" value="">
        <td style="padding: 7.5px 2.5px !important;">
            <div class="text-center text-sm row-number">
                ${rowCount}.
            </div>
        </td>
        <td style="padding: 7.5px 2.5px !important;" width=8%>
            <input type="text" class="form-control text-center" name="jumlah[]" style="border: 0px; padding: .1rem 0.5rem;" 
            placeholder="..." required>
        </td>
        <td style="padding: 7.5px 2.5px !important;" width=18%>
            @if (count($satuan) > 0)
                <select class="form-select form-control" name="unit[]" id="unit" style="border: 0px;" required>
                    <option class="text-center" value="">...</option>
                    @foreach ($satuan as $s)
                        <option class="text-center" value="{{ $s->id_satuan }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control text-center" name="unit" placeholder="Tidak ada data" 
                style="border: 0px; background-color: #fff !important;" disabled>
            @endif
        </td>
        <td style="padding: 7.5px 2.5px !important;">
            <input type="text" class="form-control" name="nama_barang[]" oninput="this.value = this.value.toUpperCase()" 
            style="border: 0px; padding: .1rem 0.5rem;" placeholder="Masukkan nama barang.." required>
        </td>
        <td style="padding: 7.5px 2.5px !important;">
            <input type="text" class="form-control" name="ket[]" placeholder="Masukkan keterangan.." style="border: 0px; padding: .1rem 0.5rem;" required>
        </td>
        <td class="align-middle text-center">
            <a href="javascript:void(0);" onclick="confirmNewLineDelete(this)">
                <i class="fas fa-minus-square"></i>
            </a>
        </td>
        `;
        tableBody.appendChild(newRow);

        // Update row numbers for all rows
        updateRowNumbers();

        function updateRowNumbers() {
            const rows = document.querySelectorAll('#dataBarang tr');
            rows.forEach((row, index) => {
                const numberCell = row.querySelector('td:first-child div');
                if (numberCell) {
                    numberCell.innerText = `${index + 1}.`;
                }
            });
        }
    });

    function confirmNewLineDelete(element) {
        const row = element.closest('tr');
        const rowNumber = row.querySelector('.row-number').textContent.trim().replace('.', '');
        Swal.fire({
            title: 'Are you sure?',
            text: 'You sure you want to delete row number ' + rowNumber + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const row = element.closest('tr');
                row.remove();
                updateRowNumbers();
            }
        });
    }

    // Fungsi untuk memperbarui nomor baris
    function updateRowNumbers() {
        const rows = document.querySelectorAll('#dataBarang tr');
        rows.forEach((row, index) => {
            const numberCell = row.querySelector('.row-number');
            if (numberCell) {
                numberCell.textContent = `${index + 1}.`;
            }
        });
    }
</script>
@endsection