<!-- resources/views/upload.blade.php -->
@extends('layouts.layout')

@section('title', 'Upload de CSV Hospitalar')

@section('content')
    <div class="card">
        <div class="card-header">
            Upload do CSV Hospitalar
        </div>
        <div class="card-body">
            <form action="{{ route('upload.census') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="census_file" class="form-label">Selecione o arquivo CSV</label>
                    <input type="file" class="form-control" id="census_file" name="census_file" accept=".csv" required>
                </div>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </div>
    </div>
@endsection
