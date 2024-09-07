@extends('layouts.base')
<!-- @section('title', 'Catatan Aktivitas') -->
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
            <h3 class="fw-bold mb-3">Data Catatan Aktivitas</h3>
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
                    <a href="#">Catatan Aktivitas</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="card-title">List Catatan Aktivitas</div>
                        </div>
                    </div>
                    @if (count($activities) > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-xxs-bold">No.</th>
                                        <th class="text-xxs-bold">Deskripsi</th>
                                        <th class="text-xxs-bold">Lingkup</th>
                                        <th class="text-xxs-bold">Aksi</th>
                                        <th class="text-xxs-bold">User</th>
                                        <th class="text-xxs-bold">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $a)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $a->description ?? '-' }}</td>
                                        <td>{{ $a->scope ?? '-' }}</td>
                                        <td>{{ $a->action ?? '-' }}</td>
                                        <td>{{ $a->user ?? '-' }}</td>
                                        <td>{{ $a->action_time ? \Carbon\Carbon::parse($a->action_time)->format('d M Y H:i:s') : '-' }}</td>
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
        const tableData = $('#basic-datatables').DataTable();
    });
</script>
@endsection