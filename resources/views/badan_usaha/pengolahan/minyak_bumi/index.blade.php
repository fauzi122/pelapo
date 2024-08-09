@extends('layouts.frontand.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Laporan Pengolahan Minyak Bumi/Hasil Olahan & Gas Bumi</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Pengolahan</a></li>
                                <li class="breadcrumb-item active"> Minyak Bumi/Hasil Olahan< & Gas Bumi/li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nav tabs -->
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-pills nav-justified" role="tablist">
                        <li class="nav-item waves-effect waves-light" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href=".minyak-bumi" role="tab"
                                aria-selected="true">
                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                <span class="d-none d-sm-block">Minyak Bumi/Hasil Olahan</span>
                            </a>
                        </li>
                        <li class="nav-item waves-effect waves-light" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href=".gas-bumi" role="tab" aria-selected="false"
                                tabindex="-1">
                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                <span class="d-none d-sm-block">Gas Bumi</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab panes -->
            <div class="tab-content text-muted">
                <div class="tab-pane active show minyak-bumi" id="home-1" role="tabpanel">
                    {{-- Pengolahan Minyak Bumi Produksi Kilang --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Produksi Kilang [Minyak Bumi/Hasil Olahan]</h5>
                                        <div>
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                onclick="produk(); provinsi();" data-bs-toggle="modal"
                                                data-bs-target="#buat-pengolahan-produksi-mb">Buat Laporan</button>
                                            <button type="button" class="btn btn-success waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#excelPengolahanMBProduksi">Import
                                                Excel</button>
                                            <!-- Include modal content -->
                                            @include('badan_usaha.pengolahan.minyak_bumi.modal')
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="table1" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Bulan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pengolahanProduksiMB as $ppmb)
                                                    @php
                                                        $id = Crypt::encryptString($ppmb->bulan . ',' . $ppmb->badan_usaha_id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <b><a
                                                                    href="/pengolahan-minyak-bumi-hasil-olah/show/{{ $id }}/produksi">{{ dateIndonesia($ppmb->bulan) }}
                                                                    <i class="bx bx-check"
                                                                        title="lihat data laporan"></i></a>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            @if ($ppmb->status_tertinggi == 1 && $ppmb->catatanx)
                                                                <span class="badge bg-warning">Sudah Diperbaiki</span>
                                                            @elseif ($ppmb->status_tertinggi == 1)
                                                                <span class="badge bg-success">Kirim</span>
                                                            @elseif ($ppmb->status_tertinggi == 2)
                                                                <span class="badge bg-danger">Revisi</span>
                                                            @elseif ($ppmb->status_tertinggi == 0)
                                                                <span class="badge bg-info">draf</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <form
                                                                action="/hapus_bulan_pengolahan_minyak_bumi_produksi/{{ $ppmb->bulan }}"
                                                                method="post" class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="hapusData($(this).closest('form'))"
                                                                    {{ $ppmb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-trash-alt" title="Hapus data"></i>
                                                                </button>
                                                            </form>
                                                            <form
                                                                action="/submit_bulan_pengolahan_minyak_bumi_produksi/{{ $ppmb->bulan }}"
                                                                method="post" class="d-inline"
                                                                data-id="{{ $ppmb->bulan }}">
                                                                @method('PUT')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-success"
                                                                    onclick="kirimData($(this).closest('form'))"
                                                                    {{ $ppmb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-paper-plane" title="Kirim data"></i>
                                                                </button>
                                                            </form>
                                                            @if ($ppmb->status_tertinggi != 1)
                                                                <a href="/pengolahan-minyak-bumi-hasil-olah/show/{{ $id }}/produksi"
                                                                    class="btn btn-sm btn-info">
                                                                    <i class="bx bx-edit" title="Revisi"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <!-- Add more rows as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Pengolahan Minyak Bumi Produksi Kilang --}}

                    {{-- Pengolahan Minyak Bumi Pasokan Kilang --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Pasokan Kilang [Minyak Bumi/Hasil Olahan]</h5>
                                        <div>
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                onclick="intake_kilang(); provinsi();" data-bs-toggle="modal"
                                                data-bs-target="#buat-pengolahan-pasokan-mb">Buat Laporan</button>
                                            <button type="button" class="btn btn-success waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#excelPengolahanMBPasokan">Import
                                                Excel</button>
                                            <!-- Include modal content -->
                                            @include('badan_usaha.pengolahan.minyak_bumi.modal')
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="table2" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Bulan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pengolahanPasokanMB as $ppmb)
                                                    @php
                                                        $id = Crypt::encryptString($ppmb->bulan . ',' . $ppmb->badan_usaha_id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <b><a
                                                                    href="/pengolahan-minyak-bumi-hasil-olah/show/{{ $id }}/pasokan">{{ dateIndonesia($ppmb->bulan) }}
                                                                    <i class="bx bx-check"
                                                                        title="lihat data laporan"></i></a>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            @if ($ppmb->status_tertinggi == 1 && $ppmb->catatanx)
                                                                <span class="badge bg-warning">Sudah Diperbaiki</span>
                                                            @elseif ($ppmb->status_tertinggi == 1)
                                                                <span class="badge bg-success">Kirim</span>
                                                            @elseif ($ppmb->status_tertinggi == 2)
                                                                <span class="badge bg-danger">Revisi</span>
                                                            @elseif ($ppmb->status_tertinggi == 0)
                                                                <span class="badge bg-info">draf</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <form
                                                                action="/hapus_bulan_pengolahan_minyak_bumi_pasokan/{{ $ppmb->bulan }}"
                                                                method="post" class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="hapusData($(this).closest('form'))"
                                                                    {{ $ppmb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-trash-alt" title="Hapus data"></i>
                                                                </button>
                                                            </form>
                                                            <form
                                                                action="/submit_bulan_pengolahan_minyak_bumi_pasokan/{{ $ppmb->bulan }}"
                                                                method="post" class="d-inline"
                                                                data-id="{{ $ppmb->bulan }}">
                                                                @method('PUT')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-success"
                                                                    onclick="kirimData($(this).closest('form'))"
                                                                    {{ $ppmb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-paper-plane" title="Kirim data"></i>
                                                                </button>
                                                            </form>
                                                            @if ($ppmb->status_tertinggi != 1)
                                                                <a href="/pengolahan-minyak-bumi-hasil-olah/show/{{ $id }}/pasokan"
                                                                    class="btn btn-sm btn-info">
                                                                    <i class="bx bx-edit" title="Revisi"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <!-- Add more rows as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Pengolahan Minyak Bumi Pasokan Kilang --}}

                    {{-- Pengolahan Minyak Bumi Distribusi Kilang --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Distribusi/Penjualan Domestik Kilang [Minyak Bumi/Hasil Olahan]
                                        </h5>
                                        <div>
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                onclick="produk(); provinsi(); sektor();" data-bs-toggle="modal"
                                                data-bs-target="#buat-pengolahan-distribusi-mb">Buat Laporan</button>
                                            <button type="button" class="btn btn-success waves-effect waves-light"
                                                data-bs-toggle="modal"
                                                data-bs-target="#excelPengolahanMBDistribusi">Import
                                                Excel</button>
                                            <!-- Include modal content -->
                                            @include('badan_usaha.pengolahan.minyak_bumi.modal')
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="table3" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Bulan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pengolahanDistribusiMB as $ppmb)
                                                    @php
                                                        $id = Crypt::encryptString($ppmb->bulan . ',' . $ppmb->badan_usaha_id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <b>
                                                                <a
                                                                    href="/pengolahan-minyak-bumi-hasil-olah/show/{{ $id }}/distribusi">{{ dateIndonesia($ppmb->bulan) }}
                                                                    <i class="bx bx-check" title="lihat data laporan"></i>
                                                                </a>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            @if ($ppmb->status_tertinggi == 1 && $ppmb->catatanx)
                                                                <span class="badge bg-warning">Sudah Diperbaiki</span>
                                                            @elseif ($ppmb->status_tertinggi == 1)
                                                                <span class="badge bg-success">Kirim</span>
                                                            @elseif ($ppmb->status_tertinggi == 2)
                                                                <span class="badge bg-danger">Revisi</span>
                                                            @elseif ($ppmb->status_tertinggi == 0)
                                                                <span class="badge bg-info">draf</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <form
                                                                action="/hapus_bulan_pengolahan_minyak_bumi_distribusi/{{ $ppmb->bulan }}"
                                                                method="post" class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="hapusData($(this).closest('form'))"
                                                                    {{ $ppmb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-trash-alt" title="Hapus data"></i>
                                                                </button>
                                                            </form>
                                                            <form
                                                                action="/submit_bulan_pengolahan_minyak_bumi_distribusi/{{ $ppmb->bulan }}"
                                                                method="post" class="d-inline"
                                                                data-id="{{ $ppmb->bulan }}">
                                                                @method('PUT')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-success"
                                                                    onclick="kirimData($(this).closest('form'))"
                                                                    {{ $ppmb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-paper-plane" title="Kirim data"></i>
                                                                </button>
                                                            </form>
                                                            @if ($ppmb->status_tertinggi != 1)
                                                                <a href="/pengolahan-minyak-bumi-hasil-olah/show/{{ $id }}/distribusi"
                                                                    class="btn btn-sm btn-info">
                                                                    <i class="bx bx-edit" title="Revisi"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <!-- Add more rows as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Pengolahan Minyak Bumi Produksi Kilang --}}
                </div>

                <div class="tab-pane gas-bumi" id="settings-1" role="tabpanel">
                    {{-- Pengolahan Gas Bumi Produksi Kilang --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Produksi Kilang [Gas Bumi]</h5>
                                        <div>
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                onclick="produk(); provinsi();" data-bs-toggle="modal"
                                                data-bs-target="#buat-pengolahan-produksi-gb">Buat Laporan</button>
                                            <button type="button" class="btn btn-success waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#excelPengolahanGBProduksi">Import
                                                Excel</button>
                                            <!-- Include modal content -->
                                            @include('badan_usaha.pengolahan.gas_bumi.modal')
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="table4" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Bulan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pengolahanProduksiGB as $ppgb)
                                                    @php
                                                        $id = Crypt::encryptString($ppgb->bulan . ',' . $ppgb->badan_usaha_id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <b><a
                                                                    href="/pengolahan-gas-bumi/show/{{ $id }}/produksi">{{ dateIndonesia($ppgb->bulan) }}
                                                                    <i class="bx bx-check"
                                                                        title="lihat data laporan"></i></a>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            @if ($ppgb->status_tertinggi == 1 && $ppgb->catatanx)
                                                                <span class="badge bg-warning">Sudah Diperbaiki</span>
                                                            @elseif ($ppgb->status_tertinggi == 1)
                                                                <span class="badge bg-success">Kirim</span>
                                                            @elseif ($ppgb->status_tertinggi == 2)
                                                                <span class="badge bg-danger">Revisi</span>
                                                            @elseif ($ppgb->status_tertinggi == 0)
                                                                <span class="badge bg-info">draf</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <form
                                                                action="/hapus_bulan_pengolahan_gas_bumi_produksi/{{ $ppgb->bulan }}"
                                                                method="post" class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="hapusData($(this).closest('form'))"
                                                                    {{ $ppgb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-trash-alt" title="Hapus data"></i>
                                                                </button>
                                                            </form>
                                                            <form
                                                                action="/submit_bulan_pengolahan_gas_bumi_produksi/{{ $ppgb->bulan }}"
                                                                method="post" class="d-inline"
                                                                data-id="{{ $ppgb->bulan }}">
                                                                @method('PUT')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-success"
                                                                    onclick="kirimData($(this).closest('form'))"
                                                                    {{ $ppgb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-paper-plane" title="Kirim data"></i>
                                                                </button>
                                                            </form>
                                                            @if ($ppgb->status_tertinggi != 1)
                                                                <a href="/pengolahan-gas-bumi/show/{{ $id }}/produksi"
                                                                    class="btn btn-sm btn-info">
                                                                    <i class="bx bx-edit" title="Revisi"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <!-- Add more rows as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Pengolahan Gas Bumi Produksi Kilang --}}

                    {{-- Pengolahan Gas Bumi Pasokan Kilang --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Pasokan Kilang [Gas Bumi]</h5>
                                        <div>
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                onclick="intake_kilang(); provinsi();" data-bs-toggle="modal"
                                                data-bs-target="#buat-pengolahan-pasokan-gb">Buat Laporan</button>
                                            <button type="button" class="btn btn-success waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#excelPengolahanGBPasokan">Import
                                                Excel</button>
                                            <!-- Include modal content -->
                                            @include('badan_usaha.pengolahan.gas_bumi.modal')
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="table5" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Bulan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pengolahanPasokanGB as $ppgb)
                                                    @php
                                                        $id = Crypt::encryptString($ppgb->bulan . ',' . $ppgb->badan_usaha_id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <b><a
                                                                    href="/pengolahan-gas-bumi/show/{{ $id }}/pasokan">{{ dateIndonesia($ppgb->bulan) }}
                                                                    <i class="bx bx-check"
                                                                        title="lihat data laporan"></i></a>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            @if ($ppgb->status_tertinggi == 1 && $ppgb->catatanx)
                                                                <span class="badge bg-warning">Sudah Diperbaiki</span>
                                                            @elseif ($ppgb->status_tertinggi == 1)
                                                                <span class="badge bg-success">Kirim</span>
                                                            @elseif ($ppgb->status_tertinggi == 2)
                                                                <span class="badge bg-danger">Revisi</span>
                                                            @elseif ($ppgb->status_tertinggi == 0)
                                                                <span class="badge bg-info">draf</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <form
                                                                action="/hapus_bulan_pengolahan_gas_bumi_pasokan/{{ $ppgb->bulan }}"
                                                                method="post" class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="hapusData($(this).closest('form'))"
                                                                    {{ $ppgb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-trash-alt" title="Hapus data"></i>
                                                                </button>
                                                            </form>
                                                            <form
                                                                action="/submit_bulan_pengolahan_gas_bumi_pasokan/{{ $ppgb->bulan }}"
                                                                method="post" class="d-inline"
                                                                data-id="{{ $ppgb->bulan }}">
                                                                @method('PUT')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-success"
                                                                    onclick="kirimData($(this).closest('form'))"
                                                                    {{ $ppgb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-paper-plane" title="Kirim data"></i>
                                                                </button>
                                                            </form>
                                                            @if ($ppgb->status_tertinggi != 1)
                                                                <a href="/pengolahan-gas-bumi/show/{{ $id }}/pasokan"
                                                                    class="btn btn-sm btn-info">
                                                                    <i class="bx bx-edit" title="Revisi"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <!-- Add more rows as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Pengolahan Gas Bumi Pasokan Kilang --}}

                    {{-- Pengolahan Gas Bumi Distribusi Kilang --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Distribusi/Penjualan Domestik Kilang [Gas Bumi]</h5>
                                        <div>
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                onclick="produk(); provinsi(); sektor();" data-bs-toggle="modal"
                                                data-bs-target="#buat-pengolahan-distribusi-gb">Buat Laporan</button>
                                            <button type="button" class="btn btn-success waves-effect waves-light"
                                                data-bs-toggle="modal"
                                                data-bs-target="#excelPengolahanGBDistribusi">Import
                                                Excel</button>
                                            <!-- Include modal content -->
                                            @include('badan_usaha.pengolahan.gas_bumi.modal')
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="table6" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Bulan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pengolahanDistribusiGB as $ppgb)
                                                    @php
                                                        $id = Crypt::encryptString($ppgb->bulan . ',' . $ppgb->badan_usaha_id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <b>
                                                                <a
                                                                    href="/pengolahan-gas-bumi/show/{{ $id }}/distribusi">{{ dateIndonesia($ppgb->bulan) }}
                                                                    <i class="bx bx-check" title="lihat data laporan"></i>
                                                                </a>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            @if ($ppgb->status_tertinggi == 1 && $ppgb->catatanx)
                                                                <span class="badge bg-warning">Sudah Diperbaiki</span>
                                                            @elseif ($ppgb->status_tertinggi == 1)
                                                                <span class="badge bg-success">Kirim</span>
                                                            @elseif ($ppgb->status_tertinggi == 2)
                                                                <span class="badge bg-danger">Revisi</span>
                                                            @elseif ($ppgb->status_tertinggi == 0)
                                                                <span class="badge bg-info">draf</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <form
                                                                action="/hapus_bulan_pengolahan_gas_bumi_distribusi/{{ $ppgb->bulan }}"
                                                                method="post" class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="hapusData($(this).closest('form'))"
                                                                    {{ $ppgb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-trash-alt" title="Hapus data"></i>
                                                                </button>
                                                            </form>
                                                            <form
                                                                action="/submit_bulan_pengolahan_gas_bumi_distribusi/{{ $ppgb->bulan }}"
                                                                method="post" class="d-inline"
                                                                data-id="{{ $ppgb->bulan }}">
                                                                @method('PUT')
                                                                @csrf
                                                                <button type="button" class="btn btn-sm btn-success"
                                                                    onclick="kirimData($(this).closest('form'))"
                                                                    {{ $ppgb->status_tertinggi == 1 ? 'disabled' : '' }}>
                                                                    <i class="bx bx-paper-plane" title="Kirim data"></i>
                                                                </button>
                                                            </form>
                                                            @if ($ppgb->status_tertinggi != 1)
                                                                <a href="/pengolahan-gas-bumi/show/{{ $id }}/distribusi"
                                                                    class="btn btn-sm btn-info">
                                                                    <i class="bx bx-edit" title="Revisi"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <!-- Add more rows as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Pengolahan Gas Bumi Produksi Kilang --}}
                </div>
            </div>
        </div>
    </div>
@endsection
