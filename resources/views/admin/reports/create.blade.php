@extends('layouts.admin')

@section('title', 'Novo Relatório')
@section('page-title', 'Novo Relatório')
@section('page-subtitle', 'Preencha as métricas do mês.')

@section('topbar-actions')
    <a href="{{ route('admin.relatorios.index') }}" class="btn-secondary btn-sm">
        <x-heroicon-o-arrow-left class="w-4 h-4" /> Voltar
    </a>
@endsection

@section('content')
    @include('admin.reports.form')
@endsection