@extends('layouts.base')
<!-- @section('title', 'Toko') -->
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
            <h3 class="fw-bold mb-3">Data Toko</h3>
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
                    <a href="#">Toko</a>
                </li>
            </ul>
        </div>

        <!-- Modal Tambah data -->
        <div class="modal fade" id="tokoModal" tabindex="-1" role="dialog" aria-labelledby="tokoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="tokoModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Toko Baru </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('toko-store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Buat data baru dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="user" name="user" value="{{ auth()->user()->name }}">

                            <div class="form-group">
                                <label for="nama">Nama (Toko)</label>
                                <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama toko.." 
                                oninput="this.value = this.value.toUpperCase()" required />
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
        <div class="modal fade" id="tokoEditModal" tabindex="-1" role="dialog" aria-labelledby="tokoEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 mx-2" style="margin-bottom: -25px;">
                        <h5 class="modal-title" id="tokoEditModal">
                            <span class="fw-light"> Data</span>
                            <span class="fw-mediumbold"> Toko </span>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('toko-update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <p class="small mx-2">
                                Perbaharui data dengan formulir ini, pastikan Anda mengisi semuanya
                            </p>

                            <input type="hidden" id="edit-id" name="id_toko">
                            <input type="hidden" id="edit-user" name="user" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="page" value="{{ request()->get('page', 1) }}">

                            <div class="form-group">
                                <label for="nama">Nama (Toko)</label>
                                <input type="text" class="form-control" name="nama" id="edit-nama" placeholder="Masukkan nama toko.." 
                                oninput="this.value = this.value.toUpperCase()" required />
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
                            <div class="card-title">List toko</div>
                            <button id="editButton" class="btn btn-warning btn-round ms-5 btn-sm" data-bs-toggle="modal" data-bs-target="#tokoEditModal" style="display: none;">
                                <i class="fa fa-edit"></i>
                                 Edit data
                            </button>
                            <form id="deleteForm" method="POST" action="{{ url('toko-delete') }}" class="d-inline">
                                @csrf
                                <input type="hidden" id="allSelectRow" name="ids" value="">
                                <button id="deleteButton" type="button" class="btn btn-danger btn-round ms-2 btn-sm" style="display: none;">
                                    <i class="fa fa-trash"></i>
                                    Delete data
                                </button>
                            </form>
                            <button class="btn btn-primary btn-round ms-auto btn-sm" data-bs-toggle="modal" data-bs-target="#tokoModal">
                                <i class="fa fa-plus"></i>
                                Tambah data
                            </button>
                        </div>
                    </div>
                    @if (count($toko) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th width=5%>
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">Nama (Toko)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($toko as $s)
                                    <tr data-id="{{ $s->id_toko }}" 
                                        data-nama="{{ $s->nama }}">
                                        <td><input type="checkbox" class="select-checkbox"></td>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $s->nama ?? '-' }}</td>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Checkbox
        var page = new URLSearchParams(window.location.search).get('page');
        var table = $('#basic-datatables').DataTable();
        var selectAllCheckbox = document.getElementById('selectAllCheckbox');
        var allSelectRowInput = document.getElementById('allSelectRow');
        var editButton = document.getElementById('editButton');
        var deleteButton = document.getElementById('deleteButton');

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
                // Tambahkan nomor halaman ke form edit
                var currentPage = table.page.info().page + 1;
                document.querySelector('input[name="page"]').value = currentPage;

                var selectedId = allSelectRowInput.value.split(',')[0];
                if (selectedId) {
                    var row = $('tr[data-id="' + selectedId + '"]');
                    
                    $('#edit-id').val(selectedId);
                    $('#edit-nama').val(row.data('nama'));
                }
            });
        }

    });
</script>
@endsection