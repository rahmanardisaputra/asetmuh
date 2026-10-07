@extends('layouts.app')
@section('title', 'Preview Import Data Barang')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
        <div class="card-title">
            <i class="fa-solid fa-eye" style="color: var(--primary);"></i> Preview Data Import
        </div>
        <div>
            <a href="{{ route('aset.index') }}" class="btn btn-outline">Batal</a>
            @if(!$hasError)
            <form action="{{ route('aset.import.process') }}" method="POST" style="display: inline-block;">
                @csrf
                <input type="hidden" name="path_file" value="{{ $path }}">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Proses Import</button>
            </form>
            @endif
        </div>
    </div>
    
    <div class="card-body" style="padding-top: 1.5rem;">
        @if($hasError)
            <div style="background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <strong><i class="fa-solid fa-triangle-exclamation"></i> Terdapat data yang tidak valid!</strong><br>
                Beberapa kolom seperti Kategori, Ruangan, atau Sumber Dana tidak ditemukan di database. Pastikan pengetikannya benar (tanpa typo). Silakan perbaiki file Excel Anda dan upload kembali.
            </div>
        @else
            <div style="background: #d1fae5; border: 1px solid #a7f3d0; color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <strong><i class="fa-solid fa-circle-check"></i> Semua data valid!</strong><br>
                Data barang sudah siap untuk di-import. Klik tombol "Proses Import" di kanan atas.
            </div>
        @endif

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Ruangan</th>
                        <th>Sumber Dana</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewData as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row['nama_barang'] }}</td>
                        <td style="{{ !$row['kategori_valid'] ? 'background-color: #fee2e2; color: #b91c1c; font-weight: bold;' : 'color: #047857;' }}">
                            {{ $row['kategori'] }} {!! !$row['kategori_valid'] ? '<i class="fa-solid fa-times-circle"></i>' : '<i class="fa-solid fa-check-circle"></i>' !!}
                        </td>
                        <td style="{{ !$row['ruangan_valid'] ? 'background-color: #fee2e2; color: #b91c1c; font-weight: bold;' : 'color: #047857;' }}">
                            {{ $row['ruangan'] }} {!! !$row['ruangan_valid'] ? '<i class="fa-solid fa-times-circle"></i>' : '<i class="fa-solid fa-check-circle"></i>' !!}
                        </td>
                        <td style="{{ !$row['sumber_dana_valid'] ? 'background-color: #fee2e2; color: #b91c1c; font-weight: bold;' : 'color: #047857;' }}">
                            {{ $row['sumber_dana'] }} {!! !$row['sumber_dana_valid'] ? '<i class="fa-solid fa-times-circle"></i>' : '<i class="fa-solid fa-check-circle"></i>' !!}
                        </td>
                        <td>{{ $row['jumlah'] }} Unit</td>
                        <td>Rp {{ number_format($row['harga'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
