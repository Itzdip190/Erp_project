<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FrontDeskController extends Controller
{
    /**
     * Helper to get active school ID.
     */
    protected function getSchoolId(): ?int
    {
        if (Auth::check() && Auth::user()->school_id) {
            return Auth::user()->school_id;
        }

        if (app()->bound('currentSchool')) {
            return app('currentSchool')?->id;
        }

        return request()->route('school')?->id;
    }

    /**
     * Display Visitor Registration page.
     */
    public function visitorRegistration(Request $request)
    {
        $schoolId = $this->getSchoolId();

        // Fetch staff members for host suggestions
        $staffMembers = Staff::where('school_id', $schoolId)
            ->select('id', 'first_name', 'last_name', 'designation_id')
            ->with(['designation:id,name'])
            ->orderBy('first_name')
            ->limit(100)
            ->get();

        // Preset visitor types
        $visitorTypes = [
            'Parent / Guardian',
            'Vendor / Supplier',
            'Guest / Dignitary',
            'Contractor / Maintenance',
            'Job Applicant',
            'Alumni',
            'Official / Inspector',
            'Relative / Friend',
            'Other Official',
        ];

        // Preset Whom to Meet categories
        $whomToMeetTypes = [
            'Principal / Management',
            'Teacher / Faculty',
            'Administration / Front Desk',
            'Student',
            'Accounts / Fee Counter',
            'Sports / Transport',
            'Hostel Warden',
            'Other Staff',
        ];

        // Preset Purposes
        $visitPurposes = [
            'Parent-Teacher Interaction',
            'Fee Payment / Accounts Enquiry',
            'New Admission Enquiry',
            'Official Meeting / Inspection',
            'Vendor Delivery / Maintenance',
            'Document Submission / Verification',
            'Student Pickup / Early Departure',
            'Job Interview',
            'Personal / Casual Visit',
            'Other',
        ];

        // Preset Security Gates
        $securityGates = [
            'Main Gate 1',
            'Gate No. 2 (North)',
            'Gate No. 3 (Rear)',
            'Reception Entry',
            'Administrative Block Gate',
            'Junior Wing Gate',
        ];

        // Preset ID Proof Types
        $idProofTypes = [
            'Aadhaar Card',
            'PAN Card',
            'Driving License',
            'Voter ID',
            'Passport',
            'Employee ID',
            'School / College ID',
            'Other Government ID',
        ];

        $registeredVisitor = null;
        if (session('registered_visitor_id')) {
            $registeredVisitor = Visitor::where('school_id', $schoolId)->find(session('registered_visitor_id'));
        }

        return view('school.front_desk.visitor_registration', compact(
            'staffMembers',
            'visitorTypes',
            'whomToMeetTypes',
            'visitPurposes',
            'securityGates',
            'idProofTypes',
            'registeredVisitor'
        ));
    }

    /**
     * Store a newly registered visitor.
     */
    public function storeVisitorRegistration(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $validated = $request->validate([
            'visitor_type'             => 'required|string|max:100',
            'full_name'                => 'required|string|max:150',
            'gender'                   => 'nullable|string|in:Male,Female,Other',
            'dob'                      => 'nullable|date',
            'mobile_number'            => ['required', 'string', 'max:25'],
            'alternate_mobile'         => 'nullable|string|max:25',
            'email'                    => 'nullable|email|max:150',
            'street_address'           => 'nullable|string|max:500',
            'state'                    => 'nullable|string|max:100',
            'city'                     => 'nullable|string|max:100',
            'pincode'                  => 'nullable|string|max:20',
            'whom_to_meet_type'        => 'required|string|max:100',
            'host_name'                => 'nullable|string|max:150',
            'security_gate'            => 'required|string|max:100',
            'entourage_count'          => 'required|integer|min:1|max:100',
            'visit_purpose'            => 'required|string|max:150',
            'detailed_purpose_remarks' => 'nullable|string|max:1000',
            'id_proof_type'            => 'nullable|string|max:100',
            'id_proof_number'          => 'nullable|string|max:100',
            'vehicle_number'           => 'nullable|string|max:50',
            'security_notes'           => 'nullable|string|max:1000',
            'photo'                    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'webcam_photo'             => 'nullable|string',
        ]);

        // Process photo (file upload or webcam base64 snapshot)
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'visitor_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs("visitors/{$schoolId}", $filename, 'public');
        } elseif ($request->filled('webcam_photo') && str_starts_with($request->input('webcam_photo'), 'data:image')) {
            $dataUrl = $request->input('webcam_photo');
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
                $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
                $type = strtolower($type[1]);
                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                    $type = 'jpg';
                }
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'visitor_cam_' . uniqid() . '.' . $type;
                    $relativePath = "visitors/{$schoolId}/" . $filename;
                    Storage::disk('public')->put($relativePath, $data);
                    $photoPath = $relativePath;
                }
            }
        }

        // Generate unique pass number
        $passNumber = Visitor::generatePassNumber($schoolId);

        // Fetch school branding snapshot
        $school = Auth::user()?->school ?? School::find($schoolId);
        $schoolLogo = null;
        if ($school?->logo) {
            $schoolLogo = str_starts_with($school->logo, 'http') ? $school->logo : asset('storage/' . ltrim($school->logo, '/'));
        }

        $metaData = [
            'school_name'        => $school?->name ?? config('app.name', 'SchoolCloud ERP'),
            'school_code'        => $school?->code ? strtoupper($school->code) : 'EDUZEN',
            'school_phone'       => $school?->phone ?? '',
            'school_address'     => $school?->address ?? '',
            'school_logo'        => $schoolLogo,
            'registered_by_name' => Auth::user()?->name ?? 'Front Desk Staff',
        ];

        // Active Academic Session if present
        $sessionId = session('current_session_id') ?? Auth::user()?->academic_session_id;

        $visitor = Visitor::create([
            'school_id'                => $schoolId,
            'academic_session_id'      => $sessionId,
            'registered_by'            => Auth::id(),
            'pass_number'              => $passNumber,
            'visitor_type'             => $validated['visitor_type'],
            'full_name'                => $validated['full_name'],
            'gender'                   => $validated['gender'] ?? null,
            'dob'                      => $validated['dob'] ?? null,
            'mobile_number'            => $validated['mobile_number'],
            'alternate_mobile'         => $validated['alternate_mobile'] ?? null,
            'email'                    => $validated['email'] ?? null,
            'street_address'           => $validated['street_address'] ?? null,
            'state'                    => $validated['state'] ?? null,
            'city'                     => $validated['city'] ?? null,
            'pincode'                  => $validated['pincode'] ?? null,
            'whom_to_meet_type'        => $validated['whom_to_meet_type'],
            'host_name'                => $validated['host_name'] ?? null,
            'security_gate'            => $validated['security_gate'],
            'entourage_count'          => $validated['entourage_count'] ?? 1,
            'visit_purpose'            => $validated['visit_purpose'],
            'detailed_purpose_remarks' => $validated['detailed_purpose_remarks'] ?? null,
            'id_proof_type'            => $validated['id_proof_type'] ?? null,
            'id_proof_number'          => $validated['id_proof_number'] ?? null,
            'vehicle_number'           => $validated['vehicle_number'] ?? null,
            'photo_path'               => $photoPath,
            'security_notes'           => $validated['security_notes'] ?? null,
            'status'                   => 'checked_in',
            'check_in_at'              => now(),
            'meta_data'                => $metaData,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => 'Visitor registered successfully! Pass No: ' . $passNumber,
                'visitor'     => $visitor,
                'photo_url'   => $visitor->photo_url,
                'school_logo' => $metaData['school_logo'],
                'print_url'   => route('school.front-desk.visitor.print', $visitor->id),
                'school_name' => $metaData['school_name'],
                'school_code' => $metaData['school_code'],
                'in_time'     => $visitor->check_in_at->format('d-M-Y h:i A'),
            ]);
        }

        return redirect()->route('school.front-desk.visitor-registration')
            ->with('success', 'Visitor registered successfully with Pass #' . $passNumber)
            ->with('registered_visitor_id', $visitor->id);
    }

    /**
     * Lookup repeat visitor info by mobile number for lightning fast registration.
     */
    public function lookupVisitorPhone(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $phone = trim($request->input('phone', ''));

        if (empty($phone) || strlen($phone) < 5) {
            return response()->json(['found' => false]);
        }

        $lastVisitor = Visitor::where('school_id', $schoolId)
            ->where(function ($q) use ($phone) {
                $q->where('mobile_number', $phone)
                  ->orWhere('mobile_number', 'LIKE', "%{$phone}");
            })
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastVisitor) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'   => true,
            'visitor' => [
                'visitor_type'     => $lastVisitor->visitor_type,
                'full_name'        => $lastVisitor->full_name,
                'gender'           => $lastVisitor->gender,
                'dob'              => $lastVisitor->dob ? $lastVisitor->dob->format('Y-m-d') : null,
                'alternate_mobile' => $lastVisitor->alternate_mobile,
                'email'            => $lastVisitor->email,
                'street_address'   => $lastVisitor->street_address,
                'state'            => $lastVisitor->state,
                'city'             => $lastVisitor->city,
                'pincode'          => $lastVisitor->pincode,
                'id_proof_type'    => $lastVisitor->id_proof_type,
                'id_proof_number'  => $lastVisitor->id_proof_number,
                'vehicle_number'   => $lastVisitor->vehicle_number,
                'photo_url'        => $lastVisitor->photo_url,
            ]
        ]);
    }

    /**
     * Scanner lookup endpoint (Barcode gun or QR camera scanner)
     */
    public function scanLookup(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $query = trim($request->input('query', ''));

        if (empty($query)) {
            return response()->json(['found' => false, 'message' => 'Please provide a pass number or barcode.']);
        }

        // Clean query if full URL was scanned
        if (str_contains($query, '/')) {
            $parts = explode('/', rtrim($query, '/'));
            $query = end($parts);
        }

        $visitor = Visitor::where('school_id', $schoolId)
            ->where(function($q) use ($query) {
                $q->where('pass_number', $query)
                  ->orWhere('pass_number', 'LIKE', "%{$query}")
                  ->orWhere('mobile_number', $query)
                  ->orWhere('id', $query);
            })
            ->orderBy('id', 'desc')
            ->first();

        if (!$visitor) {
            return response()->json(['found' => false, 'message' => 'No visitor found for scan code: ' . $query]);
        }

        return response()->json([
            'found'    => true,
            'visitor'  => $visitor,
            'in_time'  => $visitor->check_in_at ? $visitor->check_in_at->format('d-M-Y h:i A') : $visitor->created_at->format('d-M-Y h:i A'),
            'out_time' => $visitor->check_out_at ? $visitor->check_out_at->format('d-M-Y h:i A') : 'Still Inside',
            'print_url'=> route('school.front-desk.visitor.print', $visitor->id),
        ]);
    }

    /**
     * Process Check-Out when scanned at exit gate.
     */
    public function processCheckOut(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $passNumber = trim($request->input('pass_number', ''));
        $visitorId = $request->input('visitor_id');

        $visitor = Visitor::where('school_id', $schoolId)
            ->where(function($q) use ($passNumber, $visitorId) {
                if ($visitorId) {
                    $q->where('id', $visitorId);
                } else {
                    $q->where('pass_number', $passNumber)
                      ->orWhere('pass_number', 'LIKE', "%{$passNumber}");
                }
            })
            ->first();

        if (!$visitor) {
            return response()->json(['success' => false, 'message' => 'Visitor record not found.']);
        }

        if ($visitor->status === 'checked_out') {
            return response()->json([
                'success' => true,
                'already_out' => true,
                'message' => "Visitor {$visitor->full_name} ({$visitor->pass_number}) was ALREADY checked out at " . ($visitor->check_out_at ? $visitor->check_out_at->format('h:i A') : 'earlier') . '.',
                'visitor' => $visitor,
            ]);
        }

        $visitor->update([
            'status'       => 'checked_out',
            'check_out_at' => now(),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => "Visitor {$visitor->full_name} ({$visitor->pass_number}) CHECKED OUT successfully!",
            'visitor'  => $visitor,
            'out_time' => $visitor->check_out_at->format('d-M-Y h:i A'),
        ]);
    }

    /**
     * Process Check-In when scanned at entry gate.
     */
    public function processCheckIn(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $passNumber = trim($request->input('pass_number', ''));
        $visitorId = $request->input('visitor_id');

        $visitor = Visitor::where('school_id', $schoolId)
            ->where(function($q) use ($passNumber, $visitorId) {
                if ($visitorId) {
                    $q->where('id', $visitorId);
                } else {
                    $q->where('pass_number', $passNumber)
                      ->orWhere('pass_number', 'LIKE', "%{$passNumber}");
                }
            })
            ->first();

        if (!$visitor) {
            return response()->json(['success' => false, 'message' => 'Visitor record not found.']);
        }

        $visitor->update([
            'status'      => 'checked_in',
            'check_in_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Visitor {$visitor->full_name} ({$visitor->pass_number}) CHECKED IN successfully!",
            'visitor' => $visitor,
            'in_time' => $visitor->check_in_at->format('d-M-Y h:i A'),
        ]);
    }

    /**
     * Real-time live stats & visitor data polling for terminals.
     */
    public function liveData(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $todayVisitors = Visitor::where('school_id', $schoolId)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->get();

        $insideVisitors = $todayVisitors->where('status', 'checked_in')->values();
        $checkedOutVisitors = $todayVisitors->where('status', 'checked_out')->values();

        return response()->json([
            'total_today'       => $todayVisitors->count(),
            'inside_count'      => $insideVisitors->count(),
            'checked_out_count' => $checkedOutVisitors->count(),
            'inside_visitors'   => $insideVisitors->map(function ($v) {
                return [
                    'id'            => $v->id,
                    'pass_number'   => $v->pass_number,
                    'full_name'     => $v->full_name,
                    'visitor_type'  => $v->visitor_type,
                    'mobile_number' => $v->mobile_number,
                    'host_name'     => $v->host_name ?: $v->whom_to_meet_type,
                    'security_gate' => $v->security_gate,
                    'in_time'       => $v->check_in_at ? $v->check_in_at->format('h:i A') : $v->created_at->format('h:i A'),
                    'photo_url'     => $v->photo_url,
                    'print_url'     => route('school.front-desk.visitor.print', $v->id),
                ];
            }),
            'today_visitors'    => $todayVisitors->map(function ($v) {
                return [
                    'id'            => $v->id,
                    'pass_number'   => $v->pass_number,
                    'full_name'     => $v->full_name,
                    'visitor_type'  => $v->visitor_type,
                    'mobile_number' => $v->mobile_number,
                    'host_name'     => $v->host_name ?: $v->whom_to_meet_type,
                    'security_gate' => $v->security_gate,
                    'in_time'       => $v->check_in_at ? $v->check_in_at->format('h:i A') : $v->created_at->format('h:i A'),
                    'status'        => $v->status,
                    'photo_url'     => $v->photo_url,
                    'print_url'     => route('school.front-desk.visitor.print', $v->id),
                ];
            }),
        ]);
    }

    /**
     * Display printable visitor pass badge.
     */
    public function printVisitorPass(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $visitor = Visitor::where('school_id', $schoolId)->findOrFail($id);

        return view('school.front_desk.visitor_pass_print', compact('visitor'));
    }

    /**
     * Display Visitor Check-In page.
     */
    public function visitorCheckIn(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $todayVisitors = Visitor::where('school_id', $schoolId)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->get();

        $totalToday = $todayVisitors->count();
        $insideCount = $todayVisitors->where('status', 'checked_in')->count();
        $checkedOutCount = $todayVisitors->where('status', 'checked_out')->count();

        return view('school.front_desk.visitor_check_in', compact('todayVisitors', 'totalToday', 'insideCount', 'checkedOutCount'));
    }

    /**
     * Display Visitor Check-Out page.
     */
    public function visitorCheckOut(Request $request)
    {
        $schoolId = $this->getSchoolId();
        
        // Active visitors inside campus
        $insideVisitors = Visitor::where('school_id', $schoolId)
            ->where('status', 'checked_in')
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->get();

        // Checked-out visitors today
        $checkedOutVisitors = Visitor::where('school_id', $schoolId)
            ->where('status', 'checked_out')
            ->whereDate('created_at', today())
            ->orderBy('check_out_at', 'desc')
            ->get();

        $totalToday = Visitor::where('school_id', $schoolId)->whereDate('created_at', today())->count();
        $insideCount = $insideVisitors->count();
        $checkedOutCount = $checkedOutVisitors->count();

        return view('school.front_desk.visitor_check_out', compact('insideVisitors', 'checkedOutVisitors', 'totalToday', 'insideCount', 'checkedOutCount'));
    }

    /**
     * Display In-Out Report page.
     */
    public function inOutReport(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $query = Visitor::where('school_id', $schoolId);

        // Filter by Date Range (using check_in_at or created_at)
        if ($request->filled('from_date')) {
            try {
                $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
                $query->where(function ($q) use ($fromDate) {
                    $q->where('check_in_at', '>=', $fromDate)
                      ->orWhere(function ($sub) use ($fromDate) {
                          $sub->whereNull('check_in_at')->where('created_at', '>=', $fromDate);
                      });
                });
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        if ($request->filled('to_date')) {
            try {
                $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
                $query->where(function ($q) use ($toDate) {
                    $q->where('check_in_at', '<=', $toDate)
                      ->orWhere(function ($sub) use ($toDate) {
                          $sub->whereNull('check_in_at')->where('created_at', '<=', $toDate);
                      });
                });
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        // Filter by Visitor Type
        if ($request->filled('visitor_type') && !in_array($request->visitor_type, ['ALL', 'ALL CATEGORIES', ''])) {
            $query->where('visitor_type', $request->visitor_type);
        }

        // Filter by Status if passed
        if ($request->filled('status') && !in_array($request->status, ['ALL', ''])) {
            $query->where('status', $request->status);
        }

        // Keyword Search (Name, Pass Number, Mobile, Host Name, Whom to meet, Vehicle)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('pass_number', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$search}%")
                  ->orWhere('alternate_mobile', 'LIKE', "%{$search}%")
                  ->orWhere('host_name', 'LIKE', "%{$search}%")
                  ->orWhere('whom_to_meet_type', 'LIKE', "%{$search}%")
                  ->orWhere('vehicle_number', 'LIKE', "%{$search}%")
                  ->orWhere('security_gate', 'LIKE', "%{$search}%");
            });
        }

        $visitors = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        // Summary Counts for active school
        $totalCount = Visitor::where('school_id', $schoolId)->count();
        $insideCount = Visitor::where('school_id', $schoolId)->where('status', 'checked_in')->count();
        $checkedOutCount = Visitor::where('school_id', $schoolId)->where('status', 'checked_out')->count();
        $todayCount = Visitor::where('school_id', $schoolId)->whereDate('created_at', today())->count();

        // Preset visitor categories
        $visitorTypes = [
            'Parent / Guardian',
            'Vendor / Supplier',
            'Guest / Dignitary',
            'Contractor / Maintenance',
            'Job Applicant',
            'Alumni',
            'Official / Inspector',
            'Relative / Friend',
            'Other Official',
        ];

        return view('school.front_desk.in_out_report', compact(
            'visitors',
            'visitorTypes',
            'totalCount',
            'insideCount',
            'checkedOutCount',
            'todayCount'
        ));
    }

    /**
     * Display Visitor Report page.
     */
    public function visitorReport(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $query = Visitor::where('school_id', $schoolId);

        // Date range filters
        if ($request->filled('from_date')) {
            try {
                $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
                $query->where(function ($q) use ($fromDate) {
                    $q->where('check_in_at', '>=', $fromDate)
                      ->orWhere(function ($sub) use ($fromDate) {
                          $sub->whereNull('check_in_at')->where('created_at', '>=', $fromDate);
                      });
                });
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        if ($request->filled('to_date')) {
            try {
                $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
                $query->where(function ($q) use ($toDate) {
                    $q->where('check_in_at', '<=', $toDate)
                      ->orWhere(function ($sub) use ($toDate) {
                          $sub->whereNull('check_in_at')->where('created_at', '<=', $toDate);
                      });
                });
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        // Search query across name, mobile, email, pass no, state, city, address, pincode
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$search}%")
                  ->orWhere('alternate_mobile', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('pass_number', 'LIKE', "%{$search}%")
                  ->orWhere('state', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('street_address', 'LIKE', "%{$search}%")
                  ->orWhere('pincode', 'LIKE', "%{$search}%")
                  ->orWhere('host_name', 'LIKE', "%{$search}%")
                  ->orWhere('vehicle_number', 'LIKE', "%{$search}%");
            });
        }

        $visitors = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        // Preset dropdowns for editing
        $visitorTypes = [
            'Parent / Guardian',
            'Vendor / Supplier',
            'Guest / Dignitary',
            'Contractor / Maintenance',
            'Job Applicant',
            'Alumni',
            'Official / Inspector',
            'Relative / Friend',
            'Other Official',
        ];

        $whomToMeetTypes = [
            'Principal / Management',
            'Teacher / Faculty',
            'Administration / Front Desk',
            'Student',
            'Accounts / Fee Counter',
            'Sports / Transport',
            'Hostel Warden',
            'Other Staff',
        ];

        $visitPurposes = [
            'Parent-Teacher Interaction',
            'Fee Payment / Accounts Enquiry',
            'New Admission Enquiry',
            'Official Meeting / Inspection',
            'Vendor Delivery / Maintenance',
            'Document Submission / Verification',
            'Student Pickup / Early Departure',
            'Job Interview',
            'Personal / Casual Visit',
            'Other',
        ];

        $securityGates = [
            'Main Gate 1',
            'Gate No. 2 (North)',
            'Gate No. 3 (Rear)',
            'Reception Entry',
            'Administrative Block Gate',
            'Junior Wing Gate',
        ];

        $idProofTypes = [
            'Aadhaar Card',
            'PAN Card',
            'Driving License',
            'Voter ID',
            'Passport',
            'Employee ID',
            'School / College ID',
            'Other Government ID',
        ];

        $staffMembers = Staff::where('school_id', $schoolId)
            ->select('id', 'first_name', 'last_name', 'designation_id')
            ->with(['designation:id,name'])
            ->orderBy('first_name')
            ->limit(100)
            ->get();

        $totalCount = Visitor::where('school_id', $schoolId)->count();
        $maleCount = Visitor::where('school_id', $schoolId)->where('gender', 'Male')->count();
        $femaleCount = Visitor::where('school_id', $schoolId)->where('gender', 'Female')->count();

        return view('school.front_desk.visitor_report', compact(
            'visitors',
            'visitorTypes',
            'whomToMeetTypes',
            'visitPurposes',
            'securityGates',
            'idProofTypes',
            'staffMembers',
            'totalCount',
            'maleCount',
            'femaleCount'
        ));
    }

    /**
     * Get single visitor details for Edit Modal.
     */
    public function getVisitorDetails(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $visitor = Visitor::where('school_id', $schoolId)->findOrFail($id);

        return response()->json([
            'success' => true,
            'visitor' => [
                'id'                       => $visitor->id,
                'pass_number'              => $visitor->pass_number,
                'full_name'                => $visitor->full_name,
                'gender'                   => $visitor->gender,
                'dob'                      => $visitor->dob ? $visitor->dob->format('Y-m-d') : null,
                'mobile_number'            => $visitor->mobile_number,
                'alternate_mobile'         => $visitor->alternate_mobile,
                'email'                    => $visitor->email,
                'street_address'           => $visitor->street_address,
                'state'                    => $visitor->state,
                'city'                     => $visitor->city,
                'pincode'                  => $visitor->pincode,
                'visitor_type'             => $visitor->visitor_type,
                'whom_to_meet_type'        => $visitor->whom_to_meet_type,
                'host_name'                => $visitor->host_name,
                'security_gate'            => $visitor->security_gate,
                'entourage_count'          => $visitor->entourage_count ?? 1,
                'visit_purpose'            => $visitor->visit_purpose,
                'detailed_purpose_remarks' => $visitor->detailed_purpose_remarks,
                'id_proof_type'            => $visitor->id_proof_type,
                'id_proof_number'          => $visitor->id_proof_number,
                'vehicle_number'           => $visitor->vehicle_number,
                'photo_url'                => $visitor->photo_url,
                'security_notes'           => $visitor->security_notes,
                'status'                   => $visitor->status,
                'check_in_at'              => $visitor->check_in_at ? $visitor->check_in_at->format('d-M-Y h:i A') : null,
                'check_out_at'             => $visitor->check_out_at ? $visitor->check_out_at->format('d-M-Y h:i A') : null,
                'print_url'                => route('school.front-desk.visitor.print', $visitor->id),
            ],
        ]);
    }

    /**
     * Update an existing visitor record and regenerate badge information.
     */
    public function updateVisitor(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $visitor = Visitor::where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'full_name'                => 'required|string|max:150',
            'gender'                   => 'nullable|string|in:Male,Female,Other',
            'dob'                      => 'nullable|date',
            'mobile_number'            => 'required|string|max:25',
            'alternate_mobile'         => 'nullable|string|max:25',
            'email'                    => 'nullable|email|max:150',
            'street_address'           => 'nullable|string|max:500',
            'state'                    => 'nullable|string|max:100',
            'city'                     => 'nullable|string|max:100',
            'pincode'                  => 'nullable|string|max:20',
            'visitor_type'             => 'nullable|string|max:100',
            'whom_to_meet_type'        => 'nullable|string|max:100',
            'host_name'                => 'nullable|string|max:150',
            'security_gate'            => 'nullable|string|max:100',
            'entourage_count'          => 'nullable|integer|min:1|max:100',
            'visit_purpose'            => 'nullable|string|max:150',
            'detailed_purpose_remarks' => 'nullable|string|max:1000',
            'id_proof_type'            => 'nullable|string|max:100',
            'id_proof_number'          => 'nullable|string|max:100',
            'vehicle_number'           => 'nullable|string|max:50',
            'security_notes'           => 'nullable|string|max:1000',
            'status'                   => 'nullable|string|in:checked_in,checked_out,expected,cancelled',
            'photo'                    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'webcam_photo'             => 'nullable|string',
        ]);

        $photoPath = $visitor->photo_path;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'visitor_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs("visitors/{$schoolId}", $filename, 'public');
        } elseif ($request->filled('webcam_photo') && str_starts_with($request->input('webcam_photo'), 'data:image')) {
            $dataUrl = $request->input('webcam_photo');
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
                $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
                $type = strtolower($type[1]);
                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                    $type = 'jpg';
                }
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'visitor_cam_' . uniqid() . '.' . $type;
                    $relativePath = "visitors/{$schoolId}/" . $filename;
                    Storage::disk('public')->put($relativePath, $data);
                    $photoPath = $relativePath;
                }
            }
        }

        // Update visitor record
        $updateData = [
            'full_name'                => $validated['full_name'],
            'gender'                   => $validated['gender'] ?? $visitor->gender,
            'dob'                      => $validated['dob'] ?? null,
            'mobile_number'            => $validated['mobile_number'],
            'alternate_mobile'         => $validated['alternate_mobile'] ?? null,
            'email'                    => $validated['email'] ?? null,
            'street_address'           => $validated['street_address'] ?? null,
            'state'                    => $validated['state'] ?? null,
            'city'                     => $validated['city'] ?? null,
            'pincode'                  => $validated['pincode'] ?? null,
            'visitor_type'             => $validated['visitor_type'] ?? $visitor->visitor_type,
            'whom_to_meet_type'        => $validated['whom_to_meet_type'] ?? $visitor->whom_to_meet_type,
            'host_name'                => $validated['host_name'] ?? $visitor->host_name,
            'security_gate'            => $validated['security_gate'] ?? $visitor->security_gate,
            'entourage_count'          => $validated['entourage_count'] ?? $visitor->entourage_count,
            'visit_purpose'            => $validated['visit_purpose'] ?? $visitor->visit_purpose,
            'detailed_purpose_remarks' => $validated['detailed_purpose_remarks'] ?? $visitor->detailed_purpose_remarks,
            'id_proof_type'            => $validated['id_proof_type'] ?? $visitor->id_proof_type,
            'id_proof_number'          => $validated['id_proof_number'] ?? $visitor->id_proof_number,
            'vehicle_number'           => $validated['vehicle_number'] ?? $visitor->vehicle_number,
            'security_notes'           => $validated['security_notes'] ?? $visitor->security_notes,
            'photo_path'               => $photoPath,
        ];

        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
            if ($validated['status'] === 'checked_out' && empty($visitor->check_out_at)) {
                $updateData['check_out_at'] = now();
            }
        }

        $visitor->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => 'Visitor details updated successfully! Updated pass is ready.',
                'visitor'     => $visitor->fresh(),
                'photo_url'   => $visitor->photo_url,
                'print_url'   => route('school.front-desk.visitor.print', $visitor->id),
            ]);
        }

        return redirect()->route('school.front-desk.visitor-report')
            ->with('success', 'Visitor details and pass updated successfully for Pass #' . $visitor->pass_number);
    }

    /**
     * Delete a visitor record.
     */
    public function destroyVisitor(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $visitor = Visitor::where('school_id', $schoolId)->findOrFail($id);
        $passNumber = $visitor->pass_number;

        $visitor->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Visitor record {$passNumber} deleted successfully."
            ]);
        }

        return redirect()->route('school.front-desk.visitor-report')
            ->with('success', "Visitor record {$passNumber} deleted successfully.");
    }

    /**
     * Display Vendor Report page.
     */
    public function vendorReport(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $query = Visitor::where('school_id', $schoolId)
            ->where(function ($q) {
                $q->where('visitor_type', 'Vendor / Supplier')
                  ->orWhere('visitor_type', 'LIKE', '%Vendor%')
                  ->orWhere('visitor_type', 'LIKE', '%Supplier%')
                  ->orWhere('visit_purpose', 'LIKE', '%Vendor%')
                  ->orWhere('visit_purpose', 'LIKE', '%Delivery%')
                  ->orWhere('visit_purpose', 'LIKE', '%Maintenance%');
            });

        // Date range filters
        if ($request->filled('from_date')) {
            try {
                $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
                $query->where(function ($q) use ($fromDate) {
                    $q->where('created_at', '>=', $fromDate)
                      ->orWhere('check_in_at', '>=', $fromDate);
                });
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        if ($request->filled('to_date')) {
            try {
                $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
                $query->where(function ($q) use ($toDate) {
                    $q->where('created_at', '<=', $toDate)
                      ->orWhere('check_in_at', '<=', $toDate);
                });
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        // Search query
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$search}%")
                  ->orWhere('alternate_mobile', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('pass_number', 'LIKE', "%{$search}%")
                  ->orWhere('state', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('street_address', 'LIKE', "%{$search}%")
                  ->orWhere('pincode', 'LIKE', "%{$search}%")
                  ->orWhere('id_proof_number', 'LIKE', "%{$search}%")
                  ->orWhere('detailed_purpose_remarks', 'LIKE', "%{$search}%")
                  ->orWhere('visit_purpose', 'LIKE', "%{$search}%");
            });
        }

        $vendors = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        // Preset dropdowns for editing
        $visitorTypes = [
            'Vendor / Supplier',
            'Contractor / Maintenance',
            'Parent / Guardian',
            'Guest / Dignitary',
            'Job Applicant',
            'Alumni',
            'Official / Inspector',
            'Relative / Friend',
            'Other Official',
        ];

        $whomToMeetTypes = [
            'Administration / Front Desk',
            'Accounts / Fee Counter',
            'Principal / Management',
            'Teacher / Faculty',
            'Student',
            'Sports / Transport',
            'Hostel Warden',
            'Other Staff',
        ];

        $visitPurposes = [
            'Vendor Delivery / Maintenance',
            'Official Meeting / Inspection',
            'Fee Payment / Accounts Enquiry',
            'Parent-Teacher Interaction',
            'New Admission Enquiry',
            'Document Submission / Verification',
            'Other',
        ];

        $securityGates = [
            'Main Gate 1',
            'Gate No. 2 (North)',
            'Gate No. 3 (Rear)',
            'Reception Entry',
            'Administrative Block Gate',
            'Junior Wing Gate',
        ];

        $idProofTypes = [
            'GST Number / Registration',
            'PAN Card',
            'Aadhaar Card',
            'Driving License',
            'Voter ID',
            'Passport',
            'Employee ID',
            'Other Government ID',
        ];

        $staffMembers = Staff::where('school_id', $schoolId)
            ->select('id', 'first_name', 'last_name', 'designation_id')
            ->with(['designation:id,name'])
            ->orderBy('first_name')
            ->limit(100)
            ->get();

        $totalVendors = Visitor::where('school_id', $schoolId)
            ->where(function ($q) {
                $q->where('visitor_type', 'Vendor / Supplier')
                  ->orWhere('visitor_type', 'LIKE', '%Vendor%')
                  ->orWhere('visitor_type', 'LIKE', '%Supplier%');
            })->count();

        $todayVendors = Visitor::where('school_id', $schoolId)
            ->where(function ($q) {
                $q->where('visitor_type', 'Vendor / Supplier')
                  ->orWhere('visitor_type', 'LIKE', '%Vendor%')
                  ->orWhere('visitor_type', 'LIKE', '%Supplier%');
            })
            ->whereDate('created_at', today())
            ->count();

        return view('school.front_desk.vendor_report', compact(
            'vendors',
            'visitorTypes',
            'whomToMeetTypes',
            'visitPurposes',
            'securityGates',
            'idProofTypes',
            'staffMembers',
            'totalVendors',
            'todayVendors'
        ));
    }
}
