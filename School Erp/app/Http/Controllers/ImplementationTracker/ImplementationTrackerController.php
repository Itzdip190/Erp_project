<?php

namespace App\Http\Controllers\ImplementationTracker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Staff;
use App\Models\ImplementationTracker\DataImplementation;
use App\Models\ImplementationTracker\TemplateImplementation;
use App\Models\ImplementationTracker\Integration;
use App\Models\ImplementationTracker\Training;
use App\Models\ImplementationTracker\ImplActivityLog;

class ImplementationTrackerController extends Controller
{
    /**
     * Show the tracker dashboard.
     */
    public function index()
    {
        $school = Auth::user()->school;
        $schoolId = $school->id;

        // Fetch lists for all 4 tabs scoped to the school
        $dataImpl = DataImplementation::where('school_id', $schoolId)->get();
        $tempImpl = TemplateImplementation::where('school_id', $schoolId)->get();
        $integrations = Integration::where('school_id', $schoolId)->get();
        $trainings = Training::where('school_id', $schoolId)->get();

        // Fetch staff members for owner dropdown list
        $staffMembers = Staff::where('school_id', $schoolId)->where('is_active', true)->get();

        // Get UDISE data
        $udise = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);

        return view('implementation-tracker.index', compact(
            'school', 'udise', 'dataImpl', 'tempImpl', 'integrations', 'trainings', 'staffMembers'
        ));
    }

    /**
     * Update all rows for a specific tab.
     */
    public function update(Request $request, string $tab)
    {
        $schoolId = Auth::user()->school_id;
        $userName = Auth::user()->name;
        $rowsData = $request->input('rows', []);
        
        // Allowed file limits from config
        $allowedExts = config('implementation_tracker.allowed_extensions', ['pdf', 'jpg', 'jpeg', 'png', 'xlsx', 'docx']);
        $maxSizeBytes = config('implementation_tracker.max_size_mb', 10) * 1024 * 1024;

        switch ($tab) {
            case 'data':
                foreach ($rowsData as $rowId => $data) {
                    $row = DataImplementation::where('school_id', $schoolId)->findOrFail($rowId);
                    
                    // Parse Dates
                    $receivedDate = !empty($data['data_received_date']) ? $this->parseDateTime($data['data_received_date']) : null;
                    $implementedDate = !empty($data['data_implemented_on']) ? $this->parseDateTime($data['data_implemented_on']) : null;

                    // Calculate TAT
                    $tat = $data['tat'] ?? null;
                    if ($receivedDate && $implementedDate && empty($tat)) {
                        $tat = (int) round($receivedDate->diffInDays($implementedDate)) . ' days';
                    }

                    // Handle uploads
                    $attachments = json_decode($row->attachments, true) ?: [];
                    if ($request->hasFile("files.{$rowId}")) {
                        foreach ($request->file("files.{$rowId}") as $file) {
                            if ($file->isValid() && $file->getSize() <= $maxSizeBytes && in_array(strtolower($file->getClientOriginalExtension()), $allowedExts)) {
                                $path = $file->store("implementation-tracker/data/{$rowId}", 'public');
                                $attachments[] = [
                                    'path' => $path,
                                    'name' => $file->getClientOriginalName()
                                ];
                                $row->uploaded_by = $userName;
                            }
                        }
                    }

                    // Auto status
                    $confirm = $data['confirmation_school_side'] ?? '';
                    $status = $confirm === 'Confirmed' ? 'Completed' : ($data['status'] ?? 'Pending');

                    $row->update([
                        'data_received_date'       => $receivedDate,
                        'data_implemented_on'     => $implementedDate,
                        'tat'                      => $tat,
                        'owner_school_side'        => $data['owner_school_side'] ?? null,
                        'confirmation_school_side' => $confirm ?: null,
                        'status'                   => $status,
                        'comment'                  => $data['comment'] ?? null,
                        'attachments'              => !empty($attachments) ? json_encode($attachments) : null,
                    ]);
                }
                break;

            case 'template':
                foreach ($rowsData as $rowId => $data) {
                    $row = TemplateImplementation::where('school_id', $schoolId)->findOrFail($rowId);
                    
                    $impDate = !empty($data['important_dates']) ? $this->parseDate($data['important_dates']) : null;
                    
                    // Template Received Upload
                    $receivedAttachment = json_decode($row->template_received_attachment, true) ?: [];
                    if ($request->hasFile("template_received_attachment.{$rowId}")) {
                        $file = $request->file("template_received_attachment.{$rowId}");
                        if ($file->isValid() && $file->getSize() <= $maxSizeBytes && in_array(strtolower($file->getClientOriginalExtension()), $allowedExts)) {
                            $path = $file->store("implementation-tracker/template/received/{$rowId}", 'public');
                            $receivedAttachment[] = [
                                'path' => $path,
                                'name' => $file->getClientOriginalName()
                            ];
                            $row->uploaded_by_1 = $userName;
                            $row->template_received_on = now();
                        }
                    }

                    // Template Implemented Upload
                    $implementedAttachment = json_decode($row->implemented_template_attachment, true) ?: [];
                    if ($request->hasFile("implemented_template_attachment.{$rowId}")) {
                        $file = $request->file("implemented_template_attachment.{$rowId}");
                        if ($file->isValid() && $file->getSize() <= $maxSizeBytes && in_array(strtolower($file->getClientOriginalExtension()), $allowedExts)) {
                            $path = $file->store("implementation-tracker/template/implemented/{$rowId}", 'public');
                            $implementedAttachment[] = [
                                'path' => $path,
                                'name' => $file->getClientOriginalName()
                            ];
                            $row->uploaded_by_2 = $userName;
                            $row->template_implemented_on = now();
                        }
                    }

                    $confirm = $data['confirmation_school_side'] ?? '';
                    $status = $confirm === 'Confirmed' ? 'Completed' : ($data['status'] ?? 'Pending');

                    $row->update([
                        'important_dates'                 => $impDate,
                        'owner_school_side'               => $data['owner_school_side'] ?? null,
                        'confirmation_school_side'        => $confirm ?: null,
                        'status'                          => $status,
                        'comment'                         => $data['comment'] ?? null,
                        'template_received_attachment'    => !empty($receivedAttachment) ? json_encode($receivedAttachment) : null,
                        'implemented_template_attachment' => !empty($implementedAttachment) ? json_encode($implementedAttachment) : null,
                    ]);
                }
                break;

            case 'integrations':
                foreach ($rowsData as $rowId => $data) {
                    $row = Integration::where('school_id', $schoolId)->findOrFail($rowId);
                    
                    $apiRecDate = !empty($data['api_received_on']) ? $this->parseDateTime($data['api_received_on']) : null;
                    $impDate = !empty($data['implemented_on']) ? $this->parseDateTime($data['implemented_on']) : null;

                    $tat = $data['tat'] ?? null;
                    if ($apiRecDate && $impDate && empty($tat)) {
                        $tat = (int) round($apiRecDate->diffInDays($impDate)) . ' days';
                    }

                    $confirm = $data['confirmation_school_side'] ?? '';
                    $status = $confirm === 'Confirmed' ? 'Completed' : ($data['status'] ?? 'Pending');

                    $row->update([
                        'company'                  => $data['company'] ?? null,
                        'serial_number'            => $data['serial_number'] ?? null,
                        'vendor_contact_details'   => $data['vendor_contact_details'] ?? null,
                        'api_received_on'          => $apiRecDate,
                        'implemented_on'           => $impDate,
                        'tat'                      => $tat,
                        'owner_school_side'        => $data['owner_school_side'] ?? null,
                        'confirmation_school_side' => $confirm ?: null,
                        'status'                   => $status,
                        'comment'                  => $data['comment'] ?? null,
                    ]);
                }
                break;

            case 'training':
                foreach ($rowsData as $rowId => $data) {
                    $row = Training::where('school_id', $schoolId)->findOrFail($rowId);
                    
                    $doneDate = !empty($data['training_done_on']) ? $this->parseDateTime($data['training_done_on']) : null;

                    $attachments = json_decode($row->attachments, true) ?: [];
                    if ($request->hasFile("files.{$rowId}")) {
                        foreach ($request->file("files.{$rowId}") as $file) {
                            if ($file->isValid() && $file->getSize() <= $maxSizeBytes && in_array(strtolower($file->getClientOriginalExtension()), $allowedExts)) {
                                $path = $file->store("implementation-tracker/training/{$rowId}", 'public');
                                $attachments[] = [
                                    'path' => $path,
                                    'name' => $file->getClientOriginalName()
                                ];
                                $row->uploaded_by = $userName;
                            }
                        }
                    }

                    $confirm = $data['confirmation_school_side'] ?? '';
                    $status = $confirm === 'Confirmed' ? 'Completed' : ($data['status'] ?? 'Pending');

                    // Given to handles string or array input (e.g. staff names/IDs)
                    $givenTo = $data['training_given_to'] ?? null;
                    if (is_array($givenTo)) {
                        $givenTo = json_encode($givenTo);
                    }

                    $row->update([
                        'training_done_on'         => $doneDate,
                        'training_given_to'        => $givenTo,
                        'minutes_of_meeting'       => $data['minutes_of_meeting'] ?? null,
                        'owner_school_side'        => $data['owner_school_side'] ?? null,
                        'confirmation_school_side' => $confirm ?: null,
                        'status'                   => $status,
                        'comment'                  => $data['comment'] ?? null,
                        'attachments'              => !empty($attachments) ? json_encode($attachments) : null,
                    ]);
                }
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully!'
        ]);
    }

    /**
     * Get audit logs for the logs modal.
     */
    public function logs()
    {
        $schoolId = Auth::user()->school_id;
        $logs = ImplActivityLog::where('school_id', $schoolId)
            ->orderBy('changed_at', 'desc')
            ->take(150)
            ->get()
            ->map(function ($log) {
                return [
                    'tab_name' => $log->tab_name,
                    'row_reference' => $log->row_reference,
                    'field_changed' => $log->field_changed,
                    'old_value' => $log->old_value ?: '-',
                    'new_value' => $log->new_value ?: '-',
                    'changed_by' => $log->changed_by,
                    'changed_at' => $log->changed_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json($logs);
    }

    /**
     * Helper to parse datetime (expected format: DD/MM/YYYY, HH:mm or YYYY-MM-DD HH:mm).
     */
    private function parseDateTime($val)
    {
        if (empty($val)) return null;
        try {
            if (str_contains($val, ',')) {
                return Carbon::createFromFormat('d/m/Y, H:i', trim($val));
            }
            return Carbon::parse($val);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper to parse date (expected format: DD/MM/YYYY or YYYY-MM-DD).
     */
    private function parseDate($val)
    {
        if (empty($val)) return null;
        try {
            if (str_contains($val, '/')) {
                return Carbon::createFromFormat('d/m/Y', trim($val));
            }
            return Carbon::parse($val);
        } catch (\Exception $e) {
            return null;
        }
    }
}
