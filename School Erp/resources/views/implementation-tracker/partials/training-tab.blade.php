<form class="impl-tab-form" enctype="multipart/form-data" method="POST" onsubmit="return false;">
    @csrf
    <div class="impl-table-container">
        <table class="impl-table">
            <thead>
                <tr>
                    <th style="width: 3%; text-align: center;">#</th>
                    <th style="width: 13%;">Module Name</th>
                    <th style="width: 10%;">Training Done On</th>
                    <th style="width: 12%;">Given To</th>
                    <th style="width: 12%;">Minutes of Meeting</th>
                    <th style="width: 10%;">Upload</th>
                    <th style="width: 8%;">Uploaded By</th>
                    <th style="width: 10%;">Owner (School)</th>
                    <th style="width: 8%;">Confirmation</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 8%;">Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trainings as $index => $row)
                    <tr>
                        {{-- Row Index --}}
                        <td style="text-align: center; font-weight: 700; color: #64748b;">
                            {{ $index + 1 }}
                        </td>

                        {{-- Module Name --}}
                        <td>
                            <strong>{{ $row->module_name }}</strong>
                        </td>

                        {{-- Training Done On --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->training_done_on ? $row->training_done_on->format('d/m/Y, H:i') : '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][training_done_on]" class="impl-edit-input" 
                                       value="{{ $row->training_done_on ? $row->training_done_on->format('d/m/Y, H:i') : '' }}" 
                                       placeholder="DD/MM/YYYY, hh:mm">
                            </div>
                        </td>

                        {{-- Given To --}}
                        <td>
                            @php
                                $givenToArray = json_decode($row->training_given_to, true);
                                if (!is_array($givenToArray)) {
                                    $givenToArray = $row->training_given_to ? [$row->training_given_to] : [];
                                }
                            @endphp
                            <div class="impl-read-mode">
                                {{ implode(', ', $givenToArray) ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <select name="rows[{{ $row->id }}][training_given_to][]" class="impl-edit-select" multiple style="height: 70px; padding: 4px;">
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->full_name }}" {{ in_array($staff->full_name, $givenToArray) ? 'selected' : '' }}>
                                            {{ $staff->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </td>

                        {{-- Minutes of Meeting --}}
                        <td>
                            <div class="impl-read-mode" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $row->minutes_of_meeting ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][minutes_of_meeting]" class="impl-edit-input" value="{{ $row->minutes_of_meeting }}" placeholder="MoM text">
                            </div>
                        </td>

                        {{-- Upload / Attachments --}}
                        <td>
                            <div class="impl-read-mode">
                                @php
                                    $files = json_decode($row->attachments, true) ?: [];
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
                                <input type="file" name="files[{{ $row->id }}][]" class="impl-edit-input" multiple style="font-size:11px;">
                            </div>
                        </td>

                        {{-- Uploaded By --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->uploaded_by ?: '-' }}
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
