@extends('layouts.app')

@section('page-title', 'Fee Configuration')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-gears" style="color:var(--gold);margin-right:8px;"></i>Fee Configuration</h1>
        <p>Set up fee categories, structures, and mapping defaults for the institution</p>
    </div>
</div>

<div class="grid-3">
    <!-- Add Category Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Add Fee Category</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.fees.configuration') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Science Lab Fee, Hostel Fee" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" style="height:100px;" placeholder="Brief details about what this fee covers..."></textarea>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus-circle"></i> Add Category
                </button>
            </form>
        </div>
    </div>

    <!-- Category Directory -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Fee Categories Directory</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fee Category Name</th>
                            <th>Description</th>
                            <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td><strong>#{{ $category->id }}</strong></td>
                            <td><strong style="color:var(--navy);">{{ $category->name }}</strong></td>
                            <td><span style="color:var(--t2);">{{ $category->description ?? 'No description provided.' }}</span></td>
                            <td>{{ $category->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px; color:var(--t3);">No fee categories defined.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
