@extends('layouts.admin')
@section('title', 'Edit Banner')
@section('page-title', 'Edit Banner Carousel')
@section('content')
    <div class="card form-card">
        <form action="{{ route('admin.banner.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.banner._form')
            <div class="form-actions">
                <a href="{{ route('admin.banner.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection
