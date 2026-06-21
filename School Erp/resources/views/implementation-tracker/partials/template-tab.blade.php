<form class="impl-tab-form" enctype="multipart/form-data" method="POST" onsubmit="return false;">
    @csrf
    <div class="impl-table-container">
        <table class="impl-table">
            <thead>
                <tr>
                    <th style="width: 3%; text-align: center;">#</th>
                    <th style="width: 13%;">Template Name</th>
                    <th style="width: 9%;">Important Dates</th>
                    <th style="width: 9%;">Received Doc</th>
                    <th style="width: 8%;">Uploaded By</th>
                    <th style="width: 9%;">Received On</th>
                    <th style="width: 9%;">Implemented Doc</th>
                    <th style="width: 8%;">Uploaded By</th>
                    <th style="width: 9%;">Implemented On</th>
                    <th style="width: 9%;">Owner (School)</th>
                    <th style="width: 8%;">Confirmation</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 8%;">Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tempImpl as $index => $row)
                    <tr>
                        {{-- Row Index --}}
                        <td style="text-align: center; font-weight: 700; color: #64748b;">
                            {{ $index + 1 }}
                        </td>

                        {{-- Template Name --}}
                        <td>
                            <strong>{{ $row->template_name }}</strong>
                        </td>

                        {{-- Important Dates --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->important_dates ? $row->important_dates->format('d/m/Y') : '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][important_dates]" class="impl-edit-input" 
                                       value="{{ $row->important_dates ? $row->important_dates->format('d/m/Y') : '' }}" 
                                       placeholder="DD/MM/YYYY">
                            </div>
                        </td>

                        {{-- Received Doc --}}
                        <td>
                            <div class="impl-read-mode">
                                @php
                                    $files = json_decode($row->template_received_attachment, true) ?: [];
                                @endphp
                                @forelse($files as $file)
                                    <div class="impl-file-wrapper">
                                        <a href="{{ Storage::disk('public')->url($file['path']) }}" target="_blank" class="impl-file-link">
                                            <i class="fas fa-file-alt"></i> {{ Str::limit($file['name'], 12) }}
                                        </a>
                                    </div>
                                @empty
                                    -
                                @endforelse
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="file" name="template_received_attachment[{{ $row->id }}]" class="impl-edit-input" style="font-size:11px;">
                            </div>
                        </td>

                        {{-- Uploaded By 1 --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->uploaded_by_1 ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <span style="font-size:11px; color:#64748b;">Auto-fills</span>
                            </div>
                        </td>

                        {{-- Received On --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->template_received_on ? $row->template_received_on->format('d/m/Y, H:i') : '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <span style="font-size:11px; color:#64748b;">Auto-fills</span>
                            </div>
                        </td>

                        {{-- Implemented Doc --}}
                        <td>
                            <div class="impl-read-mode">
                                @php
                                    $files = json_decode($row->implemented_template_attachment, true) ?: [];
                                @endphp
                                @forelse($files as $file)
                                    <div class="impl-file-wrapper">
                                        <a href="{{ Storage::disk('public')->url($file['path']) }}" target="_blank" class="impl-file-link">
                                            <i class="fas fa-file-alt"></i> {{ Str::limit($file['name'], 12) }}
                                        </a>
                                    </div>
                                @empty
                                    -
                                @endforelse
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="file" name="implemented_template_attachment[{{ $row->id }}]" class="impl-edit-input" style="font-size:11px;">
                            </div>
                        </td>

                        {{-- Uploaded By 2 --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->uploaded_by_2 ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <span style="font-size:11px; color:#64748b;">Auto-fills</span>
                            </div>
                        </td>

                        {{-- Implemented On --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->template_implemented_on ? $row->template_implemented_on->format('d/m/Y, H:i') : '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <span style="font-size:11px; color:#64748b;">Auto-fills</span>
                            </div>
                        </td>

                        {{-- Owner School Side --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->owner_school_side ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <select name="rows[{{ $row->id }}][owner_school_side]" class="impl-edit-select">
                                    <option value="">- Select -</option>
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->full_name }}" {{ $row->owner_school_side === $staff->full_name ? 'selected' : '' }}>
                                            {{ $staff->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </td>

                        {{-- Confirmation School Side --}}
                        <td class="impl-cell-confirm {{ empty($row->confirmation_school_side) || $row->confirmation_school_side !== 'Confirmed' ? 'pending' : 'confirmed' }}">
                            <div class="impl-read-mode">
                                {{ $row->confirmation_school_side ?: 'Not Confirmed' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <select name="rows[{{ $row->id }}][confirmation_school_side]" class="impl-edit-select">
                                    <option value="" {{ empty($row->confirmation_school_side) ? 'selected' : '' }}>Awaiting Input</option>
                                    <option value="Not Confirmed" {{ $row->confirmation_school_side === 'Not Confirmed' ? 'selected' : '' }}>Not Confirmed</option>
                                    <option value="Confirmed" {{ $row->confirmation_school_side === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                </select>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td>
                            <div class="impl-read-mode">
                                <span class="impl-badge {{ strtolower($row->status === 'Completed' ? 'completed' : ($row->status === 'In Progress' ? 'progress' : 'pending')) }}">
                                    {{ $row->status }}
                                </span>
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <select name="rows[{{ $row->id }}][status]" class="impl-edit-select">
                                    <option value="Pending" {{ $row->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="In Progress" {{ $row->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Completed" {{ $row->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                        </td>

                        {{-- Comment --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->comment ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][comment]" class="impl-edit-input" value="{{ $row->comment }}" placeholder="Add details">
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>
