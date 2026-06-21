@extends('layouts.app')

@section('title', 'Implementation Process')
@section('page-title', 'Implementation Process')

@section('styles')
@php
    $assetPrefix = str_contains(request()->getHost(), 'hostinger') ? 'public/' : '';
@endphp
<link rel="stylesheet" href="{{ asset($assetPrefix . 'css/implementation-tracker.css') }}">
@endsection

@section('content')
<div class="impl-page">

    {{-- Tabs Shell --}}
    <div class="impl-wrapper">
        <div class="impl-tabs-row">
            <ul class="impl-tabs-list">
                <li><button class="impl-tab-btn active" data-tab="data">Data Implementation</button></li>
                <li><button class="impl-tab-btn" data-tab="template">Template Implementation</button></li>
                <li><button class="impl-tab-btn" data-tab="integrations">Integrations</button></li>
                <li><button class="impl-tab-btn" data-tab="training">Training</button></li>
            </ul>
            <div class="impl-actions-hdr">
                <button class="impl-btn impl-btn-logs" id="btn-show-logs">
                    <i class="fas fa-list-alt"></i> SHOW LOGS
                </button>
                <button class="impl-btn impl-btn-edit" id="btn-edit-mode">
                    <i class="fas fa-edit"></i> EDIT
                </button>
                <button class="impl-btn impl-btn-update" id="btn-update-tracker" disabled>
                    <i class="fas fa-save"></i> UPDATE
                </button>
            </div>
        </div>

        {{-- Tab 1: Data --}}
        <div class="impl-tab-content active" id="tab-data">
            @include('implementation-tracker.partials.data-tab')
        </div>

        {{-- Tab 2: Template --}}
        <div class="impl-tab-content" id="tab-template">
            @include('implementation-tracker.partials.template-tab')
        </div>

        {{-- Tab 3: Integrations --}}
        <div class="impl-tab-content" id="tab-integrations">
            @include('implementation-tracker.partials.integrations-tab')
        </div>

        {{-- Tab 4: Training --}}
        <div class="impl-tab-content" id="tab-training">
            @include('implementation-tracker.partials.training-tab')
        </div>

        {{-- Guidelines Footer Link --}}
        <div style="text-align: left; padding-top: 12px;">
            <span class="impl-guidelines-link" onclick="openModal('modal-guidelines')">
                <i class="fas fa-info-circle"></i> File Upload Guidelines
            </span>
        </div>
    </div>

    {{-- Include Modals --}}
    @include('implementation-tracker.partials.logs-modal')
    @include('implementation-tracker.partials.upload-guidelines-modal')

</div>
@endsection

@section('scripts')
<script src="{{ asset(($assetPrefix ?? '') . 'js/implementation-tracker.js') }}"></script>
@endsection
