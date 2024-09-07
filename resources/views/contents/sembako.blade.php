@extends('layouts.base')
<!-- @section('title', 'Sembako') -->
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
            <h3 class="fw-bold mb-3">Data Pengeluaran Sembako</h3>
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
                    <a href="#">Sembako</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="sembakoModal" tabindex="-1" role="dialog" aria-labelledby="sembakoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="sembakoModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Sembako Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('sembako-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" required />
                                </div>
                                <div class="col-6">
                                    <label for="nama">Nama (Barang)</label>
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="qty">Jumlah</label>
                                    <input type="text" class="form-control" name="qty" id="qty" placeholder="Masukkan jumlah.." required />
                                </div>
                                <div class="col-4">
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
                                <div class="col-4">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="harga" placeholder="Masukkan harga.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="total" placeholder="Nilai total harga.." required />
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
        <div class="modal fade" id="sembakoEditModal" tabindex="-1" role="dialog" aria-labelledby="sembakoEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="sembakoEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Sembako </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('sembako-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_sembako">

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="edit-tanggal" required />
                                </div>
                                <div class="col-6">
                                    <label for="nama">Nama (Barang)</label>
                                    <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="qty">Jumlah</label>
                                    <input type="text" class="form-control" name="qty" id="edit-qty" placeholder="Masukkan jumlah.." required />
                                </div>
                                <div class="col-4">
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
                                <div class="col-4">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="edit-harga" placeholder="Masukkan harga.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="edit-total" placeholder="Nilai total harga.." required />
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
        <div class="modal fade" id="sembakoExportModal" tabindex="-1" role="dialog" aria-labelledby="sembakoExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="sembakoExportModal">
                            <span class="fw-light"> Export Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Sembako </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('sembako-export') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
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

        <!-- Modal Import data -->
        <div class="modal fade" id="sembakoImportModal" tabindex="-1" role="dialog" aria-labelledby="sembakoImportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="sembakoImportModal">
                            <span class="fw-light"> Import Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Sembako </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('sembako-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Pilih file dengan format .xlsx
                            </p>
                            <div class="form-group">
                                <label for="file">Upload file</label>
                                <input type="file" name="file" accept=".xlsx">
                            </div>
                        </div>
                        <div class="modal-footer border-0 mx-2">
                            <button type="submit" class="btn btn-primary btn-sm">Import</button>
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
                            <div class="card-title">List Pengeluaran Sembako</div>
                            <input type="hidden" id="allSelectRow" name="ids" value="">
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#sembakoEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('sembako-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="deleteAllSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            @if (Auth::user()->level == 0)
                            <form id="statusPendingForm" method="POST" action="{{ url('sembako-status_pending') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="pendingAllSelectRow" name="ids" value="">
                                <button id="statusPendingButton" type="button" class="btn btn-warning btn-round ms-5 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Pending
                                </button>
                            </form>
                            <form id="statusProcessForm" method="POST" action="{{ url('sembako-status_process') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="processAllSelectRow" name="ids" value="">
                                <button id="statusProcessButton" type="button" class="btn btn-black btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Processing
                                </button>
                            </form>
                            <form id="statusPaidForm" method="POST" action="{{ url('sembako-status_paid') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="paidAllSelectRow" name="ids" value="">
                                <button id="statusPaidButton" type="button" class="btn btn-success btn-round ms-3 btn-sm" style="display: none;">
                                    <!-- <i class="fa fa-trash"></i> -->
                                    Set to Paid
                                </button>
                            </form>
                            @endif
                            <div class="ms-auto d-flex align-items-center">
                                @if (count($sembako) > 0)
                                    <button class="btn btn-success btn-round ms-2 btn-sm" data-bs-toggle="modal" data-bs-target="#sembakoExportModal">
                                        <i class="fa fa-file-excel"></i>
                                        Export data
                                    </button>
                                @endif
                                <button class="btn btn-black btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#sembakoImportModal">
                                    <i class="fa fa-file-excel"></i>
                                    Import data
                                </button>
                                <button class="btn btn-primary btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#sembakoModal">
                                    <i class="fa fa-plus"></i>
                                    Tambah data
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (count($sembako) > 0)
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
                                        <th class="text-xxs-bold">Nama (Barang)</th>
                                        <th class="text-xxs-bold">Jumlah</th>
                                        <th class="text-xxs-bold">Satuan</th>
                                        <th class="text-xxs-bold">Harga</th>
                                        <th class="text-xxs-bold">Total</th>
                                        <th class="text-xxs-bold">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sembako as $s)
                                    <tr data-id="{{ $s->id_sembako }}" 
                                        data-tanggal="{{ $s->tanggal }}" 
                                        data-nama="{{ $s->nama }}"
                                        data-qty="{{ $s->qty }}"
                                        data-unit="{{ $s->id_satuan }}"
                                        data-harga="{{ 'Rp ' . number_format((float) ($s->harga ?? 0), 0, ',', '.') }}"
                                        data-total="{{ 'Rp ' . number_format((float) ($s->total ?? 0), 0, ',', '.') }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $s->tanggal)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $s->nama ?? '-' }}</td>
                                        <td>{{ $s->qty ?? '-' }}</td>
                                        <td>{{ $namaSatuan[$s->id_satuan] ?? '-' }}</td>
                                        <td>{{ 'Rp ' . number_format((float) ($s->harga ?? 0), 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format((float) ($s->total ?? 0), 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($s->status) {
                                                    'pending' => 'status-pending',
                                                    'processing' => 'status-process',
                                                    default => 'status-paid',
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ ucfirst($s->status) }}</span>
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
            if (row.querySelector('td:nth-child(8)')) {
                const totalText = row.querySelector('td:nth-child(8)').innerText;
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

        radioError.classList.add('d-none');
        dateError.classList.add('d-none');
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tableData = $('#basic-datatables').DataTable();
        tableData.on('draw.dt', function() {
            calculateTotal();
        });

        calculateTotal();

        function parseFraction(fraction) {
            let parts = fraction.split('/');
            if (parts.length === 2) {
                return parseFloat(parts[0]) / parseFloat(parts[1]);
            } else {
                return parseFloat(fraction);
            }
        }

        // Ambil elemen harga, qty, dan total
        let hargaSembako = document.querySelectorAll("#harga");
        let jmlElements = document.querySelectorAll("#qty");
        let totalElements = document.querySelectorAll("#total");

        // Event listener untuk perubahan pada jumlah (qty)
        jmlElements.forEach(function(jml, index) {
            jml.addEventListener("input", function() {
                let harga = hargaSembako[index];
                let total = totalElements[index];
                let jmlValue = parseFraction(jml.value.replace(',', '.'));

                if (harga) {
                    let hargaValue = parseInt(harga.value.replace(/[^0-9]/g, ""), 10) || 0;
                    let totalValue = hargaValue * jmlValue;
                    total.value = formatCurrency(totalValue);
                }
            });
        });

        // Event listener untuk perubahan pada harga
        hargaSembako.forEach(function(hargaSm, index) {
            hargaSm.addEventListener("input", function() {
                let jml = jmlElements[index];
                let total = totalElements[index];
                
                // Format input harga dengan mata uang
                let hargaValueFormatted = this.value.replace(/[^0-9]/g, "") || 0;
                this.value = formatCurrency(hargaValueFormatted);

                let jmlValue = parseFraction(jml.value.replace(',', '.'));
                if (jmlValue) {
                    let hargaValue = parseInt(hargaValueFormatted, 10) || 0;
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

        // Ambil elemen harga, qty, dan total (Modal Edit)
        let hargaEditSembako = document.querySelectorAll("#edit-harga");
        let jmlEditElements = document.querySelectorAll("#edit-qty");
        let totalEditElements = document.querySelectorAll("#edit-total");

        // Event listener untuk perubahan pada jumlah (qty)
        jmlEditElements.forEach(function(jml, index) {
            jml.addEventListener("input", function() {
                let harga = hargaEditSembako[index];
                let total = totalEditElements[index];
                let jmlValue = parseFraction(jml.value.replace(',', '.'));

                if (harga) {
                    let hargaValue = parseInt(harga.value.replace(/[^0-9]/g, ""), 10) || 0;
                    let totalValue = hargaValue * jmlValue;
                    total.value = formatCurrency(totalValue);
                }
            });
        });

        // Event listener untuk perubahan pada harga
        hargaEditSembako.forEach(function(hargaSm, index) {
            hargaSm.addEventListener("input", function() {
                let jml = jmlEditElements[index];
                let total = totalEditElements[index];
                
                // Format input harga dengan mata uang
                let hargaValueFormatted = this.value.replace(/[^0-9]/g, "") || 0;
                this.value = formatCurrency(hargaValueFormatted);

                let jmlValue = parseFraction(jml.value.replace(',', '.'));
                if (jmlValue) {
                    let hargaValue = parseInt(hargaValueFormatted, 10) || 0;
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
                    
                    $('#edit-id').val(selectedId);
                    $('#edit-tanggal').val(row.data('tanggal'));
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-qty').val(row.data('qty'));
                    $('#edit-unit').val(row.data('unit'));
                    $('#edit-harga').val(row.data('harga'));
                    $('#edit-total').val(row.data('total'));
                }
            });
        }

    });
</script>
@endsection