@extends('layouts.layout')

@section('title', 'Revisão de Dados do CSV')
@section('content')
    <div class="card">
        <div class="card-header">
            Revisão de Dados do CSV
        </div>
        <div class="card-body">
            @if (session('newPatients') || session('newInternments'))
                <div class="alert alert-success" role="alert">
                    <strong>Sucesso!</strong>
                    @if (session('newPatients'))
                        Foram cadastrados {{ session('newPatients') }} novos pacientes.
                    @endif
                    @if (session('newInternments'))
                        Foram cadastradas {{ session('newInternments') }} novas internações.
                    @endif
                </div>
            @endif

            @if (session('valid_data') || session('invalid_data'))
                <h3>Dados Válidos</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Nascimento</th>
                            <th>Código</th>
                            <th>Guia</th>
                            <th>Entrada</th>
                            <th>Saída</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('valid_data') as $row)
                            <tr>
                                <td>{{ $row['nome'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($row['nascimento'])->format('d/m/Y') }}</td>
                                <td>{{ $row['codigo'] }}</td>
                                <td>{{ $row['guia'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($row['entrada'])->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($row['saida'])->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3>Dados Inválidos</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Erro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('invalid_data') as $row)
                            <tr>
                                <td>{{ $row['row']['nome'] }}</td>
                                <td>{{ $row['error'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <form action="{{ route('save.census') }}" method="POST">
                    @csrf
                    <input type="hidden" name="valid_data" value="{{ json_encode(session('valid_data')) }}">
                    <button type="submit" class="btn btn-success">Salvar Dados Válidos</button>
                </form>
            @else
                <p>Nenhum dado para revisar.</p>
            @endif
        </div>
    </div>
@endsection
