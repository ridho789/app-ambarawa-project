@extends('layouts.base')
<!-- @section('title', 'Poles Kaca Mobil') -->
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
            <h3 class="fw-bold mb-3">Data Pengeluaran Poles Kaca Mobil</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ url('/') }}">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Poles Kaca Mobil</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="polesModal" tabindex="-1" role="dialog" aria-labelledby="polesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="polesModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Poles Kaca Mobil Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('poles-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <div class="form-group">
                                <label for="lokasi">Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" id="lokasi" placeholder="Masukkan lokasi.." required />
                            </div>

                            <!-- <div class="form-group">
                                <span class="h5">Informasi Kendaraan</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="nopol">Nopol</label>
                                    <input type="text" class="form-control" name="nopol" id="nopol" placeholder="Masukkan nopol.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                                <div class="col-4">
                                    <label for="kode_unit">Kode Unit</label>
                                    <input type="text" class="form-control" name="kode_unit" id="kode_unit" placeholder="Masukkan kode unit.." required />
                                </div>
                                <div class="col-4">
                                    <label for="merk">Merk</label>
                                    <input type="text" class="form-control" name="merk" id="merk" placeholder="Masukkan merk.." required />
                                </div>
                            </div> -->

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Pemesanan</span>
                            </div>

                            <div class="form-group">
                                <label for="pemesan">Pemesan / Dipesan oleh</label>
                                <input type="text" class="form-control" name="pemesan" id="pemesan" placeholder="Masukkan nama pemesan.." required />
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
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama.." required />
                                </div>
                                <div class="col-4">
                                    <label for="kategori">Kategori</label>
                                    <select class="form-select form-control" id="kategori" name="kategori" required>
                                        <option value="">...</option>
                                        <option value="aset">Aset</option>
                                        <option value="stok">Stok</option>
                                        <option value="langsung">Langsung</option>
                                        <option value="jasa">Jasa</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="masa_pakai">Masa Pakai</label>
                                    <input type="text" class="form-control" name="masa_pakai" id="masa_pakai" placeholder="Masa pakai.." required />
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
                                    <label for="unit">Satuan</label>
                                    <input type="text" class="form-control" name="unit" id="unit" placeholder="Satuan.." required />
                                </div>
                                <div class="col-6">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="harga" placeholder="Masukkan harga.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="total" placeholder="Nilai total harga.." style="background-color: #fff !important;" readonly />
                            </div>

                            <div class="form-group">
                                <label for="toko">Toko</label>
                                <input type="text" class="form-control" name="toko" id="toko" placeholder="Masukkan toko.." required />
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
        <div class="modal fade" id="polesEditModal" tabindex="-1" role="dialog" aria-labelledby="polesEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="polesEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Poles Kaca Mobil </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('poles-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_tagihan_amb">

                            <div class="form-group">
                                <label for="lokasi">Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" id="edit-lokasi" placeholder="Masukkan lokasi.." required />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Pemesanan</span>
                            </div>

                            <div class="form-group">
                                <label for="pemesan">Pemesan / Dipesan oleh</label>
                                <input type="text" class="form-control" name="pemesan" id="edit-pemesan" placeholder="Masukkan nama pemesan.." required />
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
                                    <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama.." required />
                                </div>
                                <div class="col-4">
                                    <label for="kategori">Kategori</label>
                                    <select class="form-select form-control" id="edit-kategori" name="kategori" required>
                                        <option value="">...</option>
                                        <option value="aset">Aset</option>
                                        <option value="stok">Stok</option>
                                        <option value="langsung">Langsung</option>
                                        <option value="jasa">Jasa</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="masa_pakai">Masa Pakai</label>
                                    <input type="text" class="form-control" name="masa_pakai" id="edit-masa_pakai" placeholder="Masa pakai.." required />
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
                                    <input type="number" class="form-control" name="jml" min="1" id="edit-jml" placeholder="Jumlah.." required />
                                </div>
                                <div class="col-3">
                                    <label for="unit">Satuan</label>
                                    <input type="text" class="form-control" name="unit" id="edit-unit" placeholder="Satuan.." required />
                                </div>
                                <div class="col-6">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="edit-harga" placeholder="Masukkan harga.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="edit-total" placeholder="Nilai total harga.." 
                                style="background-color: #fff !important;" readonly />
                            </div>

                            <div class="form-group">
                                <label for="toko">Toko</label>
                                <input type="text" class="form-control" name="toko" id="edit-toko" placeholder="Masukkan toko.." required />
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
                            <div class="card-title">List Pengeluaran Poles Kaca Mobil</div>
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#polesEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('poles-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="allSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            <button class="btn btn-primary btn-round ms-auto btn-sm" data-bs-toggle="modal" data-bs-target="#polesModal">
                                <i class="fa fa-plus"></i>
                                Tambah data
                            </button>
                        </div>
                    </div>
                    @if (count($poles) > 0)
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
                                        <!-- <th class="text-xxs-bold">Qty</th>
                                        <th class="text-xxs-bold">Unit</th> -->
                                        <th class="text-xxs-bold">Harga</th>
                                        <th class="text-xxs-bold">Total</th>
                                        <th class="text-xxs-bold">Toko</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($poles as $p)
                                    <tr data-id="{{ $p->id_tagihan_amb }}" 
                                        data-lokasi="{{ $p->lokasi }}" 
                                        data-pemesan="{{ $p->pemesan }}"
                                        data-tgl_order="{{ $p->tgl_order }}"
                                        data-tgl_invoice="{{ $p->tgl_invoice }}"
                                        data-no_inventaris="{{ $p->no_inventaris }}"
                                        data-nama="{{ $p->nama }}"
                                        data-kategori="{{ $p->kategori }}"
                                        data-masa_pakai="{{ $p->masa_pakai }}"
                                        data-dipakai_untuk="{{ $p->dipakai_untuk }}"
                                        data-jml="{{ $p->jml }}"
                                        data-unit="{{ $p->unit }}"
                                        data-harga="{{ 'Rp ' . number_format($p->harga ?? 0, 0, ',', '.') }}"
                                        data-total="{{ 'Rp ' . number_format($p->total ?? 0, 0, ',', '.') }}"
                                        data-toko="{{ $p->toko }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $p->lokasi ?? '-' }}</td>
                                        <!-- <td>{{ $p->nopol ?? '-' }}</td>
                                        <td>{{ $p->kode_unit ?? '-' }}</td>
                                        <td>{{ $p->merk ?? '-' }}</td> -->
                                        <td>{{ $p->pemesan ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $p->tgl_order)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $p->tgl_invoice)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $p->no_inventaris ?? '-' }}</td>
                                        <td>{{ $p->nama ?? '-' }}</td>
                                        <td>{{ $p->kategori ?? '-' }}</td>
                                        <!-- <td>{{ $p->dipakai_untuk ?? '-' }}</td> -->
                                        <td>{{ $p->masa_pakai ?? '-' }}</td>
                                        <!-- <td>{{ $p->jml ?? '-' }}</td>
                                        <td>{{ $p->unit ?? '-' }}</td> -->
                                        <td>{{ 'Rp ' . number_format($p->harga ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ 'Rp ' . number_format($p->total ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $p->toko }}</td>
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
            if (row.querySelector('td:nth-child(12)')) {
                const totalText = row.querySelector('td:nth-child(12)').innerText;
                const totalValue = parseInt(totalText.replace(/[^0-9,-]+/g, ""));
                totalSum += totalValue;
            }
        });
        if (document.getElementById('total-sum')) {
            document.getElementById('total-sum').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalSum);
        }
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
                    $('#edit-lokasi').val(row.data('lokasi'));
                    $('#edit-pemesan').val(row.data('pemesan'));
                    $('#edit-tgl_order').val(row.data('tgl_order'));
                    $('#edit-tgl_invoice').val(row.data('tgl_invoice'));
                    $('#edit-no_inventaris').val(row.data('no_inventaris'));
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-kategori').val(row.data('kategori'));
                    $('#edit-masa_pakai').val(row.data('masa_pakai'));
                    $('#edit-dipakai_untuk').val(row.data('dipakai_untuk'));
                    $('#edit-jml').val(row.data('jml'));
                    $('#edit-unit').val(row.data('unit'));
                    $('#edit-harga').val(row.data('harga'));
                    $('#edit-total').val(row.data('total'));
                    $('#edit-toko').val(row.data('toko'));
                }
            });
        }
    });
</script>
@endsection