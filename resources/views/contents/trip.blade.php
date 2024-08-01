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

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" required />
                                </div>
                                <div class="col-8">
                                    <label for="kota">Kota Tujuan</label>
                                    <input type="text" class="form-control" name="kota" id="kota" placeholder="Masukkan kota tujuan.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="ket">Keterangan</label>
                                    <input type="text" class="form-control" name="ket" id="ket" placeholder="Masukkan keterangan.." required />
                                </div>
                                <div class="col-8">
                                    <label for="uraian">Uraian</label>
                                    <input type="text" class="form-control" name="uraian" id="uraian" placeholder="Masukkan uraian.." required />
                                </div>
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
                                    <input type="text" class="form-control" name="unit" id="unit" placeholder="Masukkan satuan.." required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="km_awal">KM Awal</label>
                                    <input type="number" class="form-control" name="km_awal" min="0" id="km_awal" placeholder="km awal.." required />
                                </div>
                                <div class="col-3">
                                    <label for="km_isi">KM Pengisian</label>
                                    <input type="number" class="form-control" name="km_isi" min="0" id="km_isi" placeholder="km pengisian.." required />
                                </div>
                                <div class="col-3">
                                    <label for="km_akhir">KM Akhir</label>
                                    <input type="number" class="form-control" name="km_akhir" min="0" id="km_akhir" placeholder="km akhir.." required />
                                </div>
                                <div class="col-3">
                                    <label for="km_ltr">KM/Liter</label>
                                    <input type="text" class="form-control" name="km_ltr" id="km_ltr" placeholder="km per liter.." style="background-color: #fff !important;" readonly />
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
                                    <input type="text" class="form-control" name="total" id="total" placeholder="Nilai total harga.." />
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

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Perjalanan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="edit-tanggal" required />
                                </div>
                                <div class="col-8">
                                    <label for="kota">Kota Tujuan</label>
                                    <input type="text" class="form-control" name="kota" id="edit-kota" placeholder="Masukkan kota tujuan.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="ket">Keterangan</label>
                                    <input type="text" class="form-control" name="ket" id="edit-ket" placeholder="Masukkan keterangan.." required />
                                </div>
                                <div class="col-8">
                                    <label for="uraian">Uraian</label>
                                    <input type="text" class="form-control" name="uraian" id="edit-uraian" placeholder="Masukkan uraian.." required />
                                </div>
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

                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="km_awal">KM Awal</label>
                                    <input type="number" class="form-control" name="km_awal" min="0" id="edit-km_awal" placeholder="km awal.." required />
                                </div>
                                <div class="col-3">
                                    <label for="km_isi">KM Pengisian</label>
                                    <input type="number" class="form-control" name="km_isi" min="0" id="edit-km_isi" placeholder="km pengisian.." required />
                                </div>
                                <div class="col-3">
                                    <label for="km_akhir">KM Akhir</label>
                                    <input type="number" class="form-control" name="km_akhir" min="0" id="edit-km_akhir" placeholder="km akhir.." required />
                                </div>
                                <div class="col-3">
                                    <label for="km_ltr">KM/Liter</label>
                                    <input type="text" class="form-control" name="km_ltr" id="edit-km_ltr" placeholder="km per liter.." />
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
                                    <input type="text" class="form-control" name="total" id="edit-total" placeholder="Nilai total harga.." style="background-color: #fff !important;" readonly />
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
                                    <label for="periode">Periode</label>
                                    <select class="form-control" name="periode" id="periode">
                                        <option value="">...</option>
                                        @foreach ($periodes as $periode)
                                            <option 
                                                value="{{ $periode }}">{{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}
                                            </option>
                                        @endforeach
                                    </select>
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
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#tripEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('trip-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="allSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
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
                                        <th class="text-xxs-bold">Keterangan</th>
                                        <!-- <th class="text-xxs-bold">Uraian</th> -->
                                        <th class="text-xxs-bold">Nopol / Kode Unit</th>
                                        <th class="text-xxs-bold">Merk</th>
                                        <th class="text-xxs-bold">Jumlah</th>
                                        <!-- <th class="text-xxs-bold">Satuan</th> -->
                                        <th class="text-xxs-bold">KM Awal</th>
                                        <th class="text-xxs-bold">KM Pengisian</th>
                                        <th class="text-xxs-bold">KM Akhir</th>
                                        <th class="text-xxs-bold">KM/Liter</th>
                                        <!-- <th class="text-xxs-bold">Harga</th> -->
                                        <th class="text-xxs-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trips as $t)
                                    <tr data-id="{{ $t->id_trip }}" 
                                        data-tanggal="{{ $t->tanggal }}" 
                                        data-kota="{{ $t->kota }}"
                                        data-ket="{{ $t->ket }}"
                                        data-uraian="{{ $t->uraian }}"
                                        data-kendaraan="{{ $t->id_kendaraan }}"
                                        data-merk="{{ $merkKendaraan[$t->id_kendaraan] ?? '-' }}"
                                        data-qty="{{ $t->qty }}"
                                        data-unit="{{ $t->unit }}"
                                        data-km_awal="{{ $t->km_awal }}"
                                        data-km_isi="{{ $t->km_isi }}"
                                        data-km_akhir="{{ $t->km_akhir }}"
                                        data-km_ltr="{{ $t->km_ltr }}"
                                        data-harga="{{ 'Rp ' . number_format($t->harga ?? 0, 0, ',', '.') }}"
                                        data-total="{{ 'Rp ' . number_format($t->total ?? 0, 0, ',', '.') }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $t->tanggal)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $t->kota ?? '-' }}</td>
                                        <!-- <td>{{ $t->ket ?? '-' }}</td> -->
                                        <td>{{ $t->uraian ?? '-' }}</td>
                                        <td>{{ $nopolKendaraan[$t->id_kendaraan] ?? '-' }}</td>
                                        <td>{{ $merkKendaraan[$t->id_kendaraan] ?? '-' }}</td>
                                        <td>{{ $t->qty ?? '-' }}</td>
                                        <!-- <td>{{ $t->unit ?? '-' }}</td> -->
                                        <td>{{ $t->km_awal ?? '-' }}</td>
                                        <td>{{ $t->km_isi ?? '-' }}</td>
                                        <td>{{ $t->km_akhir ?? '-' }}</td>
                                        <td>{{ $t->km_ltr ?? '-' }}</td>
                                        <!-- <td>{{ 'Rp ' . number_format($t->harga ?? 0, 0, ',', '.') }}</td> -->
                                        <td>{{ 'Rp ' . number_format($t->total ?? 0, 0, ',', '.') }}</td>
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

        if (!allData.checked && !custom.checked) {
            radioError.classList.remove('d-none');
            return false;
        }

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

        let qtyElements = document.querySelectorAll("#qty, #edit-qty");
        let kmIsiElements = document.querySelectorAll("#km_isi, #edit-km_isi");
        let kmAkhirElements = document.querySelectorAll("#km_akhir, #edit-km_akhir");
        let kmLiterElements = document.querySelectorAll("#km_ltr, #edit-km_ltr");
        let hargaTrip = document.querySelectorAll("#harga, #edit-harga");
        let totalElements = document.querySelectorAll("#total, #edit-total");
    
        // Function to calculate km/liter
        function calculateKmLiter() {
            qtyElements.forEach((qty, index) => {
                let kmIsi = kmIsiElements[index];
                let kmAkhir = kmAkhirElements[index];
                let kmLiter = kmLiterElements[index];
                let harga = hargaTrip[index];
                let qtyValue = parseFloat(qty.value.replace(',', '.'));
    
                if (qtyValue && kmIsi.value && kmAkhir.value) {
                    let valueKmLiter = (kmAkhir.value - kmIsi.value) / qtyValue;
                    kmLiter.value = parseFloat(valueKmLiter.toFixed(3));
                }

                if (harga.value && qtyValue) {
                    let totalValue = (harga.value.replace(/[^0-9]/g, "")) * qtyValue;
                    totalElements[index].value = formatCurrency(totalValue);
                }
            });
        }
    
        [...qtyElements, ...kmIsiElements, ...kmAkhirElements].forEach(element => {
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

        // Checkbox
        var table = document.getElementById('basic-datatables');
        var checkboxes;
        var selectAllCheckbox = document.getElementById('selectAllCheckbox');
        var allSelectRowInput = document.getElementById('allSelectRow');
        var editButton = document.getElementById('editButton');
        var deleteButton = document.getElementById('deleteButton');

        if (table) {
            checkboxes = table.getElementsByClassName('select-checkbox');

            // Event listener untuk checkbox "Select All"
            selectAllCheckbox.addEventListener('change', function () {
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                    var row = checkboxes[i].parentNode.parentNode;
                    row.classList.toggle('selected', this.checked);
                }

                // Update button visibility
                updateButtonVisibility();

                // Ambil dan simpan ID semua baris yang terpilih ke dalam input hidden
                updateAllSelectRow();
            });

            // Event listener untuk checkbox di setiap baris
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].addEventListener('change', function () {
                    var row = this.parentNode.parentNode;
                    row.classList.toggle('selected', this.checked);

                    // Periksa apakah setidaknya satu checkbox terpilih
                    var atLeastOneChecked = Array.from(checkboxes).some(function (checkbox) {
                        return checkbox.checked;
                    });

                    // Update button visibility
                    updateButtonVisibility();

                    // Periksa apakah semua checkbox terpilih
                    var allChecked = true;
                    for (var j = 0; j < checkboxes.length; j++) {
                        if (!checkboxes[j].checked) {
                            allChecked = false;
                            break;
                        }
                    }

                    // Atur status checkbox "Select All"
                    selectAllCheckbox.checked = allChecked;

                    // Ambil dan simpan ID semua baris yang terpilih ke dalam input hidden
                    updateAllSelectRow();
                });
            }

            // Fungsi untuk mengambil dan menyimpan ID semua baris yang terpilih
            function updateAllSelectRow() {
                var selectedIds = Array.from(checkboxes)
                    .filter(function (checkbox) {
                        return checkbox.checked;
                    })
                    .map(function (checkbox) {
                        return checkbox.closest('tr').getAttribute('data-id');
                    });

                allSelectRowInput.value = selectedIds.join(',');
            }

            // Fungsi untuk mengatur visibilitas tombol
            function updateButtonVisibility() {
                var selectedCheckboxes = Array.from(checkboxes).filter(function (checkbox) {
                    return checkbox.checked;
                }).length;

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
                    $('#edit-tanggal').val(row.data('tanggal'));
                    $('#edit-kota').val(row.data('kota'));
                    $('#edit-ket').val(row.data('ket'));
                    $('#edit-uraian').val(row.data('uraian'));
                    $('#edit-kendaraan').val(row.data('kendaraan'));
                    $('#edit-merk').val(row.data('merk'));
                    $('#edit-qty').val(row.data('qty'));
                    $('#edit-unit').val(row.data('unit'));
                    $('#edit-km_awal').val(row.data('km_awal'));
                    $('#edit-km_isi').val(row.data('km_isi'));
                    $('#edit-km_akhir').val(row.data('km_akhir'));
                    $('#edit-km_ltr').val(row.data('km_ltr'));
                    $('#edit-harga').val(row.data('harga'));
                    $('#edit-total').val(row.data('total'));
                }
            });
        }

    });
</script>
@endsection