@extends('layouts.admin')
@section('title', 'Tambah Banner')
@section('page-title', 'Tambah Banner Carousel')
@section('content')
    <div class="card form-card">
        <form action="{{ route('admin.banner.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.banner._form')
            <div class="form-actions">
                <a href="{{ route('admin.banner.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Banner</button>
            </div>
        </form>
    </div>
@endsection
