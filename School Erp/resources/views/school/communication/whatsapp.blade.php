@extends('layouts.app')

@section('page-title', 'WhatsApp Campaign Manager')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fab fa-whatsapp" style="color:var(--gold);margin-right:8px;"></i>WhatsApp Campaign Manager</h1>
        <p>Send richer newsletters and media announcements using WhatsApp Business API triggers</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>Create WhatsApp Messaging Campaign</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.communication.whatsapp') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Recipient Segment</label>
                <select class="form-control" name="target">
                    <option value="parents">All Registered Guardians</option>
                    <option value="staff">All Staff & Faculty</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">WhatsApp Template Name (Approved)</label>
                <select class="form-control" name="template">
                    <option value="newsletter_june">june_parent_newsletter (Includes PDF attachment)</option>
                    <option value="admission_welcome">admission_welcome_kit</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Optional Media URL Attachment</label>
                <input type="text" class="form-control" name="media_url" placeholder="e.g. https://schoolcloud.erp/storage/kit.pdf">
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                <i class="fab fa-whatsapp"></i> Dispatch WhatsApp Campaign
            </button>
        </form>
    </div>
</div>
@endsection
