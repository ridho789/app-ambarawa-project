@extends('layouts.base')
<!-- @section('title', 'Barang Keluar') -->
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

    .modal-xxl {
        max-width: 70%;
    }
</style>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Data Barang Keluar</h3>
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
                    <a href="#">Barang Keluar</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="barangKeluarModal" tabindex="-1" role="dialog" aria-labelledby="barangKeluarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xxl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="barangKeluarModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Barang Keluar </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('barang_keluar-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="user" name="user" value="{{ auth()->user()->name }}">

                            <div class="form-group">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <td class="text-center fw-mediumbold">No.</td>
                                            <td class="text-center fw-mediumbold">Tanggal <br> Pengambilan</td>
                                            <td class="text-center fw-mediumbold">Pengguna</td>
                                            <td class="text-center fw-mediumbold">Barang</td>
                                            <td colspan="2" class="text-center fw-mediumbold">Jumlah <br> Barang Keluar</td>
                                            <td class="text-center fw-mediumbold">Sisa Stok</td>
                                            <td class="text-center fw-mediumbold">Kendaraan</td>
                                            <td class="text-center fw-mediumbold">Lokasi</td>
                                            <td class="text-center fw-mediumbold">Keterangan</td>
                                            <td width=5%></td>
                                        </tr>
                                    </thead>
                                    <tbody id="dataBarangKeluar">
                                        <tr>
                                            <td style="padding: 7.5px 2.5px !important;">
                                                <div class="text-center text-sm">
                                                    1.
                                                </div>
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;">
                                                <input type="date" class="form-control text-center" name="tanggal_keluar[]" 
                                                style="border: 0px; padding: .1rem 0.5rem;" required>
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;" width=12.5%>
                                                <input type="text" class="form-control text-center" name="pengguna[]" placeholder="..." 
                                                oninput="this.value = this.value.toUpperCase()" style="border: 0px; padding: .1rem 0.5rem;" required>
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;" width=20%>
                                                @if (count($stok) > 0)
                                                    <select class="form-select form-control" name="id_stok_barang[]" id="stok" style="border: 0px;" required>
                                                        <option class="text-center" value="">...</option>
                                                        @foreach ($stok as $s)
                                                            <option class="text-center" value="{{ $s->id_stok_barang }}" data-jumlah="{{ $s->jumlah }}" data-satuan="{{ $s->id_satuan }}"
                                                            @if ($s->jumlah == 0) disabled @endif>
                                                            {{ $s->nama }}
                                                            @if (!empty($s->merk) && $s->merk !== '-') {{ $s->merk }} @endif
                                                            @if (!empty($s->type) && $s->type !== '-') {{ $s->type }} @endif
                                                            @if (!empty($s->keterangan) && $s->keterangan !== '-') 
                                                                @if (empty($s->merk) || $s->merk === '-') 
                                                                    @if (empty($s->type) || $s->type === '-') 
                                                                        {{ $s->keterangan }} 
                                                                    @endif
                                                                @endif
                                                            @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="text" class="form-control text-center" placeholder="Tidak ada data" 
                                                    style="border: 0px; background-color: #fff !important;" disabled>
                                                @endif
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;">
                                                <input type="text" class="form-control text-center" id="jumlah" name="jumlah[]" placeholder="..." 
                                                style="border: 0px; padding: .1rem 0.5rem;" required>
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;" width=12.5%>
                                                <input type="text" class="form-control text-center" id="satuan"
                                                style="border: 0px; padding: .1rem 0.5rem; background-color: #fff !important;" placeholder="..." readonly>
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;">
                                                <input type="text" class="form-control text-center" id="stok_tersisa" placeholder="..." 
                                                style="border: 0px; padding: .1rem 0.5rem; background-color: #fff !important;" readonly>
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;" width=17.5%>
                                                @if (count($kendaraan) > 0)
                                                    <select class="form-select form-control" name="kendaraan[]" style="border: 0px;" id="kendaraan">
                                                        <option class="text-center" value="">...</option>
                                                        @foreach ($kendaraan as $k)
                                                            <option class="text-center" value="{{ $k->id_kendaraan }}">{{ $k->nopol }} {{ $k->merk }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="text" class="form-control text-center" placeholder="Tidak ada data" 
                                                    style="border: 0px; background-color: #fff !important;" disabled>
                                                @endif
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;" width=17.5%>
                                                <input type="text" class="form-control text-center" name="lokasi[]" id="lokasi" 
                                                oninput="this.value = this.value.toUpperCase()" style="border: 0px; padding: .1rem 0.5rem;" placeholder="...">
                                            </td>
                                            <td style="padding: 7.5px 2.5px !important;" width=22.5%>
                                                <input type="text" class="form-control" name="ket[]" placeholder="..." 
                                                style="border: 0px; padding: .1rem 0.5rem;" required>
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

        <!-- Modal Edit data -->
        <div class="modal fade" id="barangKeluarEditModal" tabindex="-1" role="dialog" aria-labelledby="barangKeluarEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="barangKeluarEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Barang Keluar </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('barang_keluar-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_barang_keluar">
                            <input type="hidden" id="edit-user" name="user" value="{{ auth()->user()->name }}">

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="tanggal_keluar">Tanggal Pengembalian</label>
                                    <input type="date" class="form-control" name="tanggal_keluar" id="edit-tanggal_keluar" required />
                                </div>
                                <div class="col-6">
                                    <label for="pengguna">Pengguna</label>
                                    <input type="text" class="form-control" name="pengguna" id="edit-pengguna" placeholder="Masukkan pengguna.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Barang</span>
                            </div>

                            <div class="form-group">
                                @if (count($stok) > 0)
                                    <label for="barang">Nama Barang</label>
                                    <select class="form-select form-control" name="barang" id="edit-barang" disabled>
                                        <option value="">...</option>
                                        @foreach ($stok as $s)
                                            <option value="{{ $s->id_stok_barang }}" data-merk="{{ $s->merk }}">
                                            {{ $s->nama }}
                                            @if (!empty($s->merk) && $s->merk !== '-') {{ $s->merk }} @endif
                                            @if (!empty($s->type) && $s->type !== '-') {{ $s->type }} @endif
                                            @if (!empty($s->keterangan) && $s->keterangan !== '-') 
                                                @if (empty($s->merk) || $s->merk === '-') 
                                                    @if (empty($s->type) || $s->type === '-') 
                                                        {{ $s->keterangan }} 
                                                    @endif
                                                @endif
                                            @endif
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <label>Nama Barang</label>
                                    <select class="form-control" disabled>
                                        <option value="">Tidak ada data</option>
                                    </select>
                                @endif
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="jumlah">Jumlah yang diambil</label>
                                    <input type="text" class="form-control" name="ket" id="edit-jumlah" placeholder="Masukkan jumlah.." readonly />
                                </div>
                                <div class="col-6">
                                    <label for="sisa_stok">Sisa Stok</label>
                                    <input type="text" class="form-control" name="ket" id="edit-sisa_stok" placeholder="Sisa stok.." readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kendaraan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    @if (count($kendaraan) > 0)
                                        <label for="kendaraan">Nopol / Kode Unit</label>
                                        <select class="form-select form-control" name="kendaraan" id="edit-kendaraan" onchange="updateEditMerkKendaraan()">
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
                                    <input type="text" class="form-control" name="merk" id="edit-merk_kendaraan" placeholder="Masukkan merk.." 
                                    oninput="this.value = this.value.toUpperCase()" style="background-color: #fff !important;" readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="lokasi">Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" id="edit-lokasi" placeholder="Masukkan lokasi.."
                                 oninput="this.value = this.value.toUpperCase()" required />
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <input type="text" class="form-control" name="ket" id="edit-keterangan" placeholder="Masukkan keterangan.." required />
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
        <div class="modal fade" id="barangKeluarExportModal" tabindex="-1" role="dialog" aria-labelledby="barangKeluarExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="barangKeluarExportModal">
                            <span class="fw-light"> Export Data</span>
                            <span class="fw-mediumbold"> Barang Keluar </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('barang_keluar-export') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
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

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                            <div class="card-title">List Barang Keluar</div>
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#barangKeluarEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('barang_keluar-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="allSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            <div class="ms-auto d-flex align-items-center">
                            @if (count($barangKeluar) > 0)
                                <button class="btn btn-success btn-round ms-2 btn-sm" data-bs-toggle="modal" data-bs-target="#barangKeluarExportModal">
                                    <i class="fa fa-file-excel"></i>
                                    Export data
                                </button>
                            @endif
                            <button class="btn btn-primary btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#barangKeluarModal">
                                <i class="fa fa-plus"></i>
                                Tambah data
                            </button>
                            </div>
                        </div>
                    </div>
                    @if (count($barangKeluar) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th width=5%>
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">Tanggal <br> Pengambilan</th>
                                        <th class="text-xxs-bold">Pengguna <br> / Karyawan</th>
                                        <th class="text-xxs-bold">Barang</th>
                                        <th class="text-xxs-bold">Jumlah <br> yang diambil</th>
                                        <!-- <th class="text-xxs-bold">Sisa Stok</th> -->
                                        <th class="text-xxs-bold">Satuan</th>
                                        <th class="text-xxs-bold">Kendaraan</th>
                                        <th class="text-xxs-bold">Lokasi</th>
                                        <th class="text-xxs-bold">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangKeluar as $bk)
                                    <tr data-id="{{ $bk->id_barang_keluar }}" 
                                        data-stok_barang="{{ $bk->id_stok_barang }}"
                                        data-merk_barang="{{ $merkStokBarang[$bk->id_stok_barang] ?? '-' }}"
                                        data-kendaraan="{{ $bk->id_kendaraan }}"
                                        data-merk_kendaraan="{{ $merkKendaraan[$bk->id_kendaraan] ?? '-' }}"
                                        data-tanggal_keluar="{{ $bk->tanggal_keluar }}"
                                        data-pengguna="{{ $bk->pengguna }}"
                                        data-jumlah="{{ $bk->jumlah }}"
                                        data-sisa_stok="{{ $sisaStokBarang[$bk->id_stok_barang] ?? '-' }}"
                                        data-lokasi="{{ $bk->lokasi }}"
                                        data-keterangan="{{ $bk->ket }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $bk->tanggal_keluar)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $bk->pengguna ?? '-' }}</td>
                                        @if ($bk->id_stok_barang)
                                            @php
                                                $nama = $namaStokBarang[$bk->id_stok_barang] ?? '';
                                                $merk = $merkStokBarang[$bk->id_stok_barang] ?? '';
                                                $type = $typeStokBarang[$bk->id_stok_barang] ?? '';
                                                $keterangan = $ketStokBarang[$bk->id_stok_barang] ?? '';
                                            @endphp
                                            @if ((!empty($merk) && $merk !== '-') || (!empty($type) && $type !== '-'))
                                                <td>{{ $nama }} 
                                                    @if (!empty($merk) && $merk !== '-') 
                                                        {{ $merk }} 
                                                    @endif
                                                    @if (!empty($type) && $type !== '-') 
                                                        {{ $type }} 
                                                    @endif
                                                </td>
                                            @elseif (!empty($keterangan) && $keterangan !== '-')
                                                <td>{{ $nama }} {{ $keterangan }}</td>
                                            @else
                                                <td>{{ $nama }}</td>
                                            @endif
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{ $bk->jumlah ?? '-' }}</td>
                                        <!-- <td>{{ $bk->sisa_stok ?? '-' }}</td> -->
                                        <td>{{ $namaSatuan[$satuanStokBarang[$bk->id_stok_barang]] ?? '-' }}</td>
                                        @if ($bk->id_kendaraan)
                                            <td>{{ $nopolKendaraan[$bk->id_kendaraan] }} {{ $merkKendaraan[$bk->id_kendaraan] }}</td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{ $bk->lokasi ?? '-' }}</td>
                                        <td>{{ $bk->ket ?? '-' }}</td>
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
    function previewImage(event) {
        var image = document.getElementById('image-preview');
        image.src = URL.createObjectURL(event.target.files[0]);
        image.style.display = 'block';
    }

    function updateEditMerkKendaraan() {
        var kendaraanEditSelect = document.getElementById('edit-kendaraan');
        var selectedEditOption = kendaraanEditSelect.options[kendaraanEditSelect.selectedIndex];
        var merk = selectedEditOption.getAttribute('data-merk');
        
        var merkEditInput = document.getElementById('edit-merk_kendaraan');
        merkEditInput.value = merk ? merk.toUpperCase() : '';
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

        radioError.classList.add('d-none');
        dateError.classList.add('d-none');
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const satuanData = @json($satuan);

        document.querySelectorAll('select[name="id_stok_barang[]"]').forEach(function (selectElement) {
            selectElement.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const satuanId = selectedOption.getAttribute('data-satuan');
                let jumlahStok = selectedOption.getAttribute('data-jumlah');

                // Find the satuan name based on the satuanId
                const satuanName = satuanData.find(satuan => satuan.id_satuan === parseInt(satuanId))?.nama;

                // Find the associated inputs in the same row
                const currentRow = this.closest('tr');
                const satuanInput = currentRow.querySelector('input#satuan');
                const sisaStokInput = currentRow.querySelector('input#stok_tersisa');
                const jumlahInput = currentRow.querySelector('input[name="jumlah[]"]');

                // Update the satuan field with the retrieved name
                satuanInput.value = satuanName || '...';

                // Update the sisa stok field with the selected stock amount
                sisaStokInput.value = jumlahStok || '...';

                // Reset the jumlah field when the barang is changed
                jumlahInput.value = '';

                // Remove any previous event listener to prevent duplication
                jumlahInput.removeEventListener('input', jumlahInput._listener);

                // Create a new event listener for the jumlah input
                const jumlahInputListener = function () {
                    const inputJumlah = parseFloat(this.value);
                    const availableStok = parseFloat(jumlahStok);  // Get the latest jumlahStok from the current selection

                    if (inputJumlah) {
                        if (inputJumlah > availableStok) {
                            // If the inputted jumlah exceeds the available stock, show an alert and reset the field
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Jumlah yang di input tidak bisa melebihi sisa stok.',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            });
                            this.value = '';
                            sisaStokInput.value = jumlahStok;
                        } else {
                            // Otherwise, update the sisa stok
                            const sisaStok = availableStok - inputJumlah;
                            sisaStokInput.value = sisaStok;
                        }
                    } else {
                        // Reset sisa stok if input is empty
                        sisaStokInput.value = jumlahStok;
                    }
                };

                // Assign the new listener to a custom property to allow removal later
                jumlahInput._listener = jumlahInputListener;

                // Add the event listener for the jumlah input
                jumlahInput.addEventListener('input', jumlahInputListener);
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
        var editButton = document.getElementById('editButton');
        var deleteButton = document.getElementById('deleteButton');

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

                allSelectRowInput.value = selectedIds.join(',');
            }

            // Fungsi untuk mengatur visibilitas tombol
            function updateButtonVisibility() {
                var selectedCheckboxes = table.rows({ search: 'applied' }).nodes().to$().find('.select-checkbox:checked').length;

                if (selectedCheckboxes === 1) {
                    editButton.style.display = 'inline-block';
                    deleteButton.style.display = 'inline-block';
                    deleteButton.classList.remove('ms-5');
                    deleteButton.classList.add('ms-3');
                } else if (selectedCheckboxes > 1) {
                    editButton.style.display = 'none';
                    deleteButton.style.display = 'inline-block';
                    deleteButton.classList.remove('ms-3');
                    deleteButton.classList.add('ms-5');
                } else {
                    editButton.style.display = 'none';
                    deleteButton.style.display = 'none';
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

            editButton.addEventListener('click', function () {
                var selectedId = allSelectRowInput.value.split(',')[0];
                if (selectedId) {
                    var row = $('tr[data-id="' + selectedId + '"]');
                    
                    $('#edit-id').val(selectedId);
                    $('#edit-tanggal_keluar').val(row.data('tanggal_keluar'));
                    $('#edit-pengguna').val(row.data('pengguna'));
                    $('#edit-barang').val(row.data('stok_barang'));
                    $('#edit-jumlah').val(row.data('jumlah'));
                    $('#edit-sisa_stok').val(row.data('sisa_stok'));
                    $('#edit-merk_barang').val(row.data('merk_barang'));
                    $('#edit-kendaraan').val(row.data('kendaraan'));
                    $('#edit-merk_kendaraan').val(row.data('merk_kendaraan'));
                    $('#edit-lokasi').val(row.data('lokasi'));
                    $('#edit-keterangan').val(row.data('keterangan'));
                }
            });
        }

    });

    document.getElementById('addRowButton').addEventListener('click', function () {
        const tableBody = document.getElementById('dataBarangKeluar');
        const rowCount = tableBody.rows.length + 1;

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
        <td style="padding: 7.5px 2.5px !important;">
            <div class="text-center text-sm row-number">
                ${rowCount}.
            </div>
        </td>
        <td style="padding: 7.5px 2.5px !important;">
            <input type="date" class="form-control text-center" name="tanggal_keluar[]" 
            style="border: 0px; padding: .1rem 0.5rem;" required>
        </td>
        <td style="padding: 7.5px 2.5px !important;" width=12.5%>
            <input type="text" class="form-control text-center" name="pengguna[]" placeholder="..." 
            oninput="this.value = this.value.toUpperCase()" style="border: 0px; padding: .1rem 0.5rem;" required>
        </td>
        <td style="padding: 7.5px 2.5px !important;" width=20%>
            @if (count($stok) > 0)
                <select class="form-select form-control" name="id_stok_barang[]" id="stok" style="border: 0px;" required>
                    <option class="text-center" value="">...</option>
                    @foreach ($stok as $s)
                        <option class="text-center" value="{{ $s->id_stok_barang }}" data-jumlah="{{ $s->jumlah }}" data-satuan="{{ $s->id_satuan }}">
                        {{ $s->nama }} {{ $s->merk }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control text-center" placeholder="Tidak ada data" 
                style="border: 0px; background-color: #fff !important;" disabled>
            @endif
        </td>
        <td style="padding: 7.5px 2.5px !important;">
            <input type="text" class="form-control text-center" id="jumlah" name="jumlah[]" placeholder="..." 
            style="border: 0px; padding: .1rem 0.5rem;" required>
        </td>
        <td style="padding: 7.5px 2.5px !important;" width=12.5%>
            <input type="text" class="form-control text-center" id="satuan"
            style="border: 0px; padding: .1rem 0.5rem; background-color: #fff !important;" placeholder="..." readonly>
        </td>
        <td style="padding: 7.5px 2.5px !important;">
            <input type="text" class="form-control text-center" id="stok_tersisa" placeholder="..." 
            style="border: 0px; padding: .1rem 0.5rem; background-color: #fff !important;" readonly>
        </td>
        <td style="padding: 7.5px 2.5px !important;" width=17.5%>
            @if (count($kendaraan) > 0)
                <select class="form-select form-control" name="kendaraan[]" style="border: 0px;" id="kendaraan">
                    <option class="text-center" value="">...</option>
                    @foreach ($kendaraan as $k)
                        <option class="text-center" value="{{ $k->id_kendaraan }}">{{ $k->nopol }} {{ $k->merk }}</option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control text-center" placeholder="Tidak ada data" 
                style="border: 0px; background-color: #fff !important;" disabled>
            @endif
        </td>
        <td style="padding: 7.5px 2.5px !important;" width=17.5%>
            <input type="text" class="form-control text-center" name="lokasi" id="lokasi" 
            oninput="this.value = this.value.toUpperCase()" style="border: 0px; padding: .1rem 0.5rem;" placeholder="...">
        </td>
        <td style="padding: 7.5px 2.5px !important;" width=22.5%>
            <input type="text" class="form-control" name="ket[]" placeholder="..." 
            style="border: 0px; padding: .1rem 0.5rem;" required>
        </td>
        <td>
            <a href="javascript:void(0);" onclick="confirmNewLineDelete(this)">
                <i class="fas fa-minus-square"></i>
            </a>
        </td>
        `;
        tableBody.appendChild(newRow);

        // Update row numbers for all rows
        updateRowNumbers();

        function updateRowNumbers() {
            const rows = document.querySelectorAll('#dataBarangKeluar tr');
            rows.forEach((row, index) => {
                const numberCell = row.querySelector('td:first-child div');
                if (numberCell) {
                    numberCell.innerText = `${index + 1}.`;
                }
            });
        }

        const satuanData = @json($satuan);
        document.querySelectorAll('select[name="id_stok_barang[]"]').forEach(function (selectElement) {
            selectElement.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const satuanId = selectedOption.getAttribute('data-satuan');
                let jumlahStok = selectedOption.getAttribute('data-jumlah');

                // Find the satuan name based on the satuanId
                const satuanName = satuanData.find(satuan => satuan.id_satuan === parseInt(satuanId))?.nama;

                // Find the associated inputs in the same row
                const currentRow = this.closest('tr');
                const satuanInput = currentRow.querySelector('input#satuan');
                const sisaStokInput = currentRow.querySelector('input#stok_tersisa');
                const jumlahInput = currentRow.querySelector('input[name="jumlah[]"]');

                // Update the satuan field with the retrieved name
                satuanInput.value = satuanName || '...';

                // Update the sisa stok field with the selected stock amount
                sisaStokInput.value = jumlahStok || '...';

                // Reset the jumlah field when the barang is changed
                jumlahInput.value = '';

                // Remove any previous event listener to prevent duplication
                jumlahInput.removeEventListener('input', jumlahInput._listener);

                // Create a new event listener for the jumlah input
                const jumlahInputListener = function () {
                    const inputJumlah = parseFloat(this.value);
                    const availableStok = parseFloat(jumlahStok);  // Get the latest jumlahStok from the current selection

                    if (inputJumlah) {
                        if (inputJumlah > availableStok) {
                            // If the inputted jumlah exceeds the available stock, show an alert and reset the field
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Jumlah yang di input tidak bisa melebihi sisa stok.',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            });
                            this.value = '';
                            sisaStokInput.value = jumlahStok;
                        } else {
                            // Otherwise, update the sisa stok
                            const sisaStok = availableStok - inputJumlah;
                            sisaStokInput.value = sisaStok;
                        }
                    } else {
                        // Reset sisa stok if input is empty
                        sisaStokInput.value = jumlahStok;
                    }
                };

                // Assign the new listener to a custom property to allow removal later
                jumlahInput._listener = jumlahInputListener;

                // Add the event listener for the jumlah input
                jumlahInput.addEventListener('input', jumlahInputListener);
            });
        });
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
        const rows = document.querySelectorAll('#dataBarangKeluar tr');
        rows.forEach((row, index) => {
            const numberCell = row.querySelector('.row-number');
            if (numberCell) {
                numberCell.textContent = `${index + 1}.`;
            }
        });
    }
</script>
@endsection