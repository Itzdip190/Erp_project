<form class="impl-tab-form" enctype="multipart/form-data" method="POST" onsubmit="return false;">
    @csrf
    <div class="impl-table-container">
        <table class="impl-table">
            <thead>
                <tr>
                    <th style="width: 4%; text-align: center;">#</th>
                    <th style="width: 14%;">Module Name</th>
                    <th style="width: 12%;">Attachments</th>
                    <th style="width: 8%;">Uploaded By</th>
                    <th style="width: 10%;">Data Received Date</th>
                    <th style="width: 10%;">Data Implemented On</th>
                    <th style="width: 6%;">TAT</th>
                    <th style="width: 10%;">Owner (School)</th>
                    <th style="width: 8%;">Confirmation</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 10%;">Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataImpl as $index => $row)
                    <tr>
                        {{-- Row Index --}}
                        <td style="text-align: center; font-weight: 700; color: #64748b;">
                            {{ $index + 1 }}
                        </td>

                        {{-- Module Name --}}
                        <td>
                            <strong>{{ $row->module_name }}</strong>
                        </td>

                        {{-- Attachments --}}
                        <td>
                            <div class="impl-read-mode">
                                @php
                                    $files = json_decode($row->attachments, true) ?: [];
                                @endphp
                                @forelse($files as $file)
                                    <div class="impl-file-wrapper">
                                        <a href="{{ Storage::disk('public')->url($file['path']) }}" target="_blank" class="impl-file-link">
                                            <i class="fas fa-file-alt"></i> {{ Str::limit($file['name'], 15) }}
                                        </a>
                                    </div>
                                @empty
                                    -
                                @endforelse
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="file" name="files[{{ $row->id }}][]" class="impl-edit-input" multiple style="font-size:11px;">
                            </div>
                        </td>

                        {{-- Uploaded By --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->uploaded_by ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <span style="font-size:12px; color:#64748b;">Auto-fills</span>
                            </div>
                        </td>

                        {{-- Data Received Date --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->data_received_date ? $row->data_received_date->format('d/m/Y, H:i') : '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][data_received_date]" class="impl-edit-input" 
                                       value="{{ $row->data_received_date ? $row->data_received_date->format('d/m/Y, H:i') : '' }}" 
                                       placeholder="DD/MM/YYYY, hh:mm">
                            </div>
                        </td>

                        {{-- Data Implemented On --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->data_implemented_on ? $row->data_implemented_on->format('d/m/Y, H:i') : '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][data_implemented_on]" class="impl-edit-input" 
                                       value="{{ $row->data_implemented_on ? $row->data_implemented_on->format('d/m/Y, H:i') : '' }}" 
                                       placeholder="DD/MM/YYYY, hh:mm">
                            </div>
                        </td>

                        {{-- TAT --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->tat ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][tat]" class="impl-edit-input" 
                                       value="{{ $row->tat }}" placeholder="Auto or override">
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
