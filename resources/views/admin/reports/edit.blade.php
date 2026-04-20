@php
    $reportTitle = 'Editar Relatório';
    $subtitle = ($report->client?->trade_name ?? $report->client?->company_name ?? 'Cliente Removido') . ' · ' . $report->reference_month->translatedFormat('F/Y');
@endphp

@extends('layouts.admin')

@section('title', $reportTitle)
@section('page-title', $reportTitle)
@section('page-subtitle', $subtitle)

@section('topbar-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.relatorios.index') }}" class="btn-secondary btn-sm">
            <x-heroicon-o-arrow-left class="w-4 h-4" /> Voltar
        </a>
    </div>
@endsection

@section('content')
    @include('admin.reports.form')
@endsection