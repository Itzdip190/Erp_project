@extends('layouts.app')

@section('page-title', 'Card Template Creator')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-id-card-alt" style="color:var(--gold);margin-right:8px;"></i>Card Template Creator</h1>
        <p>Design customizable layouts for Student ID Cards, Bus Passes, and Exam Admit Cards</p>
    </div>
</div>

<div class="grid-3">
    <!-- Creator Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Create Template</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.cards.template-creator') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Template Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Standard ID Card Blue" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Card Type</label>
                    <select name="type" class="form-control" required>
                        <option value="id_card">Student ID Card</option>
                        <option value="bus_pass">Bus Pass</option>
                        <option value="admit_card">Exam Admit Card</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Background Color Hex</label>
                    <input type="color" name="background_color" class="form-control" value="#1a1f3c" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Text Color Hex</label>
                    <input type="color" name="text_color" class="form-control" value="#ffffff" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Layout Style</label>
                    <select name="layout_style" class="form-control" required>
                        <option value="classic">Classic Portrait</option>
                        <option value="minimal">Minimalist Landscape</option>
                        <option value="detailed">Detailed Double-sided</option>
                    </select>
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
                            <th>Card Type</th>
                            <th>Color Theme</th>
                            <th>Layout Style</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $tpl)
                        <tr>
                            <td><strong style="color:var(--navy);">{{ $tpl->name }}</strong></td>
                            <td>
                                @if($tpl->type === 'id_card')
                                    <span class="badge badge-success">ID Card</span>
                                @elseif($tpl->type === 'bus_pass')
                                    <span class="badge badge-blue">Bus Pass</span>
                                @else
                                    <span class="badge badge-purple">Admit Card</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span style="display:inline-block; width:15px; height:15px; border-radius:50%; background-color:{{ $tpl->background_color }}; border:1px solid var(--border);"></span>
                                    <span style="font-size:11px; font-family:monospace;">{{ $tpl->background_color }}</span>
                                </div>
                            </td>
                            <td><span style="text-transform:capitalize;">{{ $tpl->layout_style }}</span></td>
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
