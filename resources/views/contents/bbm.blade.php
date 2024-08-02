@extends('layouts.base')
<!-- @section('title', 'BBM') -->
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
            <h3 class="fw-bold mb-3">Data Pengeluaran BBM</h3>
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
                    <a href="#">BBM</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="bbmModal" tabindex="-1" role="dialog" aria-labelledby="bbmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="bbmModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran BBM Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('bbm-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="nama">Nama</label>
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                                <div class="col-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kendaraan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    @if (count($kendaraan) > 0)
                                        <label for="kendaraan">Pilih Kendaraan (Nopol / Kode Unit)</label>
                                        <select class="form-select form-control" name="kendaraan" id="kendaraan" onchange="updateMerk()">
                                            <option value="">...</option>
                                            @foreach ($kendaraan as $k)
                                                <option value="{{ $k->id_kendaraan }}" data-merk="{{ $k->merk }}">{{ $k->nopol }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <label>Pilih Kendaraan (Nopol / Kode Unit)</label>
                                        <select class="form-control" disabled>
                                            <option value="">Tidak ada data</option>
                                        </select>
                                    @endif
                                </div>
                                <div class="col-4">
                                    <label for="jns_mobil">Jenis Mobil</label>
                                    <input type="text" class="form-control" name="jns_mobil" id="jns_mobil" placeholder="Jenis mobil.." 
                                    oninput="this.value = this.value.toUpperCase()" style="background-color: #fff !important;" readonly />
                                </div>
                                <div class="col-4">
                                    <label for="jns_bbm">Jenis BBM</label>
                                    <input type="text" class="form-control" name="jns_bbm" id="jns_bbm" placeholder="Jenis bbm.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kilometer/Liter</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="liter">Liter</label>
                                    <input type="text" class="form-control" name="liter" id="liter" placeholder="liter.." required />
                                </div>
                                <div class="col-2">
                                    <label for="km_awal">KM Awal</label>
                                    <input type="number" class="form-control" name="km_awal" min="0" id="km_awal" placeholder="awal.." required />
                                </div>
                                <div class="col-2">
                                    <label for="km_isi">KM Pengisian</label>
                                    <input type="number" class="form-control" name="km_isi" min="0" id="km_isi" placeholder="isi.." required />
                                </div>
                                <div class="col-2">
                                    <label for="km_akhir">KM Akhir</label>
                                    <input type="number" class="form-control" name="km_akhir" min="0" id="km_akhir" placeholder="akhir.." required />
                                </div>
                                <div class="col-3">
                                    <label for="km_ltr">KM/Liter</label>
                                    <input type="text" class="form-control" name="km_ltr" id="km_ltr" placeholder="km per liter.." style="background-color: #fff !important;" readonly />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="harga" placeholder="Masukkan harga.." required />
                                </div>
                                <div class="col-4">
                                    <label for="tot_harga">Total Harga</label>
                                    <input type="text" class="form-control" name="tot_harga" id="tot_harga" placeholder="Masukkan total harga.." required />
                                </div>
                                <div class="col-4">
                                    <label for="tot_km">Total KM</label>
                                    <input type="text" class="form-control" name="tot_km" id="tot_km" placeholder="Nilai Total KM.." style="background-color: #fff !important;" readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="ket">Keterangan</label>
                                <input type="text" class="form-control" name="ket" id="ket" placeholder="Masukkan keterangan.." required />
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
        <div class="modal fade" id="bbmEditModal" tabindex="-1" role="dialog" aria-labelledby="bbmEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="bbmEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran BBM </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('bbm-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_bbm">

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="nama">Nama</label>
                                    <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                                <div class="col-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="edit-tanggal" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kendaraan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    @if (count($kendaraan) > 0)
                                        <label for="kendaraan">Pilih Kendaraan (Nopol / Kode Unit)</label>
                                        <select class="form-select form-control" name="kendaraan" id="edit-kendaraan" onchange="updateEditMerk()">
                                            <option value="">...</option>
                                            @foreach ($kendaraan as $k)
                                                <option value="{{ $k->id_kendaraan }}" data-merk="{{ $k->merk }}">{{ $k->nopol }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <label>Pilih Kendaraan (Nopol / Kode Unit)</label>
                                        <select class="form-control" disabled>
                                            <option value="">Tidak ada data</option>
                                        </select>
                                    @endif
                                </div>
                                <div class="col-4">
                                    <label for="jns_mobil">Jenis Mobil</label>
                                    <input type="text" class="form-control" name="jns_mobil" id="edit-jns_mobil" placeholder="Jenis mobil.." 
                                    oninput="this.value = this.value.toUpperCase()" style="background-color: #fff !important;" readonly />
                                </div>
                                <div class="col-4">
                                    <label for="jns_bbm">Jenis BBM</label>
                                    <input type="text" class="form-control" name="jns_bbm" id="edit-jns_bbm" placeholder="Jenis bbm.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Kilometer/Liter</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="liter">Liter</label>
                                    <input type="text" class="form-control" name="liter" id="edit-liter" placeholder="liter.." required />
                                </div>
                                <div class="col-2">
                                    <label for="km_awal">KM Awal</label>
                                    <input type="number" class="form-control" name="km_awal" min="0" id="edit-km_awal" placeholder="awal.." required />
                                </div>
                                <div class="col-2">
                                    <label for="km_isi">KM Pengisian</label>
                                    <input type="number" class="form-control" name="km_isi" min="0" id="edit-km_isi" placeholder="isi.." required />
                                </div>
                                <div class="col-2">
                                    <label for="km_akhir">KM Akhir</label>
                                    <input type="number" class="form-control" name="km_akhir" min="0" id="edit-km_akhir" placeholder="akhir.." required />
                                </div>
                                <div class="col-3">
                                    <label for="km_ltr">KM/Liter</label>
                                    <input type="text" class="form-control" name="km_ltr" id="edit-km_ltr" placeholder="km per liter.." style="background-color: #fff !important;" readonly />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="edit-harga" placeholder="Masukkan harga.." required />
                                </div>
                                <div class="col-4">
                                    <label for="tot_harga">Total Harga</label>
                                    <input type="text" class="form-control" name="tot_harga" id="edit-tot_harga" placeholder="Masukkan total harga.." required />
                                </div>
                                <div class="col-4">
                                    <label for="tot_km">Total KM</label>
                                    <input type="text" class="form-control" name="tot_km" id="edit-tot_km" placeholder="Nilai Total KM.." style="background-color: #fff !important;" readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="ket">Keterangan</label>
                                <input type="text" class="form-control" name="ket" id="edit-ket" placeholder="Masukkan keterangan.." required />
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
        <div class="modal fade" id="bbmExportModal" tabindex="-1" role="dialog" aria-labelledby="bbmExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="bbmExportModal">
                            <span class="fw-light"> Export Data</span>
                            <span class="fw-mediumbold"> Pengeluaran BBM </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('bbm-export') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
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
                            <div class="card-title">List Pengeluaran BBM</div>
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#bbmEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('bbm-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="allSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            <div class="ms-auto d-flex align-items-center">
                                @if (count($bbm) > 0)
                                    <button class="btn btn-success btn-round ms-2 btn-sm" data-bs-toggle="modal" data-bs-target="#bbmExportModal">
                                        <i class="fa fa-file-excel"></i>
                                        Export data
                                    </button>
                                @endif
                                <button class="btn btn-primary btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#bbmModal">
                                    <i class="fa fa-plus"></i>
                                    Tambah data
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (count($bbm) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th width=5%>
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">Nama</th>
                                        <th class="text-xxs-bold">Tanggal</th>
                                        <th class="text-xxs-bold">Nopol / Kode Unit</th>
                                        <th class="text-xxs-bold">Jenis Mobil</th>
                                        <!-- <th class="text-xxs-bold">Jenis BBM</th> -->
                                        <th class="text-xxs-bold">Liter</th>
                                        <th class="text-xxs-bold">KM Awal</th>
                                        <th class="text-xxs-bold">KM Pengisian</th>
                                        <th class="text-xxs-bold">KM Akhir</th>
                                        <th class="text-xxs-bold">KM/Liter</th>
                                        <th class="text-xxs-bold">Total KM</th>
                                        <!-- <th class="text-xxs-bold">Harga</th> -->
                                        <th class="text-xxs-bold">Total Harga</th>
                                        <!-- <th class="text-xxs-bold">Keterangan</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bbm as $b)
                                    <tr data-id="{{ $b->id_bbm }}"
                                        data-nama="{{ $b->nama }}"
                                        data-tanggal="{{ $b->tanggal }}" 
                                        data-kendaraan="{{ $b->id_kendaraan }}"
                                        data-merk="{{ $merkKendaraan[$b->id_kendaraan] ?? '-' }}"
                                        data-jns_bbm="{{ $b->jns_bbm }}"
                                        data-liter="{{ $b->liter }}"
                                        data-km_awal="{{ $b->km_awal }}"
                                        data-km_isi="{{ $b->km_isi }}"
                                        data-km_akhir="{{ $b->km_akhir }}"
                                        data-km_ltr="{{ $b->km_ltr }}"
                                        data-harga="{{ 'Rp ' . number_format($b->harga ?? 0, 0, ',', '.') }}"
                                        data-tot_harga="{{ 'Rp ' . number_format($b->tot_harga ?? 0, 0, ',', '.') }}"
                                        data-ket="{{ $b->ket }}"
                                        data-tot_km="{{ $b->tot_km }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $b->nama ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $b->tanggal)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $nopolKendaraan[$b->id_kendaraan] ?? '-' }}</td>
                                        <td>{{ $merkKendaraan[$b->id_kendaraan] ?? '-' }}</td>
                                        <!-- <td>{{ $b->jns_bbm ?? '-' }}</td> -->
                                        <td>{{ $b->liter ?? '-' }}</td>
                                        <td>{{ $b->km_awal ?? '-' }}</td>
                                        <td>{{ $b->km_isi ?? '-' }}</td>
                                        <td>{{ $b->km_akhir ?? '-' }}</td>
                                        <td>{{ $b->km_ltr ?? '-' }}</td>
                                        <td>{{ $b->tot_km ?? '-' }}</td>
                                        <!-- <td>{{ 'Rp ' . number_format($b->harga ?? 0, 0, ',', '.') }}</td> -->
                                        <td>{{ 'Rp ' . number_format($b->tot_harga ?? 0, 0, ',', '.') }}</td>
                                        <!-- <td>{{ $b->ket ?? '-' }}</td> -->
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
        
        var merkInput = document.getElementById('jns_mobil');
        merkInput.value = merk ? merk.toUpperCase() : '';
    }

    function updateEditMerk() {
        var kendaraanEditSelect = document.getElementById('edit-kendaraan');
        var selectedEditOption = kendaraanEditSelect.options[kendaraanEditSelect.selectedIndex];
        var merk = selectedEditOption.getAttribute('data-merk');
        
        var merkEditInput = document.getElementById('edit-jns_mobil');
        merkEditInput.value = merk ? merk.toUpperCase() : '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        var rows = document.querySelectorAll('#basic-datatables tbody tr');
        var previousRow = null;

        rows.forEach(function(row, index) {
            var currentIdKendaraan = row.getAttribute('data-kendaraan');
            var currentKmAwal = parseFloat(row.getAttribute('data-km_awal'));
            var currentKmAkhir = parseFloat(row.getAttribute('data-km_akhir'));
            
            if (previousRow) {
                var previousIdKendaraan = previousRow.getAttribute('data-kendaraan');
                var previousKmAkhir = parseFloat(previousRow.getAttribute('data-km_akhir'));
                
                if (currentIdKendaraan === previousIdKendaraan && currentKmAwal !== previousKmAkhir) {
                    row.querySelector('td:nth-child(8)').classList.add('text-danger');
                }
            }

            previousRow = row;
        });

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

        let literElements = document.querySelectorAll("#liter, #edit-liter");
        let kmAwalElements = document.querySelectorAll("#km_awal, #edit-km_awal");
        let kmIsiElements = document.querySelectorAll("#km_isi, #edit-km_isi");
        let kmAkhirElements = document.querySelectorAll("#km_akhir, #edit-km_akhir");
        let kmLiterElements = document.querySelectorAll("#km_ltr, #edit-km_ltr");
        let totKMElements = document.querySelectorAll("#tot_km, #edit-tot_km");
        let hargaBBM = document.querySelectorAll("#harga, #edit-harga");
        let totHargaBBM = document.querySelectorAll("#tot_harga, #edit-tot_harga");
    
        // Function to calculate km/liter
        function calculateKmLiter() {
            literElements.forEach((liter, index) => {
                let kmAwal = kmAwalElements[index];
                let kmIsi = kmIsiElements[index];
                let kmAkhir = kmAkhirElements[index];
                let kmLiter = kmLiterElements[index];
                let totKM = totKMElements[index];
                let harga = hargaBBM[index];
                let literValue = parseFraction(liter.value.replace(',', '.'));
    
                if (literValue && kmIsi.value && kmAkhir.value) {
                    let valueKmLiter = (kmAkhir.value - kmIsi.value) / literValue;
                    kmLiter.value = parseFloat(valueKmLiter.toFixed(3));
                }

                // Total KM
                if (kmAwal.value && kmAkhir.value) {
                    totKM.value = kmAkhir.value - kmAwal.value;
                }

                if (harga.value && literValue) {
                    let totalValue = (harga.value.replace(/[^0-9]/g, "")) * literValue;
                    totHargaBBM[index].value = formatCurrency(totalValue);
                }
            });
        }
    
        [...literElements, ...kmIsiElements, ...kmAkhirElements, ...kmAwalElements].forEach(element => {
            element.addEventListener("change", calculateKmLiter);
        });

        // Harga
        hargaBBM.forEach(function(hargaB, index) {
            hargaB.addEventListener("input", function() {
                this.value = formatCurrency(this.value);

                let literValue = parseFraction(literElements[index].value.replace(',', '.'));
                if (literValue) {
                    let totalValue = (this.value.replace(/[^0-9]/g, "")) * literValue;
                    totHargaBBM[index].value = formatCurrency(totalValue);
                }
            });
        });

        // Harga
        totHargaBBM.forEach(function(totHarga, index) {
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
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-tanggal').val(row.data('tanggal'));
                    $('#edit-kendaraan').val(row.data('kendaraan'));
                    $('#edit-jns_mobil').val(row.data('merk'));
                    $('#edit-jns_bbm').val(row.data('jns_bbm'));
                    $('#edit-liter').val(row.data('liter'));
                    $('#edit-km_awal').val(row.data('km_awal'));
                    $('#edit-km_isi').val(row.data('km_isi'));
                    $('#edit-km_akhir').val(row.data('km_akhir'));
                    $('#edit-km_ltr').val(row.data('km_ltr'));
                    $('#edit-harga').val(row.data('harga'));
                    $('#edit-tot_harga').val(row.data('tot_harga'));
                    $('#edit-ket').val(row.data('ket'));
                    $('#edit-tot_km').val(row.data('tot_km'));
                }
            });
        }

    });
</script>
@endsection