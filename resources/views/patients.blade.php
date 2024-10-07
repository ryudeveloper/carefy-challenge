<!-- resources/views/patients.blade.php -->
@extends('layouts.layout')

@section('title', 'Lista de Pacientes')

@section('content')
    <div class="card">
        <div class="card-header">
            Lista de Pacientes e Internações
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Nascimento</th>
                        <th>Código</th>
                        <th>Internações</th>
                        <th>Entrada</th>
                        <th>Saída</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patients as $patient)
                        @foreach ($patient->internments as $internment) <!-- Loop pelas internações -->
                            <tr>
                                <td>{{ $patient->var_nome }}</td>
                                <td>{{ \Carbon\Carbon::parse($patient->var_nascimento)->format('d/m/Y') }}</td>
                                <td>{{ $patient->var_codigo }}</td>
                                <td>{{ $internment->var_guia }}</td>
                                <td>{{ \Carbon\Carbon::parse($internment->var_entrada)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($internment->var_saida)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
