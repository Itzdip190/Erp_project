<form class="impl-tab-form" enctype="multipart/form-data" method="POST" onsubmit="return false;">
    @csrf
    <div class="impl-table-container">
        <table class="impl-table">
            <thead>
                <tr>
                    <th style="width: 3%; text-align: center;">#</th>
                    <th style="width: 13%;">Integration Name</th>
                    <th style="width: 10%;">Company</th>
                    <th style="width: 10%;">Serial Number</th>
                    <th style="width: 10%;">Vendor Contact</th>
                    <th style="width: 10%;">API Rec On</th>
                    <th style="width: 10%;">Implemented On</th>
                    <th style="width: 6%;">TAT</th>
                    <th style="width: 10%;">Owner (School)</th>
                    <th style="width: 8%;">Confirmation</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 12%;">Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($integrations as $index => $row)
                    <tr>
                        {{-- Row Index --}}
                        <td style="text-align: center; font-weight: 700; color: #64748b;">
                            {{ $index + 1 }}
                        </td>

                        {{-- Integration Name --}}
                        <td>
                            <strong>{{ $row->integration_name }}</strong>
                        </td>

                        {{-- Company --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->company ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][company]" class="impl-edit-input" value="{{ $row->company }}" placeholder="Company">
                            </div>
                        </td>

                        {{-- Serial Number --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->serial_number ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][serial_number]" class="impl-edit-input" value="{{ $row->serial_number }}" placeholder="S/N">
                            </div>
                        </td>

                        {{-- Vendor Contact --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->vendor_contact_details ?: '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][vendor_contact_details]" class="impl-edit-input" value="{{ $row->vendor_contact_details }}" placeholder="Contact Details">
                            </div>
                        </td>

                        {{-- API Rec On --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->api_received_on ? $row->api_received_on->format('d/m/Y, H:i') : '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][api_received_on]" class="impl-edit-input" 
                                       value="{{ $row->api_received_on ? $row->api_received_on->format('d/m/Y, H:i') : '' }}" 
                                       placeholder="DD/MM/YYYY, hh:mm">
                            </div>
                        </td>

                        {{-- Implemented On --}}
                        <td>
                            <div class="impl-read-mode">
                                {{ $row->implemented_on ? $row->implemented_on->format('d/m/Y, H:i') : '-' }}
                            </div>
                            <div class="impl-edit-mode" style="display: none;">
                                <input type="text" name="rows[{{ $row->id }}][implemented_on]" class="impl-edit-input" 
                                       value="{{ $row->implemented_on ? $row->implemented_on->format('d/m/Y, H:i') : '' }}" 
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
