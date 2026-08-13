/**
 * EducoreERP - Master Interactive Module Dictionary & Page Functionality
 */

const moduleData = {
    'business_mgmt': {
        title: 'Advanced Business & Administrative Management',
        icon: 'fa-briefcase',
        desc: 'Comprehensive oversight tools for financial planning, fee collection reconciliation, staff attendance, payroll processing, and multi-branch administrative control.',
        subFeatures: [
            'Real-Time Fee Collection & Dues Dashboard',
            'Automated Staff Attendance & Biometric Integration',
            'Payroll Processing with Tax & Provident Fund Calculators',
            'Multi-Branch & Campus Consolidated Analytics'
        ],
        targetRole: 'School Owners, Directors, Administrators, Finance Heads'
    },
    'student_app': {
        title: 'Interactive Student & Learning App',
        icon: 'fa-user-graduate',
        desc: 'A unified mobile hub for students to access digital diaries, class timetables, attendance status, online fee payment, AI doubt solver, and transport tracking.',
        subFeatures: [
            'Digital Daily Diary & Homework Submission',
            '24/7 AI Tutor for Homework Assistance & Concept Clearing',
            'Live GPS Bus Tracking & ETA Notifications',
            'Digital Student Identity Card with QR Validation'
        ],
        targetRole: 'Students, Parents, Guardians'
    },
    'faculty_app': {
        title: 'Empowered Teacher & Staff App',
        icon: 'fa-chalkboard-teacher',
        desc: 'Streamline daily classroom tasks with one-tap attendance marking, digital marksheets upload, lesson planning, and direct communication with parents.',
        subFeatures: [
            '1-Tap Classroom Attendance & Leave Marking',
            'Instant Mark Entry & Grade Weightage Calculation',
            'Direct Parent Message Broadcast & Notice Publishing',
            'Digital Teacher Timetable & Substitution Alerts'
        ],
        targetRole: 'Class Teachers, Subject Teachers, Department Heads'
    },
    'admission_crm': {
        title: 'Intelligent Admission & Lead CRM',
        icon: 'fa-funnel-dollar',
        desc: 'Automate student recruitment pipelines from multi-channel enquiries (website, walk-in, social media) to document verification and seat confirmation.',
        subFeatures: [
            'Lead Capture Automation & Source Tracking',
            'Counselor Lead Assignment & Daily Follow-up Schedules',
            'Digital Admission Application Forms & Entrance Test Portal',
            'Real-Time Conversion Rates & Funnel Analytics'
        ],
        targetRole: 'Admission Officers, Counselors, Marketing Teams'
    },
    'enquiry_app': {
        title: 'Mobile Enquiry Management App',
        icon: 'fa-mobile-alt',
        desc: 'Capture, call, and convert walk-in and digital leads anywhere from mobile devices with automated follow-up reminders and status logs.',
        subFeatures: [
            'On-the-go Walk-in Prospect Registration',
            'Click-to-Call Lead Engagement with Call Logs',
            'Automated SMS & WhatsApp Prospect Follow-ups',
            'Admission Target vs. Achievement Gauges'
        ],
        targetRole: 'Front Office Executives, Telecallers, Counselors'
    },
    'ai_learning': {
        title: 'Basic AI Support',
        icon: 'fa-robot',
        desc: 'Get essential AI assistance across the platform to answer routine queries, support everyday tasks, and guide users efficiently.',
        subFeatures: [
            'Basic AI Assistant for General Queries & Help',
            'Quick Information & Guidance Lookup',
            'Automated Assistance for Routine Tasks',
            'Instant Response for General FAQs'
        ],
        targetRole: 'Students, Teachers, Staff, Administrators'
    },
    'lms': {
        title: 'Cloud Learning Management System (LMS)',
        icon: 'fa-laptop-code',
        desc: 'Deliver online courses, host video lectures, manage digital assignments, and assess student knowledge with interactive online quizzes.',
        subFeatures: [
            'Video Lecture Hosting & PDF E-Book Library',
            'Online Assignment Upload & Plagiarism Checker',
            'Automated Quiz Engine with Instant Score Calculation',
            'Student Course Completion Certificate Issuance'
        ],
        targetRole: 'E-Learning Co-ordinators, Teachers, Students'
    },
    'skill_tracking': {
        title: 'Skill Development & Progress Tracking',
        icon: 'fa-chart-line',
        desc: 'Evaluate holistic student growth beyond academics, including co-curricular skills, behavioral traits, sports accomplishments, and leadership metrics.',
        subFeatures: [
            'Rubrics-based Behavioral & Skill Ratings',
            'Personalized Goal Setting & Mentorship Feedback',
            'Extracurricular Activity & Trophy Logs',
            '360-Degree Holistic Student Growth Certificate'
        ],
        targetRole: 'Counselors, Class Teachers, Parents'
    },
    'placement_alumni': {
        title: 'Placement Cell & Alumni Portal',
        icon: 'fa-user-tie',
        desc: 'Connect graduates with top corporate recruiters, organize campus placement drives, and maintain an active global alumni network.',
        subFeatures: [
            'Campus Interview Drive Scheduling & Resume Upload',
            'Company Placement Eligibility Filtering Engine',
            'Alumni Directory & Networking Hub',
            'Alumni Donation & Guest Lecture Event Portal'
        ],
        targetRole: 'Placement Officers, Final Year Students, Alumni'
    },

    /* --- Why Choose Cards Keys --- */
    'all_in_one': {
        title: 'All-in-One Integrated School ERP',
        icon: 'fa-cubes',
        desc: 'A unified single-database architecture connecting admissions, academics, fee billing, HR, library, transport, and hostels seamlessly.',
        subFeatures: [
            'Zero Data Duplication Across Modules',
            'Unified Central Student & Employee Master Database',
            'Real-Time Inter-Departmental Workflows',
            'Centralized Executive Management Dashboard'
        ],
        targetRole: 'Trustees, Directors, General Managers'
    },
    'cloud_based': {
        title: '24/7 Secure Cloud-Based Platform',
        icon: 'fa-cloud-upload-alt',
        desc: 'Access your institution dashboard anytime, anywhere on any device with 99.9% guaranteed uptime SLA and automatic daily cloud backups.',
        subFeatures: [
            'AWS Cloud Infrastructure with Auto-Scaling',
            'Automated Daily Offsite Backups',
            'Zero On-Premise Server Maintenance Cost',
            'SSL 256-Bit Data Encryption'
        ],
        targetRole: 'IT Managers, Administrators, Management'
    },
    'role_based': {
        title: 'Granular Role-Based Access Control',
        icon: 'fa-user-shield',
        desc: 'Define custom access permissions for Admins, Principals, Accountants, Class Teachers, Subject Faculties, Students, and Parents.',
        subFeatures: [
            'Field-level & Module-level Permission Flags',
            'Role-Specific Custom Dashboards & Quick Actions',
            'Complete Staff Activity & Audit Logs',
            'Multi-level Approval Workflows'
        ],
        targetRole: 'Super Admins, HR Managers, System Admins'
    },
    'communication': {
        title: 'Seamless Multi-Channel Communication',
        icon: 'fa-comments',
        desc: 'Keep parents, students, and staff informed instantly with automated SMS broadcasts, WhatsApp messages, email notices, and app push alerts.',
        subFeatures: [
            'Automated Absenteeism & Fee Due Alerts',
            'Emergency School Circular & Holiday Notices',
            'Two-Way Parent Teacher Chat & Feedback',
            'Bulk SMS & DLT Registered Template Engine'
        ],
        targetRole: 'Teachers, Receptionists, Admin Team'
    },
    'mobile_apps': {
        title: 'Dedicated Native Mobile Apps',
        icon: 'fa-mobile-alt',
        desc: 'Modern Android & iOS native applications tailored for Student/Parent learning and Staff/Teacher daily classroom management.',
        subFeatures: [
            '1-Tap Attendance & Homework Submission',
            'Live GPS School Bus Tracking on Map',
            'Online Fee Payment & Instant Downloadable Receipts',
            'Exam Timetables & Report Card PDF Downloads'
        ],
        targetRole: 'Parents, Students, Teachers, Bus Drivers'
    },
    'scalable_secure': {
        title: 'Scalable & Secure Enterprise Infrastructure',
        icon: 'fa-server',
        desc: 'Engineered to handle 500 to 50,000+ students effortlessly with high-speed query execution and strict compliance.',
        subFeatures: [
            'End-to-End Encrypted Data Transmission',
            'Multi-tenant Cloud Architecture for Chain Schools',
            'ISO & Data Protection Compliant Infrastructure',
            'Redundant Backup Clusters'
        ],
        targetRole: 'IT Directors, Trustees, Security Officers'
    },
    'customizable_modules': {
        title: 'Customizable & Flexible Module Engine',
        icon: 'fa-sliders-h',
        desc: 'Adapt the ERP system to your school board (CBSE, ICSE, IB, State Board) and custom fee/grading policies.',
        subFeatures: [
            'Configurable Grading Rules & Weightage Schemes',
            'Custom Header/Footer for Report Cards & Receipts',
            'Customizable Fee Installments & Discount Rules',
            'Modular Enable/Disable Feature Toggles'
        ],
        targetRole: 'Exam Controller, Accountants, Principals'
    },
    'performance_tracking': {
        title: 'Real-Time Academic Performance Tracking',
        icon: 'fa-tachometer-alt',
        desc: 'Track student attendance trends, subject performance gaps, and teacher evaluation analytics with visual charts.',
        subFeatures: [
            'Visual Student 360 Degree Progress Charts',
            'Subject-wise Class Performance Comparison',
            'Early Warning Analytics for At-Risk Learners',
            'Automated Continuous & Comprehensive Evaluation'
        ],
        targetRole: 'Principals, Class Teachers, Parents'
    },
    'fast_support': {
        title: 'Fast Implementation & 24/7 Dedicated Support',
        icon: 'fa-headset',
        desc: 'Get your institution live in less than 48 hours with dedicated data migration managers and round-the-clock helpline.',
        subFeatures: [
            'Dedicated Account Implementation Manager',
            'Complete Legacy Data Extraction & Importing',
            'Hands-on Staff Training Seminars',
            '24/7 Priority Phone & WhatsApp Helpdesk'
        ],
        targetRole: 'School Owners, Directors, All Staff'
    }
};

