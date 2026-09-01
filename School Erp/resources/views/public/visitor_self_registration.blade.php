<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Visitor Gate Registration — {{ $school->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --theme-blue: #1d4ed8;
            --theme-blue-gradient: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #2563eb 100%);
            --theme-blue-hover: #1e40af;
            --theme-blue-light: #eff6ff;
            --theme-blue-border: #bfdbfe;
            --input-border: #cbd5e1;
            --bg-color: #f1f5f9;
        }

        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: #0f172a;
            min-height: 100vh;
            padding: 16px 12px 60px 12px;
            -webkit-tap-highlight-color: transparent;
        }

        .main-container {
            max-width: 760px;
            margin: 0 auto;
        }

        /* School Branding Header */
        .school-brand-banner {
            background: var(--theme-blue-gradient);
            border-radius: 20px 20px 0 0;
            padding: 24px 20px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(29, 78, 216, 0.25);
        }

        .school-brand-banner::after {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 140px;
            height: 140px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .brand-header-flex {
            display: flex;
            align-items: center;
            gap: 16px;
            position: relative;
            z-index: 2;
        }

        .school-logo-frame {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.8);
        }

        .school-logo-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
        }

        .school-logo-frame i {
            color: var(--theme-blue);
            font-size: 28px;
        }

        .school-title-text h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin: 0 0 3px 0;
            line-height: 1.25;
            color: #ffffff;
        }

        .school-title-text p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.88);
            margin: 0;
        }

        .school-code-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(4px);
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            margin-top: 4px;
            border: 1px solid rgba(255, 255, 255, 0.35);
        }

        /* Gold Stripe Separator */
        .gold-stripe {
            background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
            height: 4px;
            width: 100%;
        }

        /* Sub-Header Bar */
        .sub-header-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sub-header-bar .tag-title {
            font-size: 13px;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sub-header-bar .pill-portal {
            background: var(--theme-blue-light);
            color: var(--theme-blue);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
        }

        /* Form Card */
        .form-card {
            background: #ffffff;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 24px 20px;
            border: 1px solid #e2e8f0;
            border-top: none;
        }

        /* Section Headings */
        .section-hdr {
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
            margin: 22px 0 14px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 8px;
            border-bottom: 1.5px solid #f1f5f9;
            position: relative;
        }

        .section-hdr:first-of-type {
            margin-top: 0;
        }

        .section-hdr::after {
            content: '';
            position: absolute;
            bottom: -1.5px;
            left: 0;
            width: 36px;
            height: 2px;
            background: var(--theme-blue);
            border-radius: 2px;
        }

        .section-hdr-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--theme-blue-light);
            color: var(--theme-blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        /* Labels & Inputs */
        .form-label-custom {
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
            display: block;
        }

        .form-label-custom .req {
            color: #ef4444;
            font-weight: 800;
            margin-left: 2px;
        }

        .form-control-custom, .form-select-custom {
            width: 100%;
            border: 1.5px solid var(--input-border);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13.5px;
            color: #0f172a;
            background-color: #ffffff;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            border-color: var(--theme-blue);
            box-shadow: 0 0 0 3.5px rgba(29, 78, 216, 0.15);
            background-color: #ffffff;
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
            font-size: 13px;
        }

        .email-highlight-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 6px;
            font-size: 11.5px;
            color: #1e40af;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Photo Box */
        .photo-capture-card {
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.2s ease;
        }

        .photo-capture-card:hover {
            border-color: var(--theme-blue);
            background: var(--theme-blue-light);
        }

        .preview-img-frame {
            width: 90px;
            height: 90px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid var(--theme-blue);
            margin: 0 auto 10px auto;
            display: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Submit Button */
        .btn-submit-request {
            width: 100%;
            background: var(--theme-blue-gradient);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 14px 24px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.22s ease;
            box-shadow: 0 6px 18px rgba(29, 78, 216, 0.28);
            margin-top: 24px;
        }

        .btn-submit-request:hover {
            background: var(--theme-blue-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(29, 78, 216, 0.35);
            color: #ffffff;
        }

        .btn-submit-request:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Camera Overlay Modal */
        .custom-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(8px);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .custom-modal-overlay.active {
            display: flex;
        }

        .camera-dialog {
            background: #ffffff;
            border-radius: 18px;
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .camera-dialog-hdr {
            background: var(--theme-blue-gradient);
            color: #ffffff;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .camera-video-box {
            width: 100%;
            height: 280px;
            background: #0f172a;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        .camera-video-box video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Success Screen */
        .success-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 36px 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            border: 1.5px solid #86efac;
            animation: fadeIn 0.4s ease;
        }

        .success-icon-circle {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: #dcfce7;
            color: #16a34a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.18);
        }

        .pass-number-badge {
            background: #f8fafc;
            border: 2px dashed #0056b3;
            border-radius: 12px;
            padding: 12px 18px;
            display: inline-block;
            margin: 16px 0 20px;
            font-size: 16px;
            font-weight: 800;
            color: #dc2626;
            letter-spacing: 0.5px;
        }

        .info-pill-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            color: #1e40af;
            text-align: left;
            margin-bottom: 24px;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="main-container">

    @if(session('request_submitted'))
    <!-- Success Confirmation Screen -->
    <div class="success-card">
        <div class="success-icon-circle">
            <i class="fas fa-check"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Registration Request Submitted!</h3>
        <p class="text-muted mb-3" style="font-size: 14px;">Welcome to <strong>{{ session('school_name', $school->name) }}</strong></p>

        <div class="pass-number-badge">
            <span style="font-size: 11px; text-transform: uppercase; color: #64748b; display: block; font-weight: 700;">Pass Request Reference</span>
            {{ session('submitted_pass_number') }}
        </div>

        <div class="info-pill-note">
            <div class="fw-bold mb-1"><i class="fas fa-info-circle me-1"></i> What happens next?</div>
            <div>1. Please notify the security guard / front desk desk at the gate that you have submitted your details.</div>
            <div>2. Once the school approves your request, your <strong>Official Digital Visitor Pass</strong> with QR code will be automatically emailed to: <strong>{{ session('submitted_email') }}</strong>.</div>
        </div>

        <a href="{{ route('public.visitor.register', ['schoolCode' => $school->code ?: $school->id]) }}" class="btn btn-outline-primary fw-bold px-4 py-2" style="border-radius: 10px;">
            <i class="fas fa-redo me-1"></i> Submit Another Request
        </a>
    </div>

    @else

    <!-- Main Registration Form -->
    <div class="school-brand-banner">
        <div class="brand-header-flex">
            <div class="school-logo-frame">
                @if($school->logo_url)
                    <img src="{{ $school->logo_url }}" alt="{{ $school->name }}">
                @else
                    <i class="fas fa-graduation-cap"></i>
                @endif
            </div>
            <div class="school-title-text">
                <h1>{{ $school->name }}</h1>
                <p><i class="fas fa-map-marker-alt me-1"></i> {{ $school->address ?: 'School Campus' }}</p>
                <div class="school-code-badge">
                    <i class="fas fa-shield-alt me-1"></i> CODE: {{ strtoupper($school->code ?: 'EDUZEN') }}
                </div>
            </div>
        </div>
    </div>
    <div class="gold-stripe"></div>

    <div class="sub-header-bar">
        <div class="tag-title">
            <i class="fas fa-id-card text-primary"></i> Visitor Self-Registration
        </div>
        <div class="pill-portal">
            <i class="fas fa-qrcode me-1"></i> Gate Scan
        </div>
    </div>

    <div class="form-card">
        @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3 small" style="border-radius: 10px;">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Please fix the following errors:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form id="publicVisitorForm" method="POST" action="{{ route('public.visitor.register.store', ['schoolCode' => $school->code ?: $school->id]) }}" enctype="multipart/form-data">
            @csrf

            <!-- Section 1: Visitor Type & Personal Info -->
            <div class="section-hdr">
                <span class="section-hdr-icon"><i class="fas fa-user"></i></span>
                <span>Personal Information</span>
            </div>

            <div class="mb-3">
                <label class="form-label-custom" for="visitor_type">
                    Select Visitor Type <span class="req">*</span>
                </label>
                <select name="visitor_type" id="visitor_type" class="form-select-custom" required>
                    <option value="">-- Select Visitor Type --</option>
                    @foreach($visitorTypes as $vType)
                        <option value="{{ $vType }}" {{ old('visitor_type') == $vType ? 'selected' : '' }}>
                            {{ $vType }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-7 col-12">
                    <label class="form-label-custom" for="full_name">
                        Your Full Name <span class="req">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name" class="form-control-custom" placeholder="Enter Full Name" value="{{ old('full_name') }}" required>
                </div>
                <div class="col-md-5 col-12">
                    <label class="form-label-custom" for="gender">Gender</label>
                    <select name="gender" id="gender" class="form-select-custom">
                        <option value="">-- Select --</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6 col-12">
                    <label class="form-label-custom" for="mobile_number">
                        Mobile Number <span class="req">*</span>
                    </label>
                    <input type="tel" name="mobile_number" id="mobile_number" class="form-control-custom" placeholder="10-digit Mobile No." maxlength="15" value="{{ old('mobile_number') }}" required>
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label-custom" for="alternate_mobile">Alternate / Emergency Mobile</label>
                    <input type="tel" name="alternate_mobile" id="alternate_mobile" class="form-control-custom" placeholder="Optional Emergency No." maxlength="15" value="{{ old('alternate_mobile') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label-custom" for="email">
                    Email Address <span class="req">*</span>
                </label>
                <input type="email" name="email" id="email" class="form-control-custom" placeholder="name@example.com" value="{{ old('email') }}" required>
                <div class="email-highlight-box">
                    <i class="fas fa-envelope-open-text font-size-16"></i>
                    <span>Your Digital Visitor Pass & QR will be automatically sent to this email address once approved by the front desk.</span>
                </div>
            </div>

            <!-- Section 2: Address & Location Details -->
            <div class="section-hdr">
                <span class="section-hdr-icon"><i class="fas fa-map-marker-alt"></i></span>
                <span>Address & Residential Details</span>
            </div>

            <div class="mb-3">
                <label class="form-label-custom" for="street_address">Street Address</label>
                <textarea name="street_address" id="street_address" class="form-control-custom" rows="2" placeholder="Residential / Office address">{{ old('street_address') }}</textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4 col-12">
                    <label class="form-label-custom" for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control-custom" placeholder="City" value="{{ old('city') }}">
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label-custom" for="state">State</label>
                    <input type="text" name="state" id="state" class="form-control-custom" placeholder="State" value="{{ old('state') }}">
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label-custom" for="pincode">Pincode</label>
                    <input type="text" name="pincode" id="pincode" class="form-control-custom" placeholder="Pincode" maxlength="10" value="{{ old('pincode') }}">
                </div>
            </div>

            <!-- Section 3: Visit Purpose & Host Assignment -->
            <div class="section-hdr">
                <span class="section-hdr-icon"><i class="fas fa-user-friends"></i></span>
                <span>Visit Details & Meeting Assignment</span>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6 col-12">
                    <label class="form-label-custom" for="whom_to_meet_type">
                        Whom to Meet (Department / Category) <span class="req">*</span>
                    </label>
                    <select name="whom_to_meet_type" id="whom_to_meet_type" class="form-select-custom" required>
                        <option value="">-- Select Category --</option>
                        @foreach($whomToMeetTypes as $wType)
                            <option value="{{ $wType }}" {{ old('whom_to_meet_type') == $wType ? 'selected' : '' }}>
                                {{ $wType }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label-custom" for="host_name">Specific Official / Staff Name</label>
                    <input type="text" name="host_name" id="host_name" list="staffHostList" class="form-control-custom" placeholder="Name of Person to meet (Optional)" value="{{ old('host_name') }}">
                    <datalist id="staffHostList">
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->first_name }} {{ $staff->last_name }} ({{ $staff->designation?->name ?? 'Staff' }})"></option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6 col-12">
                    <label class="form-label-custom" for="visit_purpose">
                        Purpose of Visit <span class="req">*</span>
                    </label>
                    <select name="visit_purpose" id="visit_purpose" class="form-select-custom" required>
                        <option value="">-- Select Purpose --</option>
                        @foreach($visitPurposes as $purpose)
                            <option value="{{ $purpose }}" {{ old('visit_purpose') == $purpose ? 'selected' : '' }}>
                                {{ $purpose }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label-custom" for="entourage_count">
                        Total Person(s) / Head Count <span class="req">*</span>
                    </label>
                    <input type="number" name="entourage_count" id="entourage_count" class="form-control-custom" min="1" max="100" value="{{ old('entourage_count', 1) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label-custom" for="detailed_purpose_remarks">Detailed Purpose Remarks</label>
                <input type="text" name="detailed_purpose_remarks" id="detailed_purpose_remarks" class="form-control-custom" placeholder="Additional details regarding your visit..." value="{{ old('detailed_purpose_remarks') }}">
            </div>

            <!-- Section 4: Identification & Photo -->
            <div class="section-hdr">
                <span class="section-hdr-icon"><i class="fas fa-shield-alt"></i></span>
                <span>Identity & Vehicle Information</span>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4 col-12">
                    <label class="form-label-custom" for="id_proof_type">Govt ID Proof Type</label>
                    <select name="id_proof_type" id="id_proof_type" class="form-select-custom">
                        <option value="">-- Select --</option>
                        @foreach($idProofTypes as $idType)
                            <option value="{{ $idType }}" {{ old('id_proof_type') == $idType ? 'selected' : '' }}>
                                {{ $idType }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label-custom" for="id_proof_number">ID Reference No.</label>
                    <input type="text" name="id_proof_number" id="id_proof_number" class="form-control-custom" placeholder="ID Number (Optional)" value="{{ old('id_proof_number') }}">
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label-custom" for="vehicle_number">Vehicle Number</label>
                    <input type="text" name="vehicle_number" id="vehicle_number" class="form-control-custom" placeholder="e.g. DL-01-AB-1234" value="{{ old('vehicle_number') }}">
                </div>
            </div>

            <!-- Photo / Selfie Capture -->
            <div class="mb-3">
                <label class="form-label-custom">Take Selfie / Upload Photo</label>
                <div class="photo-capture-card">
                    <img src="" id="visitorPhotoPreview" class="preview-img-frame" alt="Preview">
                    <input type="file" name="photo" id="publicPhotoInput" accept="image/*" class="d-none">
                    <input type="hidden" name="webcam_photo" id="publicWebcamPhotoInput">

                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <button type="button" class="btn btn-sm btn-primary fw-bold px-3 py-2" onclick="openSelfieCameraModal()" style="border-radius: 9px; background: var(--theme-blue);">
                            <i class="fas fa-camera me-1"></i> Take Selfie Photo
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold px-3 py-2" onclick="document.getElementById('publicPhotoInput').click()" style="border-radius: 9px;">
                            <i class="fas fa-upload me-1"></i> Upload Image
                        </button>
                        <button type="button" id="btnClearSelfie" class="btn btn-sm btn-link text-danger p-0 ms-2" style="display: none; text-decoration: none;" onclick="clearSelfiePhoto()">
                            <i class="fas fa-trash me-1"></i> Remove
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" id="btnSubmitSelfRegistration" class="btn-submit-request">
                <i class="fas fa-paper-plane"></i> Submit Visitor Request
            </button>
        </form>
    </div>
    @endif

</div>

<!-- Camera Selfie Lightbox Modal -->
<div class="custom-modal-overlay" id="publicCameraOverlay">
    <div class="camera-dialog">
        <div class="camera-dialog-hdr">
            <h6 class="m-0 fw-bold"><i class="fas fa-camera me-2"></i> Take Selfie Snapshot</h6>
            <button type="button" class="btn-close btn-close-white" onclick="closeSelfieCameraModal()"></button>
        </div>
        <div class="p-3 text-center">
            <div class="camera-video-box mb-3">
                <video id="selfieVideo" autoplay playsinline></video>
                <canvas id="selfieCanvas" style="display: none;"></canvas>
            </div>
            <div id="selfieErrorAlert" class="alert alert-warning py-1 small mb-2" style="display: none;"></div>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-primary fw-bold px-4 py-2" onclick="captureSelfieSnapshot()" style="border-radius: 10px; background: var(--theme-blue);">
                    <i class="fas fa-camera me-1"></i> Capture Photo
                </button>
                <button type="button" class="btn btn-secondary fw-semibold px-3 py-2" onclick="closeSelfieCameraModal()" style="border-radius: 10px;">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Photo handling
    const fileInput = document.getElementById('publicPhotoInput');
    const photoPreview = document.getElementById('visitorPhotoPreview');
    const webcamInput = document.getElementById('publicWebcamPhotoInput');
    const btnClear = document.getElementById('btnClearSelfie');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    photoPreview.src = evt.target.result;
                    photoPreview.style.display = 'block';
                    webcamInput.value = '';
                    if (btnClear) btnClear.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function clearSelfiePhoto() {
        if (fileInput) fileInput.value = '';
        if (webcamInput) webcamInput.value = '';
        if (photoPreview) {
            photoPreview.src = '';
            photoPreview.style.display = 'none';
        }
        if (btnClear) btnClear.style.display = 'none';
    }

    // Selfie Camera
    let selfieStream = null;
    const cameraOverlay = document.getElementById('publicCameraOverlay');
    const selfieVideo = document.getElementById('selfieVideo');
    const selfieCanvas = document.getElementById('selfieCanvas');
    const selfieErrorAlert = document.getElementById('selfieErrorAlert');

    function openSelfieCameraModal() {
        if (selfieErrorAlert) selfieErrorAlert.style.display = 'none';
        if (cameraOverlay) cameraOverlay.classList.add('active');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } })
                .then(function(stream) {
                    selfieStream = stream;
                    selfieVideo.srcObject = stream;
                    selfieVideo.play();
                })
                .catch(function(err) {
                    console.error("Camera error:", err);
                    if (selfieErrorAlert) {
                        selfieErrorAlert.textContent = "Camera permission denied or not available. Please use image upload.";
                        selfieErrorAlert.style.display = 'block';
                    }
                });
        } else {
            if (selfieErrorAlert) {
                selfieErrorAlert.textContent = "Camera not supported on this browser. Please upload an image file.";
                selfieErrorAlert.style.display = 'block';
            }
        }
    }

    function captureSelfieSnapshot() {
        if (!selfieVideo || !selfieStream) return;
        selfieCanvas.width = 480;
        selfieCanvas.height = 360;
        const ctx = selfieCanvas.getContext('2d');
        ctx.drawImage(selfieVideo, 0, 0, selfieCanvas.width, selfieCanvas.height);

        const dataUrl = selfieCanvas.toDataURL('image/jpeg', 0.9);
        webcamInput.value = dataUrl;
        if (fileInput) fileInput.value = '';

        if (photoPreview) {
            photoPreview.src = dataUrl;
            photoPreview.style.display = 'block';
        }
        if (btnClear) btnClear.style.display = 'inline-block';

        closeSelfieCameraModal();
    }

    function closeSelfieCameraModal() {
        if (selfieStream) {
            selfieStream.getTracks().forEach(track => track.stop());
            selfieStream = null;
        }
        if (cameraOverlay) cameraOverlay.classList.remove('active');
    }

    // Form submit loading
    const form = document.getElementById('publicVisitorForm');
    if (form) {
        form.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitSelfRegistration');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting Request...';
            }
        });
    }
</script>
</body>
</html>
