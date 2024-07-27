@extends('layouts.base')
<!-- @section('title', 'Pasir') -->
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
            <h3 class="fw-bold mb-3">Data Pengeluaran Pasir</h3>
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
                    <a href="#">Pasir</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="pasirModal" tabindex="-1" role="dialog" aria-labelledby="pasirModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="pasirModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Pasir Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('pasir-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <div class="form-group">
                                @if (count($proyek) > 0)
                                    <label for="proyek">Pilih Proyek</label>
                                    <select class="form-select form-control" name="proyek" id="proyek" required>
                                        <option value="">...</option>
                                        @foreach ($proyek as $p)
                                            <option value="{{ $p->id_proyek }}">{{ $p->nama }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <label style="margin-bottom: 4.5px;">Pilih proyek</label>
                                    <select class="form-control" disabled>
                                        <option value="">Tidak ada data</option>
                                    </select>
                                @endif
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" required />
                                </div>
                                <div class="col-6">
                                    <label for="nama">Nama (Barang)</label>
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama.." value="Pasir" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="ukuran">Ukuran</label>
                                <input type="text" class="form-control" name="ukuran" id="ukuran" placeholder="Masukkan ukuran.." required />
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <input type="text" class="form-control" name="deskripsi" id="deskripsi" placeholder="Masukkan deskripsi.." required />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="text" class="form-control" name="jumlah" id="jumlah" placeholder="Masukkan jumlah.." required />
                                </div>
                                <div class="col-4">
                                    <label for="satuan">Satuan</label>
                                    <input type="text" class="form-control" name="satuan" id="satuan" placeholder="Masukkan satuan.." required />
                                </div>
                                <div class="col-4">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="harga" placeholder="Masukkan harga.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="total" placeholder="Nilai total harga.." />
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
        <div class="modal fade" id="pasirEditModal" tabindex="-1" role="dialog" aria-labelledby="pasirEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="pasirEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Pengeluaran Pasir </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('pasir-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_pasir">

                            <div class="form-group">
                                @if (count($proyek) > 0)
                                    <label for="proyek">Pilih Proyek</label>
                                    <select class="form-select form-control" name="proyek" id="edit-proyek" required>
                                        <option value="">...</option>
                                        @foreach ($proyek as $p)
                                            <option value="{{ $p->id_proyek }}">{{ $p->nama }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <label style="margin-bottom: 4.5px;">Pilih proyek</label>
                                    <select class="form-control" disabled>
                                        <option value="">Tidak ada data</option>
                                    </select>
                                @endif
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="edit-tanggal" required />
                                </div>
                                <div class="col-6">
                                    <label for="nama">Nama (Barang)</label>
                                    <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="ukuran">Ukuran</label>
                                <input type="text" class="form-control" name="ukuran" id="edit-ukuran" placeholder="Masukkan ukuran.." required />
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <input type="text" class="form-control" name="deskripsi" id="edit-deskripsi" placeholder="Masukkan deskripsi.." required />
                            </div>

                            <div class="form-group">
                                <span class="h5 fw-mediumbold">Informasi Harga</span>
                            </div>

                            <div class="form-group row">
                                <div class="col-4">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="text" class="form-control" name="jumlah" id="edit-jumlah" placeholder="Masukkan jumlah.." required />
                                </div>
                                <div class="col-4">
                                    <label for="satuan">Satuan</label>
                                    <input type="text" class="form-control" name="satuan" id="edit-satuan" placeholder="Masukkan satuan.." required />
                                </div>
                                <div class="col-4">
                                    <label for="harga">Harga</label>
                                    <input type="text" class="form-control" name="harga" id="edit-harga" placeholder="Masukkan harga.." required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="total">Total Harga</label>
                                <input type="text" class="form-control" name="total" id="edit-total" placeholder="Nilai total harga.." />
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
                            <div class="card-title">List Pengeluaran Pasir</div>
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#pasirEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('pasir-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="allSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            <button class="btn btn-primary btn-round ms-auto btn-sm" data-bs-toggle="modal" data-bs-target="#pasirModal">
                                <i class="fa fa-plus"></i>
                                Tambah data
                            </button>
                        </div>
                    </div>
                    @if (count($pasir) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th width=5%>
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">Proyek</th>
                                        <th class="text-xxs-bold">Tanggal</th>
                                        <th class="text-xxs-bold">Nama (Barang)</th>
                                        <th class="text-xxs-bold">Ukuran</th>
                                        <th class="text-xxs-bold">Deskripsi</th>
                                        <th class="text-xxs-bold">Jumlah</th>
                                        <th class="text-xxs-bold">Satuan</th>
                                        <!-- <th class="text-xxs-bold">Harga</th> -->
                                        <th class="text-xxs-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pasir as $p)
                                    <tr data-id="{{ $p->id_pembangunan }}" 
                                    data-tanggal="{{ $p->tanggal }}" 
                                    data-proyek="{{ $p->id_proyek }}"
                                    data-nama="{{ $p->nama }}" 
                                    data-ukuran="{{ $p->ukuran }}" 
                                    data-deskripsi="{{ $p->deskripsi }}" 
                                    data-jumlah="{{ $p->jumlah }}" 
                                    data-satuan="{{ $p->satuan }}" 
                                    data-harga="{{ 'Rp ' . number_format($p->harga ?? 0, 0, ',', '.') }}" 
                                    data-total="{{ 'Rp ' . number_format($p->tot_harga ?? 0, 0, ',', '.') }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $namaProyek[$p->id_proyek] ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $p->tanggal)->format('d-M-Y') ?? '-' }}</td>
                                        <td>{{ $p->nama ?? '-' }}</td>
                                        <td>{{ $p->ukuran ?? '-' }}</td>
                                        <td>{{ $p->deskripsi ?? '-' }}</td>
                                        <td>{{ $p->jumlah ?? '-' }}</td>
                                        <td>{{ $p->satuan ?? '-' }}</td>
                                        <!-- <td>{{ 'Rp ' . number_format($p->harga ?? 0, 0, ',', '.') }}</td> -->
                                        <td>{{ 'Rp ' . number_format($p->tot_harga ?? 0, 0, ',', '.') }}</td>
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
            if (row.querySelector('td:nth-child(10)')) {
                const totalText = row.querySelector('td:nth-child(10)').innerText;
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

        function parseFraction(fraction) {
            let parts = fraction.split('/');
            if (parts.length === 2) {
                return parseFloat(parts[0]) / parseFloat(parts[1]);
            } else {
                return parseFloat(fraction);
            }
        }

        // Mengatur deskripsi
        const descriptionCells = document.querySelectorAll('#basic-datatables td:nth-child(7)');
        const maxLength = 50; // Ganti dengan panjang maksimum yang diinginkan

        descriptionCells.forEach(cell => {
            let text = cell.innerText;
            if (text.length > maxLength) {
                let formattedText = '';
                for (let i = 0; i < text.length; i += maxLength) {
                    formattedText += text.substring(i, i + maxLength) + '<br>';
                }
                cell.innerHTML = formattedText;
            }
        });

        // Ambil elemen harga, jumlah, dan total
        let hargaPasir = document.querySelectorAll("#harga, #edit-harga");
        let jmlElements = document.querySelectorAll("#jumlah, #edit-jumlah");
        let totalElements = document.querySelectorAll("#total, #edit-total");

        // Event listener untuk perubahan pada jumlah (jumlah)
        jmlElements.forEach(function(jml, index) {
            jml.addEventListener("input", function() {
                let harga = hargaPasir[index];
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
        hargaPasir.forEach(function(hargaSm, index) {
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

        // Harga
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
            selectAllCheckbox.addEventListener('change', function() {
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
                checkboxes[i].addEventListener('change', function() {
                    var row = this.parentNode.parentNode;
                    row.classList.toggle('selected', this.checked);

                    // Periksa apakah setidaknya satu checkbox terpilih
                    var atLeastOneChecked = Array.from(checkboxes).some(function(checkbox) {
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
                    .filter(function(checkbox) {
                        return checkbox.checked;
                    })
                    .map(function(checkbox) {
                        return checkbox.closest('tr').getAttribute('data-id');
                    });

                allSelectRowInput.value = selectedIds.join(',');
            }

            // Fungsi untuk mengatur visibilitas tombol
            function updateButtonVisibility() {
                var selectedCheckboxes = Array.from(checkboxes).filter(function(checkbox) {
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
            deleteButton.addEventListener('click', function() {
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

            editButton.addEventListener('click', function() {
                var selectedId = allSelectRowInput.value.split(',')[0];
                if (selectedId) {
                    var row = $('tr[data-id="' + selectedId + '"]');

                    $('#edit-id').val(selectedId);
                    $('#edit-proyek').val(row.data('proyek'));
                    $('#edit-tanggal').val(row.data('tanggal'));
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-ukuran').val(row.data('ukuran'));
                    $('#edit-deskripsi').val(row.data('deskripsi'));
                    $('#edit-jumlah').val(row.data('jumlah'));
                    $('#edit-satuan').val(row.data('satuan'));
                    $('#edit-harga').val(row.data('harga'));
                    $('#edit-total').val(row.data('total'));
                }
            });
        }

    });
</script>
@endsection