/* --- TESTIMONIAL SLIDER DATA --- */
const testimonials = [
    {
        quote: "Educorerp has made our college & school management smooth and simple. From attendance to fee tracking, everything is in one place. Our teachers save time, and parents stay better informed. It’s the best decision we made for our campus.",
        name: "Dr. S. Malarkkan",
        role: "Principal, MVIT Group of Institutions",
        image: "https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=400&q=80"
    },
    {
        quote: "The automated fee collection, online receipt generation, and real-time WhatsApp alerts reduced our front-office queries by 70%. Educorerp is truly a game changer for modern school management.",
        name: "Dr. Rajesh Sharma",
        role: "Director, Delhi Public School (DPS)",
        image: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80"
    },
    {
        quote: "Managing multi-campus academic timetables, exam grading rules, and report cards used to take weeks. With Educorerp's AI-driven system, we generate everything with a single click.",
        name: "Sister Mary Joseph",
        role: "Academic Head, St. Xavier’s Group",
        image: "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80"
    }
];

let currentTestimonialIndex = 0;
let testimonialTimer = null;

function renderTestimonial(index) {
    const data = testimonials[index];
    const quoteEl = document.getElementById('testimonialQuote');
    const nameEl = document.getElementById('testimonialName');
    const roleEl = document.getElementById('testimonialRole');
    const imgEl = document.getElementById('testimonialImg');
    const dots = document.querySelectorAll('.testimonial-dot');

    if (quoteEl && nameEl && roleEl && imgEl) {
        quoteEl.textContent = `"${data.quote}"`;
        nameEl.textContent = data.name;
        roleEl.textContent = `— ${data.role}`;
        imgEl.src = data.image;

        dots.forEach((dot, idx) => {
            if (idx === index) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
}

function nextTestimonial() {
    currentTestimonialIndex = (currentTestimonialIndex + 1) % testimonials.length;
    renderTestimonial(currentTestimonialIndex);
}

function prevTestimonial() {
    currentTestimonialIndex = (currentTestimonialIndex - 1 + testimonials.length) % testimonials.length;
    renderTestimonial(currentTestimonialIndex);
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. Navbar Sticky Shadow on Scroll
    const navbar = document.querySelector('.navbar-custom');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // 2. Testimonial Slider Controls & Auto Play
    renderTestimonial(0);
    testimonialTimer = setInterval(nextTestimonial, 5000);

    const prevBtn = document.getElementById('prevTestimonialBtn');
    const nextBtn = document.getElementById('nextTestimonialBtn');

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            clearInterval(testimonialTimer);
            prevTestimonial();
            testimonialTimer = setInterval(nextTestimonial, 5000);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            clearInterval(testimonialTimer);
            nextTestimonial();
            testimonialTimer = setInterval(nextTestimonial, 5000);
        });
    }

    document.querySelectorAll('.testimonial-dot').forEach((dot, idx) => {
        dot.addEventListener('click', function() {
            clearInterval(testimonialTimer);
            currentTestimonialIndex = idx;
            renderTestimonial(idx);
            testimonialTimer = setInterval(nextTestimonial, 5000);
        });
    });

    // 3. Learn More Modal Handler for All Feature & Why-Choose Cards
    const moduleModalElement = document.getElementById('moduleDetailModal');
    if (moduleModalElement) {
        const moduleModal = new bootstrap.Modal(moduleModalElement);

        document.querySelectorAll('.btn-learn-more-card').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const key = this.getAttribute('data-module');
                const data = moduleData[key];

                if (data) {
                    document.getElementById('modalModuleTitle').textContent = data.title;
                    document.getElementById('modalModuleDesc').textContent = data.desc;
                    document.getElementById('modalModuleRole').textContent = data.targetRole;

                    const listContainer = document.getElementById('modalModuleFeatures');
                    listContainer.innerHTML = '';
                    data.subFeatures.forEach(feat => {
                        const li = document.createElement('li');
                        li.className = 'mb-2 text-dark font-medium d-flex align-items-center gap-2';
                        li.innerHTML = `<i class="fas fa-check-circle text-primary"></i> ${feat}`;
                        listContainer.appendChild(li);
                    });

                    moduleModal.show();
                }
            });
        });
    }

    // 4. Book a Free Demo 4-Step Interactive Engine & Calendly Listener
    const demoForm = document.getElementById('demoBookingForm');
    if (demoForm) {
        let currentYear = 2026;
        let currentMonth = 7; // August (0-indexed)
        let selectedDay = 5;
        let selectedDateStr = '2026-08-05';
        let selectedTimeStr = '10:15 AM - 10:30 AM';
        let currentStep = 1;

        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

        const step1Container = document.getElementById('step1Container');
        const step2Container = document.getElementById('step2Container');
        const step3Container = document.getElementById('step3Container');
        const step4Container = document.getElementById('step4Container');

        const stepProgressFill = document.getElementById('stepProgressFill');
        const progressStep1 = document.getElementById('progressStep1');
        const progressStep2 = document.getElementById('progressStep2');
        const progressStep3 = document.getElementById('progressStep3');
        const progressStep4 = document.getElementById('progressStep4');

        const selectedBookingDateInput = document.getElementById('selectedBookingDate');
        const selectedBookingTimeInput = document.getElementById('selectedBookingTime');
        const selectedTimezoneValInput = document.getElementById('selectedTimezoneVal');
        const timezoneSelect = document.getElementById('timezoneSelect');

        const calendarDaysGrid = document.getElementById('calendarDaysGrid');
        const calendarMonthYearTitle = document.getElementById('calendarMonthYearTitle');
        const btnPrevMonth = document.getElementById('btnPrevMonth');
        const btnNextMonth = document.getElementById('btnNextMonth');

        const btnGoToStep2 = document.getElementById('btnGoToStep2');
        const btnGoToStep3 = document.getElementById('btnGoToStep3');
        const btnBackToStep1 = document.getElementById('btnBackToStep1');
        const btnBackToStep1Sec = document.getElementById('btnBackToStep1Sec');
        const btnBackToStep2 = document.getElementById('btnBackToStep2');
        const btnBackToStep2Sec = document.getElementById('btnBackToStep2Sec');

        const step2DateSummary = document.getElementById('step2DateSummary');
        const step3Summary = document.getElementById('step3Summary');
        const timeSlotGrid = document.getElementById('timeSlotGrid');

        // Step Navigation Function
        function goToStep(step) {
            currentStep = step;
            if (step1Container) step1Container.style.display = (step === 1) ? 'block' : 'none';
            if (step2Container) step2Container.style.display = (step === 2) ? 'block' : 'none';
            if (step3Container) step3Container.style.display = (step === 3) ? 'block' : 'none';
            if (step4Container) step4Container.style.display = (step === 4) ? 'block' : 'none';

            // Progress bar line fill
            const fillPct = (step - 1) * 33.33;
            if (stepProgressFill) stepProgressFill.style.width = fillPct + '%';

            // Badges
            [progressStep1, progressStep2, progressStep3, progressStep4].forEach((el, idx) => {
                if (!el) return;
                const stepNum = idx + 1;
                el.classList.remove('active', 'completed');
                if (stepNum === step) {
                    el.classList.add('active');
                } else if (stepNum < step) {
                    el.classList.add('completed');
                }
            });
        }

        // Render Calendar Days
        function renderCalendar() {
            if (!calendarDaysGrid) return;
            calendarDaysGrid.innerHTML = '';
            if (calendarMonthYearTitle) {
                calendarMonthYearTitle.textContent = `${months[currentMonth]} ${currentYear}`;
            }

            const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            // Adjust firstDayIndex so Monday is 0
            const startingDay = (firstDayIndex === 0) ? 6 : firstDayIndex - 1;

            // Empty cells before start of month
            for (let i = 0; i < startingDay; i++) {
                const emptyCell = document.createElement('div');
                calendarDaysGrid.appendChild(emptyCell);
            }

            const today = new Date();
            today.setHours(0,0,0,0);

            for (let day = 1; day <= daysInMonth; day++) {
                const dayBtn = document.createElement('button');
                dayBtn.type = 'button';
                dayBtn.className = 'calendar-day-btn';
                dayBtn.textContent = day;

                const dayDate = new Date(currentYear, currentMonth, day);
                const dayOfWeek = dayDate.getDay();

                // Disable past dates and Sundays if desired (keep Saturdays & weekdays)
                if (dayDate < today || dayOfWeek === 0) {
                    dayBtn.classList.add('disabled');
                    dayBtn.disabled = true;
                } else {
                    if (day === selectedDay && currentMonth === 7 && currentYear === 2026) {
                        dayBtn.classList.add('selected');
                    }
                    if (dayDate.getTime() === today.getTime()) {
                        dayBtn.classList.add('today');
                    }

                    dayBtn.addEventListener('click', function() {
                        document.querySelectorAll('.calendar-day-btn').forEach(b => b.classList.remove('selected'));
                        dayBtn.classList.add('selected');
                        selectedDay = day;

                        const dateObj = new Date(currentYear, currentMonth, day);
                        const formattedMonthStr = (currentMonth + 1).toString().padStart(2, '0');
                        const formattedDayStr = day.toString().padStart(2, '0');
                        selectedDateStr = `${currentYear}-${formattedMonthStr}-${formattedDayStr}`;

                        if (selectedBookingDateInput) selectedBookingDateInput.value = selectedDateStr;

                        const dayName = dayNames[dateObj.getDay()];
                        const readableStr = `${dayName}, ${months[currentMonth]} ${day}, ${currentYear}`;

                        if (step2DateSummary) {
                            step2DateSummary.innerHTML = `<i class="fas fa-calendar-alt text-primary me-2"></i> ${readableStr}`;
                        }

                        if (btnGoToStep2) btnGoToStep2.disabled = false;
                        goToStep(2);
                    });
                }

                calendarDaysGrid.appendChild(dayBtn);
            }
        }

        // Generate Time Slots for Step 2
        function renderTimeSlots() {
            if (!timeSlotGrid) return;
            timeSlotGrid.innerHTML = '';

            const slots = [
                "9:00 AM", "9:15 AM", "9:30 AM", "9:45 AM",
                "10:00 AM", "10:15 AM", "10:30 AM", "10:45 AM",
                "11:00 AM", "11:15 AM", "11:30 AM", "11:45 AM",
                "2:00 PM", "2:15 PM", "2:30 PM", "2:45 PM",
                "3:00 PM", "3:30 PM", "4:00 PM", "4:30 PM"
            ];

            slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'time-slot-btn';
                btn.textContent = slot;

                btn.addEventListener('click', function() {
                    document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');

                    selectedTimeStr = slot;
                    if (selectedBookingTimeInput) selectedBookingTimeInput.value = slot;

                    // Update Step 3 Summary
                    if (step3Summary) {
                        const dObj = new Date(selectedDateStr);
                        const dayName = isNaN(dObj.getDay()) ? '' : dayNames[dObj.getDay()];
                        step3Summary.innerHTML = `<i class="fas fa-clock text-primary me-2"></i> ${slot}, ${dayName} ${selectedDateStr}`;
                    }

                    if (btnGoToStep3) btnGoToStep3.disabled = false;
                    goToStep(3);
                });

                timeSlotGrid.appendChild(btn);
            });
        }

        // Initialize Default Values
        selectedBookingDateInput.value = '2026-08-05';
        selectedBookingTimeInput.value = '10:15 AM';
        if (timezoneSelect) {
            timezoneSelect.addEventListener('change', function() {
                if (selectedTimezoneValInput) selectedTimezoneValInput.value = this.value;
            });
        }

        // Calendar Nav Buttons
        if (btnPrevMonth) {
            btnPrevMonth.addEventListener('click', function() {
                if (currentMonth === 0) { currentMonth = 11; currentYear--; } else { currentMonth--; }
                renderCalendar();
            });
        }
        if (btnNextMonth) {
            btnNextMonth.addEventListener('click', function() {
                if (currentMonth === 11) { currentMonth = 0; currentYear++; } else { currentMonth++; }
                renderCalendar();
            });
        }

        // Step Back & Next Buttons
        if (btnGoToStep2) btnGoToStep2.addEventListener('click', function() { goToStep(2); });
        if (btnGoToStep3) btnGoToStep3.addEventListener('click', function() { goToStep(3); });
        if (btnBackToStep1) btnBackToStep1.addEventListener('click', function() { goToStep(1); });
        if (btnBackToStep1Sec) btnBackToStep1Sec.addEventListener('click', function() { goToStep(1); });
        if (btnBackToStep2) btnBackToStep2.addEventListener('click', function() { goToStep(2); });
        if (btnBackToStep2Sec) btnBackToStep2Sec.addEventListener('click', function() { goToStep(2); });

        // Initial Renders
        renderCalendar();
        renderTimeSlots();

        // 5. AJAX Form Submission
        demoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('btnSubmitBooking') || demoForm.querySelector('button[type="submit"]');
            const alertBox = document.getElementById('demoAlertBox');

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Scheduling Session...';
            }

            const formData = new FormData(demoForm);

            fetch(demoForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Schedule Event <i class="fas fa-check-circle ms-2"></i>';
                }

                if (data.success) {
                    // Populate success card
                    const clientName = formData.get('full_name') || 'Client';
                    const dateVal = selectedBookingDateInput.value || 'Scheduled Date';
                    const timeVal = selectedBookingTimeInput.value || '10:15 AM';
                    const tzVal = selectedTimezoneValInput.value || 'India Standard Time';

                    const successDateTime = document.getElementById('summarySuccessDateTime');
                    const successTZ = document.getElementById('summarySuccessTZ');
                    const successClient = document.getElementById('summarySuccessClient');

                    if (successDateTime) successDateTime.textContent = `${timeVal}, ${dateVal}`;
                    if (successTZ) successTZ.textContent = tzVal;
                    if (successClient) successClient.textContent = clientName;

                    goToStep(4);
                } else {
                    if (alertBox) {
                        alertBox.style.display = 'block';
                        alertBox.className = 'alert alert-danger';
                        alertBox.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> ' + (data.message || 'Error scheduling demo request.');
                    }
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Schedule Event <i class="fas fa-check-circle ms-2"></i>';
                }
                if (alertBox) {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Something went wrong. Please check all fields and try again.';
                }
            });
        });

        // 6. Calendly PostMessage Event Sync Listener (Requirement 8)
        window.addEventListener('message', function(e) {
            if (e.data && e.data.event && e.data.event === 'calendly.event_scheduled') {
                const payload = e.data.payload || {};
                const invitee = payload.invitee || {};
                const event = payload.event || {};

                const cData = new FormData();
                cData.append('full_name', invitee.name || 'Calendly Guest');
                cData.append('email', invitee.email || 'guest@calendly.com');
                cData.append('phone', invitee.text_reminder_number || 'N/A');
                cData.append('institute_name', 'Calendly Prospect');
                cData.append('role', 'Prospect');
                cData.append('city', 'N/A');
                cData.append('state', 'N/A');
                cData.append('country', 'India');
                cData.append('booking_date', event.start_time ? event.start_time.split('T')[0] : 'Scheduled Date');
                cData.append('booking_time', event.start_time ? event.start_time.split('T')[1] : 'Scheduled Time');
                cData.append('timezone', 'Asia/Kolkata');
                cData.append('source', 'Calendly');
                cData.append('message', 'Booked via embedded Calendly widget');

                fetch(demoForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: cData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        goToStep(4);
                    }
                })
                .catch(err => console.log('Calendly sync log:', err));
            }
        });
    }
});
