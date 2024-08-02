@extends('layouts.base')
<!-- @section('title', 'Sparepart') -->
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
            <h3 class="fw-bold mb-3">Data Pengeluaran Spareparts</h3>
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
                    <a href="#">Sparepart</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah Data -->
        <div class="modal fade" id="sparepartModal" tabindex="-1" role="dialog" aria-labelledby="sparepartModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="sparepartModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Sparepart Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('sparepartamb-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <div class="form-group">
                                <label for="lokasi">Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" id="lokasi" placeholder="Masukkan lokasi.." required />
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

                            <div class="form-group">
                                <label for="nama">Nama (Barang)</label>
                                <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama.." required />
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="kategori">Kategori</label>
                                    <select class="form-select form-control" id="kategori" name="kategori" required>
                                        <option value="">...</option>
                                        <option value="Aset">Aset</option>
                                        <option value="Stok">Stok</option>
                                        <option value="Langsung Pakai">Langsung Pakai</option>
                                        <option value="Jasa">Jasa</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="masa_pakai">Masa Pakai</label>
                                    <input type="text" class="form-control" name="masa_pakai" id="masa_pakai" placeholder="Masa pakai.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="dipakai_untuk">Dipakai untuk</label>
                                <input type="text" class="form-control" name="dipakai_untuk" id="dipakai_untuk" placeholder="Dipakai untuk.." required />
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

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div id="offlineFields" class="d-none">
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="jml_offline">Jumlah</label>
                                        <input type="text" class="form-control" name="jml" id="jml_offline" placeholder="Masukkan jumlah.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="unit_offline">Satuan</label>
                                        <input type="text" class="form-control" name="unit" id="unit_offline" placeholder="Masukkan satuan.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="harga">Harga</label>
                                        <input type="text" class="form-control" name="harga" id="harga" placeholder="Masukkan harga.." />
                                    </div>
                                </div>
                            </div>

                            <div id="onlineFields" class="d-none">
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="jml_online">Jumlah</label>
                                        <input type="text" class="form-control" name="jml_onl" id="jml_online" placeholder="Masukkan jumlah.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="unit_online">Satuan</label>
                                        <input type="text" class="form-control" name="unit_onl" id="unit_online" placeholder="Masukkan satuan.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="harga_online">Harga Online</label>
                                        <input type="text" class="form-control" name="harga_online" id="harga_online" placeholder="Harga online.." />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="ongkir">Ongkir</label>
                                        <input type="text" class="form-control" name="ongkir" id="ongkir" placeholder="Ongkir.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="diskon_ongkir">Diskon Ongkir</label>
                                        <input type="text" class="form-control" name="diskon_ongkir" id="diskon_ongkir" placeholder="Diskon ongkir.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="asuransi">Asuransi</label>
                                        <input type="text" class="form-control" name="asuransi" id="asuransi" placeholder="Asuransi.." />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-6">
                                        <label for="b_proteksi">Biaya Proteksi</label>
                                        <input type="text" class="form-control" name="b_proteksi" id="b_proteksi" placeholder="Biaya proteksi.." />
                                    </div>
                                    <div class="col-6">
                                        <label for="b_jasa_aplikasi">Biaya Jasa Aplikasi</label>
                                        <input type="text" class="form-control" name="b_jasa_aplikasi" id="b_jasa_aplikasi" placeholder="Biaya jasa aplikasi.." />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="total" placeholder="Nilai total harga.." required />
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
        <div class="modal fade" id="sparepartEditModal" tabindex="-1" role="dialog" aria-labelledby="sparepartEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="sparepartEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Sparepart </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('sparepartamb-update') }}" method="POST" enctype="multipart/form-data">
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

                            <div class="form-group">
                                <label for="nama">Nama (Barang)</label>
                                <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama.." required />
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="kategori">Kategori</label>
                                    <select class="form-select form-control" id="edit-kategori" name="kategori" required>
                                        <option value="">...</option>
                                        <option value="Aset">Aset</option>
                                        <option value="Stok">Stok</option>
                                        <option value="Langsung Pakai">Langsung Pakai</option>
                                        <option value="Jasa">Jasa</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="masa_pakai">Masa Pakai</label>
                                    <input type="text" class="form-control" name="masa_pakai" id="edit-masa_pakai" placeholder="Masa pakai.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="dipakai_untuk">Dipakai untuk</label>
                                <input type="text" class="form-control" name="dipakai_untuk" id="edit-dipakai_untuk" placeholder="Dipakai untuk.." required />
                            </div>

                            <div class="form-group">
                                <label>Metode Pembelian</label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check mr-3">
                                        <input class="form-check-input" type="radio" name="metode_pembelian" id="edit-is_online" value="online" onclick="toggleFieldsEdit()" />
                                        <label class="form-check-label" for="is_online">
                                            Online
                                        </label>
                                    </div>
                                    <div class="form-check mr-3">
                                        <input class="form-check-input" type="radio" name="metode_pembelian" id="edit-is_offline" value="offline" onclick="toggleFieldsEdit()" />
                                        <label class="form-check-label" for="is_offline">
                                            Offline
                                        </label>
                                    </div>
                                    <div>
                                        <span type="button" class="badge badge-black" style="margin-bottom: 8px;" onclick="resetFieldsEdit()">Reset</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div id="offlineFieldsEdit" class="d-none">
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="jml_offline">Jumlah</label>
                                        <input type="text" class="form-control" name="jml" id="edit-jml_offline" placeholder="Masukkan jumlah.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="unit_offline">Satuan</label>
                                        <input type="text" class="form-control" name="unit" id="edit-unit_offline" placeholder="Masukkan satuan.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="harga">Harga</label>
                                        <input type="text" class="form-control" name="harga" id="edit-harga" placeholder="Masukkan harga.." />
                                    </div>
                                </div>
                            </div>

                            <div id="onlineFieldsEdit" class="d-none">
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="jml_online">Jumlah</label>
                                        <input type="text" class="form-control" name="jml_onl" id="edit-jml_online" placeholder="Masukkan jumlah.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="unit_online">Satuan</label>
                                        <input type="text" class="form-control" name="unit_onl" id="edit-unit_online" placeholder="Masukkan satuan.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="harga_online">Harga Online</label>
                                        <input type="text" class="form-control" name="harga_online" id="edit-harga_online" placeholder="Harga online.." />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-4">
                                        <label for="ongkir">Ongkir</label>
                                        <input type="text" class="form-control" name="ongkir" id="edit-ongkir" placeholder="Ongkir.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="diskon_ongkir">Diskon Ongkir</label>
                                        <input type="text" class="form-control" name="diskon_ongkir" id="edit-diskon_ongkir" placeholder="Diskon ongkir.." />
                                    </div>
                                    <div class="col-4">
                                        <label for="asuransi">Asuransi</label>
                                        <input type="text" class="form-control" name="asuransi" id="edit-asuransi" placeholder="Asuransi.." />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-6">
                                        <label for="b_proteksi">Biaya Proteksi</label>
                                        <input type="text" class="form-control" name="b_proteksi" id="edit-b_proteksi" placeholder="Biaya proteksi.." />
                                    </div>
                                    <div class="col-6">
                                        <label for="b_jasa_aplikasi">Biaya Jasa Aplikasi</label>
                                        <input type="text" class="form-control" name="b_jasa_aplikasi" id="edit-b_jasa_aplikasi" placeholder="Biaya jasa aplikasi.." />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="edit-total" placeholder="Nilai total harga.." required />
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

        <!-- Modal Export data -->
        <div class="modal fade" id="sparepartExportModal" tabindex="-1" role="dialog" aria-labelledby="sparepartExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="sparepartExportModal">
                            <span class="fw-light"> Export Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Sparepart </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('sparepartamb-export') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Pengaturan untuk menentukan format data pengeluaran sesuai keinginan
                            </p>

                            <div class="form-group">
                                <label>Metode Pembelian</label>
                                <select class="form-select form-control" id="metode_pembelian" name="metode_pembelian" required>
                                    <option value="">...</option>
                                    @if (count($sparepartOnline) > 0)
                                        <option value="online">Online</option>
                                    @endif
                                    @if (count($sparepartOffline) > 0)
                                        <option value="offline">Offline</option>
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

        <!-- Rangkuman data pengeluaran berdasarkan tgl order dan toko -->
        @if (count($sparepartambgroup) > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="card-title">List Rangkuman Pengeluaran Spareparts</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatablesgroup" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">Periode</th>
                                        <th class="text-xxs-bold">Toko</th>
                                        <th class="text-xxs-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sparepartambgroup as $s)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $s->month_name }} {{ $s->year }}</td>
                                        <td>{{ $s->lokasi }}</td>
                                        <td>{{ 'Rp ' . number_format($s->total_sum  ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="card-title">List Pengeluaran Spareparts</div>
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#sparepartEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('sparepartamb-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="allSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            <div class="ms-auto d-flex align-items-center">
                                @if (count($sparepartamb) > 0)
                                    <button class="btn btn-success btn-round ms-2 btn-sm" data-bs-toggle="modal" data-bs-target="#sparepartExportModal">
                                        <i class="fa fa-file-excel"></i>
                                        Export data
                                    </button>
                                @endif
                                <button class="btn btn-primary btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#sparepartModal">
                                    <i class="fa fa-plus"></i>
                                    Tambah data
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (count($sparepartamb) > 0)
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
                                        <th class="text-xxs-bold">Nopol / Kode Unit</th>
                                        <th class="text-xxs-bold">Merk</th>
                                        <th class="text-xxs-bold">Pemesan</th>
                                        <th class="text-xxs-bold">Tgl. Order</th>
                                        <th class="text-xxs-bold">Tgl. Invoice</th>
                                        <th class="text-xxs-bold">No. Inventaris</th>
                                        <th class="text-xxs-bold">Nama (Barang)</th>
                                        <th class="text-xxs-bold">Kategori</th>
                                        <!-- <th class="text-xxs-bold">Keperluan</th> -->
                                        <!-- <th class="text-xxs-bold">Masa Pakai</th> -->
                                        <!-- <th class="text-xxs-bold">Qty</th> -->
                                        <!-- <th class="text-xxs-bold">Unit</th> -->
                                        <!-- <th class="text-xxs-bold">Harga</th> -->
                                        <th class="text-xxs-bold">Total</th>
                                        <th class="text-xxs-bold">Toko</th>
                                        <th class="text-xxs-bold">Via</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sparepartamb as $s)
                                    <tr data-id="{{ $s->id_tagihan_amb }}" 
                                        data-lokasi="{{ $s->lokasi }}" 
                                        data-kendaraan="{{ $s->id_kendaraan }}"
                                        data-merk="{{ $merkKendaraan[$s->id_kendaraan] ?? '-' }}"
                                        data-pemesan="{{ $s->pemesan }}"
                                        data-tgl_order="{{ $s->tgl_order }}"
                                        data-tgl_invoice="{{ $s->tgl_invoice }}"
                                        data-no_inventaris="{{ $s->no_inventaris }}"
                                        data-nama="{{ $s->nama }}"
                                        data-kategori="{{ $s->kategori }}"
                                        data-dipakai_untuk="{{ $s->dipakai_untuk }}"
                                        data-masa_pakai="{{ $s->masa_pakai }}"
                                        data-jml="{{ $s->jml }}"
                                        data-unit="{{ $s->unit }}"
                                        data-harga="{{ 'Rp ' . number_format($s->harga ?? 0, 0, ',', '.') }}"
                                        data-harga_online="{{ 'Rp ' . number_format($s->harga_online ?? 0, 0, ',', '.') }}"
                                        data-diskon_ongkir="{{ 'Rp ' . number_format($s->diskon_ongkir ?? 0, 0, ',', '.') }}"
                                        data-ongkir="{{ 'Rp ' . number_format($s->ongkir ?? 0, 0, ',', '.') }}"
                                        data-asuransi="{{ 'Rp ' . number_format($s->asuransi ?? 0, 0, ',', '.') }}"
                                        data-b_proteksi="{{ 'Rp ' . number_format($s->b_proteksi ?? 0, 0, ',', '.') }}"
                                        data-b_jasa_aplikasi="{{ 'Rp ' . number_format($s->b_jasa_aplikasi ?? 0, 0, ',', '.') }}"
                                        data-total="{{ 'Rp ' . number_format($s->total ?? 0, 0, ',', '.') }}"
                                        data-toko="{{ $s->toko }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $s->lokasi ?? '-' }}</td>
                                        <td>{{ $nopolKendaraan[$s->id_kendaraan] ?? '-' }}</td>
                                        <td>{{ $merkKendaraan[$s->id_kendaraan] ?? '-' }}</td>
                                        <td>{{ $s->pemesan ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $s->tgl_order)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $s->tgl_invoice)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $s->no_inventaris ?? '-' }}</td>
                                        <td>{{ $s->nama ?? '-' }}</td>
                                        <td>{{ $s->kategori ?? '-' }}</td>
                                        <!-- <td>{{ $s->dipakai_untuk ?? '-' }}</td> -->
                                        <!-- <td>{{ $s->masa_pakai ?? '-' }}</td> -->
                                        <!-- <td>{{ $s->jml ?? '-' }}</td> -->
                                        <!-- <td>{{ $s->unit ?? '-' }}</td> -->
                                        <!-- <td>{{ 'Rp ' . number_format($s->harga ?? 0, 0, ',', '.') }}</td> -->
                                        <td>{{ 'Rp ' . number_format($s->total ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $s->toko }}</td>
                                        <td>
                                            @if ($s->harga_online)
                                                Online
                                            @else
                                                Offline
                                            @endif
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

    function toggleFields() {
        const isOnlineChecked = document.getElementById('is_online').checked;
        const offlineFields = document.getElementById('offlineFields');
        const onlineFields = document.getElementById('onlineFields');

        if (isOnlineChecked) {
            setFieldsRequired(onlineFields, true);
            setFieldsRequired(offlineFields, false);
            offlineFields.classList.add('d-none');
            onlineFields.classList.remove('d-none');

        } else {
            setFieldsRequired(onlineFields, false);
            setFieldsRequired(offlineFields, true);
            offlineFields.classList.remove('d-none');
            onlineFields.classList.add('d-none');
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

    function resetFields() {
        document.getElementById('is_online').checked = false;
        document.getElementById('is_offline').checked = false;
        setFieldsRequired(document.getElementById('offlineFields'), false);
        setFieldsRequired(document.getElementById('onlineFields'), false);
        document.getElementById('offlineFields').classList.add('d-none');
        document.getElementById('onlineFields').classList.add('d-none');
    }

    // Modal Edit
    function toggleFieldsEdit() {
        const isOnlineCheckedEdit = document.getElementById('edit-is_online').checked;
        const offlineFieldsEdit = document.getElementById('offlineFieldsEdit');
        const onlineFieldsEdit = document.getElementById('onlineFieldsEdit');

        if (isOnlineCheckedEdit) {
            setFieldsRequired(onlineFieldsEdit, true);
            setFieldsRequired(offlineFieldsEdit, false);
            offlineFieldsEdit.classList.add('d-none');
            onlineFieldsEdit.classList.remove('d-none');

        } else {
            setFieldsRequired(onlineFieldsEdit, false);
            setFieldsRequired(offlineFieldsEdit, true);
            offlineFieldsEdit.classList.remove('d-none');
            onlineFieldsEdit.classList.add('d-none');
        }
    }

    function resetFieldsEdit() {
        document.getElementById('edit-is_online').checked = false;
        document.getElementById('edit-is_offline').checked = false;
        setFieldsRequired(document.getElementById('offlineFieldsEdit'), false);
        setFieldsRequired(document.getElementById('onlineFieldsEdit'), false);
        document.getElementById('offlineFieldsEdit').classList.add('d-none');
        document.getElementById('onlineFieldsEdit').classList.add('d-none');
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

    function toggleCustomFields() {
        const isCustomChecked = document.getElementById('custom').checked;
        const customFields = document.getElementById('customFields');

        if (isCustomChecked) {
            setSelectFieldsRequired(customFields, true);
            customFields.classList.remove('d-none');

        } else {
            setSelectFieldsRequired(customFields, false);
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
        $('#basic-datatablesgroup').DataTable();
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

        let jmlOnlineElements = document.querySelectorAll("#jml_online, #edit-jml_online");
        let jmlOfflineElements = document.querySelectorAll("#jml_offline, #edit-jml_offline");
        let hargaTokoElements = document.querySelectorAll("#harga, #edit-harga");
        let hargaOnlElements = document.querySelectorAll("#harga_online, #edit-harga_online");
        let diskonOngkirElements = document.querySelectorAll("#diskon_ongkir, #edit-diskon_ongkir");
        let ongkirElements = document.querySelectorAll("#ongkir, #edit-ongkir");
        let asuransiElements = document.querySelectorAll("#asuransi, #edit-asuransi");
        let proteksiElements = document.querySelectorAll("#b_proteksi, #edit-b_proteksi");
        let aplikasiElements = document.querySelectorAll("#b_jasa_aplikasi, #edit-b_jasa_aplikasi");
        let totalElements = document.querySelectorAll("#total, #edit-total");

        // Calculate price in method online
        if (document.getElementById('is_online').checked == false || document.getElementById('edit-is_online').checked == false) {
            function updateTotal(index) {
                let hargaOnl = hargaOnlElements[index];
                let jmlOnl = jmlOnlineElements[index];
                let diskonOngkir = diskonOngkirElements[index];
                let ongkir = ongkirElements[index];
                let asuransi = asuransiElements[index];
                let proteksi = proteksiElements[index];
                let aplikasi = aplikasiElements[index];
                let total = totalElements[index];
                
                let jmlOnlValue = parseFraction(jmlOnl.value.replace(',', '.'));
                let hargaOnlValue = parseInt(hargaOnl.value.replace(/[^0-9]/g, ""), 10) || 0;
                let ongkirlValue = parseInt(ongkir.value.replace(/[^0-9]/g, ""), 10) || 0;
                let asuransiValue = parseInt(asuransi.value.replace(/[^0-9]/g, ""), 10) || 0;
                let proteksiValue = parseInt(proteksi.value.replace(/[^0-9]/g, ""), 10) || 0;
                let aplikasiValue = parseInt(aplikasi.value.replace(/[^0-9]/g, ""), 10) || 0;
                let diskonOngkirValue = parseInt(diskonOngkir.value.replace(/[^0-9]/g, ""), 10) || 0;

                let totalValue = ((hargaOnlValue * jmlOnlValue) - (diskonOngkirValue)) + (ongkirlValue + asuransiValue + proteksiValue + aplikasiValue);
                total.value = formatCurrency(totalValue);
            }

            function formatInput(element) {
                let valueFormatted = element.value.replace(/[^0-9]/g, "") || 0;
                element.value = formatCurrency(valueFormatted);
            }

            [jmlOnlineElements, hargaOnlElements, diskonOngkirElements, ongkirElements, asuransiElements, proteksiElements, aplikasiElements
            ].forEach(function(elements, elementsIndex) {
                elements.forEach(function(element, index) {
                    element.addEventListener("input", function() {
                        if (elementsIndex !== 0) formatInput(element);
                        updateTotal(index);
                    });
                });
            });
        }

        // Calculate price in method oflline
        if (document.getElementById('is_offline').checked == false || document.getElementById('edit-is_offline').checked == false) {
            jmlOfflineElements.forEach(function(jml, index) {
                jml.addEventListener("input", function() {
                    let harga = hargaTokoElements[index];
                    let total = totalElements[index];
                    let jmlValue = parseFraction(jml.value.replace(',', '.'));

                    if (harga) {
                        let hargaValue = parseInt(harga.value.replace(/[^0-9]/g, ""), 10) || 0;
                        let totalValue = hargaValue * jmlValue;
                        total.value = formatCurrency(totalValue);
                    }
                });
            });

            hargaTokoElements.forEach(function(harga, index) {
                harga.addEventListener("input", function() {
                    let jml = jmlOfflineElements[index];
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
        }

        // Format currency
        function addCurrencyFormatter(elements) {
            elements.forEach(function(element) {
                element.addEventListener("input", function() {
                    this.value = formatCurrency(this.value);
                });
            });
        }

        addCurrencyFormatter(hargaTokoElements);
        addCurrencyFormatter(hargaOnlElements);
        addCurrencyFormatter(diskonOngkirElements);
        addCurrencyFormatter(ongkirElements);
        addCurrencyFormatter(asuransiElements);
        addCurrencyFormatter(proteksiElements);
        addCurrencyFormatter(aplikasiElements);
        addCurrencyFormatter(totalElements);

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
                    $('#edit-lokasi').val(row.data('lokasi'));
                    $('#edit-kendaraan').val(row.data('kendaraan'));
                    $('#edit-merk').val(row.data('merk'));
                    $('#edit-pemesan').val(row.data('pemesan'));
                    $('#edit-tgl_order').val(row.data('tgl_order'));
                    $('#edit-tgl_invoice').val(row.data('tgl_invoice'));
                    $('#edit-no_inventaris').val(row.data('no_inventaris'));
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-kategori').val(row.data('kategori'));
                    $('#edit-dipakai_untuk').val(row.data('dipakai_untuk'));
                    $('#edit-masa_pakai').val(row.data('masa_pakai'));
                    $('#edit-total').val(row.data('total'));
                    $('#edit-toko').val(row.data('toko'));

                    if (row.data('harga_online') && row.data('harga_online') != 'Rp 0') {
                        $('#edit-is_online').prop('checked', true);
                        toggleFieldsEdit();

                        $('#edit-jml_online').val(row.data('jml'));
                        $('#edit-unit_online').val(row.data('unit'));
                        $('#edit-harga_online').val(row.data('harga_online'));
                        $('#edit-diskon_ongkir').val(row.data('diskon_ongkir'));
                        $('#edit-ongkir').val(row.data('ongkir'));
                        $('#edit-asuransi').val(row.data('asuransi'));
                        $('#edit-b_proteksi').val(row.data('b_proteksi'));
                        $('#edit-b_jasa_aplikasi').val(row.data('b_jasa_aplikasi'));
                    }

                    if (row.data('harga') && row.data('harga') != 'Rp 0') {
                        $('#edit-is_offline').prop('checked', true);
                        toggleFieldsEdit();

                        $('#edit-jml_offline').val(row.data('jml'));
                        $('#edit-unit_offline').val(row.data('unit'));
                        $('#edit-harga').val(row.data('harga'));
                    }
                    
                }
            });
        }
    });
</script>
@endsection