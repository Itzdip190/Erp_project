@extends('layouts.app')

@section('page-title', 'Certificate Template Creator')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-certificate" style="color:var(--gold);margin-right:8px;"></i>Certificate Template Creator</h1>
        <p>Design official templates for Transfer Certificates, Character Certificates, or Custom Merit Awards</p>
    </div>
</div>

<div class="grid-3">
    <!-- Creator Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Create Template</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.certificates.template-creator') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Template Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Merit Award Gold" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Certificate Type</label>
                    <select name="type" class="form-control" required>
                        <option value="transfer">School Leaving / Transfer Certificate</option>
                        <option value="character">Character Certificate</option>
                        <option value="custom">Custom Merit / Sports Award</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Header Title Text</label>
                    <input type="text" name="title_text" class="form-control" placeholder="e.g. CERTIFICATE OF EXCELLENCE" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Body Text (HTML/Placeholders allowed)</label>
                    <textarea name="body_text" class="form-control" style="height:120px;" placeholder="Use variables: [Student_Name], [Parent_Name], [Admission_ID], [Grade_Class]" required></textarea>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-magic"></i> Generate Template
                </button>
            </form>
        </div>
    </div>

    <!-- Active Templates List -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Available Design Templates</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Template Name</th>
                            <th>Type</th>
                            <th>Header Title</th>
                            <th>Body Text Preview</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $tpl)
                        <tr>
                            <td><strong style="color:var(--navy);">{{ $tpl->name }}</strong></td>
                            <td>
                                @if($tpl->type === 'transfer')
                                    <span class="badge badge-danger">Transfer</span>
                                @elseif($tpl->type === 'character')
                                    <span class="badge badge-success">Character</span>
                                @else
                                    <span class="badge badge-blue">Custom</span>
                                @endif
                            </td>
                            <td><strong style="color:var(--navy); font-size:12px;">{{ $tpl->title_text }}</strong></td>
                            <td><span style="color:var(--t2); font-size:11.5px;">{{ substr($tpl->body_text, 0, 70) }}...</span></td>
                            <td>{{ $tpl->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px; color:var(--t3);">No templates design logged.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
