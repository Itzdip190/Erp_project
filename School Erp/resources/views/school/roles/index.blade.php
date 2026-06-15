@extends('layouts.app')

@section('title', 'Role Category')
@section('page-title', 'Role Category')

@section('content')
<div class="card">
    <div class="card-hdr">
        <h3>Role Directory</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Display Name</th>
                        <th>Guard Name</th>
                        <th>User Count</th>
                        <th>Permissions Overview</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rolesData as $role)
                    <tr>
                        <td><span style="font-family:monospace; font-weight:700; color:var(--navy);">{{ $role['name'] }}</span></td>
                        <td><strong>{{ $role['display_name'] }}</strong></td>
                        <td><span class="badge badge-blue">{{ $role['guard'] }}</span></td>
                        <td><strong style="color:var(--gold);">{{ $role['user_count'] }}</strong> users</td>
                        <td><span style="font-size:12px; color:var(--t2);">{{ $role['description'] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
