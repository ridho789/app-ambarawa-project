@extends('layouts.base')
<!-- @section('title', 'Stok Barang') -->
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
            <h3 class="fw-bold mb-3">Data Stok Barang</h3>
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
                    <a href="#">Stok Barang</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="stokModal" tabindex="-1" role="dialog" aria-labelledby="stokModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="stokModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Stok Barang Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('stok-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="user" name="user" value="{{ auth()->user()->name }}">

                            <div class="form-group">
                                <label for="nama">Nama Barang</label>
                                <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama barang.." 
                                oninput="this.value = this.value.toUpperCase()" required />
                            </div>

                            <div class="form-group">
                                <label for="kategori">Kategori Barang</label>
                                <select class="form-control" name="kategori" id="kategori" required>
                                    <option value="" disabled selected>...</option>
                                    <option value="BESI">BESI</option>
                                    <option value="CAT">CAT</option>
                                    <option value="MATERIAL">MATERIAL</option>
                                    <option value="OPERASIONAL">OPERASIONAL</option>
                                    <option value="SPAREPART">SPAREPART</option>
                                </select>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="jumlah">Jumlah Stok</label>
                                    <input type="text" class="form-control" name="jumlah" id="jumlah" placeholder="Jumlah.." />
                                </div>
                                <div class="col-6">
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
                            </div>

                            <div class="form-group">
                                <label for="file">Upload Foto Barang</label>
                                <input type="file" name="file" accept="image/png, image/jpeg" id="file" onchange="previewImage(event)">
                                <div class="mt-3">
                                    <img id="image-preview" src="#" alt="Preview foto" style="display:none; max-width: 200px;"/>
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
        <div class="modal fade" id="stokEditModal" tabindex="-1" role="dialog" aria-labelledby="stokEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="stokEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Stok Barang </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('stok-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_stok">
                            <input type="hidden" id="edit-user" name="user" value="{{ auth()->user()->name }}">

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="nama">Nama Barang</label>
                                    <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama barang.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>

                                <div class="col-6">
                                    <label for="no_rak">No. Rak</label>
                                    <input type="text" class="form-control" name="no_rak" id="edit-no_rak" placeholder="Masukkan no. rak.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="kategori">Kategori</label>
                                <select class="form-control" name="kategori" id="edit-kategori" required>
                                    <option value="" disabled selected>...</option>
                                    <option value="BESI">BESI</option>
                                    <option value="CAT">CAT</option>
                                    <option value="MATERIAL">MATERIAL</option>
                                    <option value="OPERASIONAL">OPERASIONAL</option>
                                    <option value="SPAREPART">SPAREPART</option>
                                </select>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="merk">Merk</label>
                                    <input type="text" class="form-control" name="merk" id="edit-merk" placeholder="Masukkan merk barang.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>

                                <div class="col-6">
                                    <label for="type">Type</label>
                                    <input type="text" class="form-control" name="type" id="edit-type" placeholder="Masukkan type barang.." 
                                    oninput="this.value = this.value.toUpperCase()" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="jumlah">Jumlah Stok</label>
                                    <input type="text" class="form-control" name="jumlah" id="edit-jumlah" placeholder="Jumlah.." />
                                </div>
                                <div class="col-6">
                                    @if (count($satuan) > 0)
                                        <label for="satuan">Satuan</label>
                                        <select class="form-select form-control" name="unit" id="edit-unit">
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
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="edit-keterangan" placeholder="Masukkan keterangan.." ></textarea>
                            </div>

                            <!-- foto barang -->
                            <div class="form-group">
                                <label for="foto">Upload Foto Barang</label>
                                <input type="file" class="form-control" name="foto" id="edit-foto" accept=".png, .jpg, .jpeg" onchange="previewImageModal(event)"/>
                            </div>

                            <!-- Preview gambar -->
                            <div class="form-group">
                                <img id="edit-image-preview" style="display:none; width: 200px; height: auto;" />
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
        <div class="modal fade" id="stokExportModal" tabindex="-1" role="dialog" aria-labelledby="stokExportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="stokExportModal">
                            <span class="fw-light"> Export Data</span>
                            <span class="fw-mediumbold"> Stok Barang </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('stok-export') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Tampilkan semua data
                            </p>
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
        <div class="modal fade" id="stokImportModal" tabindex="-1" role="dialog" aria-labelledby="stokImportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="stokImportModal">
                            <span class="fw-light"> Import Data</span>
                            <span class="fw-mediumbold"> Stok Barang </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('stok-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Pilih file dengan format .xlsx
                            </p>
                            <div class="form-group">
                                <label for="file">Upload file</label>
                                <input type="file" class="form-control" name="file" accept=".xlsx">
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

        <!-- Modal untuk menampilkan gambar -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <img class="avatar-img rounded" id="modal-image" src="" alt="Gambar" style="width: 100%; height: auto;">
                    </div>
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
                            <div class="card-title">List Stok Barang</div>
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#stokEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('stok-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="allSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            <div class="ms-auto d-flex align-items-center">
                                @if (count($stok) > 0)
                                    <button class="btn btn-success btn-round ms-2 btn-sm" data-bs-toggle="modal" data-bs-target="#stokExportModal">
                                        <i class="fa fa-file-excel"></i>
                                        Export data
                                    </button>
                                @endif
                                <button class="btn btn-black btn-round ms-3 btn-sm" data-bs-toggle="modal" data-bs-target="#stokImportModal">
                                    <i class="fa fa-file-excel"></i>
                                    Import data
                                </button>
                                <button class="btn btn-primary btn-round ms-auto btn-sm" data-bs-toggle="modal" data-bs-target="#stokModal" style="display: none;">
                                    <i class="fa fa-plus"></i>
                                    Tambah data
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (count($stok) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th width=5%>
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">Nama Barang</th>
                                        <th class="text-xxs-bold">Kategori</th>
                                        <th class="text-xxs-bold">Merk</th>
                                        <th class="text-xxs-bold">Type</th>
                                        <th class="text-xxs-bold">Jumlah <br> Stok</th>
                                        <th class="text-xxs-bold">Satuan</th>
                                        <th class="text-xxs-bold">No. Rak</th>
                                        <th class="text-xxs-bold">Keterangan</th>
                                        <th class="text-xxs-bold">Foto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stok as $s)
                                    <tr data-id="{{ $s->id_stok_barang }}" 
                                        data-nama="{{ $s->nama }}"
                                        data-kategori="{{ $s->kategori }}"
                                        data-merk="{{ $s->merk }}"
                                        data-type="{{ $s->type }}"
                                        data-jumlah="{{ $s->jumlah }}"
                                        data-satuan="{{ $s->id_satuan }}"
                                        data-no_rak="{{ $s->no_rak }}"
                                        data-keterangan="{{ $s->keterangan }}"
                                        data-foto="{{ $s->foto }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td class="{{ $s->jumlah < 5 ? 'text-danger' : '' }}">{{ $s->nama ?? '-' }}</td>
                                        <td>{{ !empty($s->kategori) ? $s->kategori : '-' }}</td>
                                        <td>{{ !empty($s->merk) ? $s->merk : '-' }}</td>
                                        <td>{{ !empty($s->type) ? $s->type : '-' }}</td>
                                        <td class="{{ $s->jumlah < 5 ? 'text-danger' : '' }}">{{ $s->jumlah ?? '-' }}</td>
                                        <td>{{ $namaSatuan[$s->id_satuan] ?? '-' }}</td>
                                        <td>{{ !empty($s->no_rak) ? $s->no_rak : '-' }}</td>
                                        <td>{{ !empty($s->keterangan) ? $s->keterangan : '-' }}</td>
                                        <td>
                                            @if(!empty($s->foto))
                                                <a href="#" class="image-link" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ asset('storage/' . $s->foto) }}">
                                                    <img class="avatar-img rounded" src="{{ asset('storage/' . $s->foto) }}" alt="Foto" style="width: 125px; height: auto;">
                                                </a>
                                            @else
                                                -
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
                            <strong>Catatan: </strong> <span>Stok barang yang jumlahnya kurang dari 5 akan ditampilkan dengan warna <span class="text-danger">merah</span></span>
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
    function previewImageModal(event) {
        var input = event.target;
        var reader = new FileReader();
        var imgElement = document.getElementById('edit-image-preview');

        reader.onload = function() {
            imgElement.src = reader.result;
            imgElement.style.display = 'block';
        }

        if (input.files && input.files[0]) {
            reader.readAsDataURL(input.files[0]);
        }
    }

    var modal = document.getElementById('stokEditModal');
    modal.addEventListener('hidden.bs.modal', function () {
        var imgElement = document.getElementById('edit-image-preview');
        imgElement.style.display = 'none';
        imgElement.src = '';
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Link Image to Preview
        var imageLinks = document.querySelectorAll('.image-link');
        var modalImage = document.getElementById('modal-image');

        imageLinks.forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                var imageUrl = this.getAttribute('data-image');
                modalImage.src = imageUrl;
            });
        });

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
                    $('#edit-nama').val(row.data('nama'));
                    $('#edit-no_rak').val(row.data('no_rak'));
                    $('#edit-kategori').val(row.data('kategori'));
                    $('#edit-merk').val(row.data('merk'));
                    $('#edit-type').val(row.data('type'));
                    $('#edit-jumlah').val(row.data('jumlah'));
                    $('#edit-unit').val(row.data('satuan'));
                    $('#edit-keterangan').val(row.data('keterangan'));
                }
            });
        }

    });
</script>
@endsection