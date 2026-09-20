# SchoolCloud ERP - Dedicated Parent Mobile Application (React Native Android)

A complete, enterprise-grade **Parent-Only** React Native mobile application engineered specifically for Android devices. 

---

## 🔒 Key Design Principles & Role Isolation

- **100% Parent Role Isolation**:
  - Exclusively contains Parent / Guardian authentication and academic workflows.
  - Teacher login, School Admin, Staff, and Superadmin portals are completely blocked from this app.
- **Enterprise Multi-Child Architecture**:
  - Seamless child selector in header and bottom-sheet modal.
  - Instantly toggles active student context across all screens (Attendance, Diary, Homework, Fees, Exams, Bus Tracking) without reloading.
- **RESTful API Backend Core**:
  - Connects to Laravel ERP backend via Sanctum Bearer token authentication and `/api/v1/parent/*` endpoints.
  - Supports offline token caching, session restoration, and real-time refresh.

---

## 📁 Project Structure

```
parent_mobile_app/
├── package.json               # Dependencies & build scripts
├── tsconfig.json              # TypeScript configuration with path aliases
├── app.json                   # React Native / Android App Config & Permissions
├── babel.config.js            # Module resolver and babel presets
├── metro.config.js            # Metro bundler config
├── .env.example               # Environment variables template
├── App.tsx                    # Root application entry with Providers
├── index.js                   # Application bootstrap registry
└── src/
    ├── api/                   # Dedicated API client & services
    │   ├── client.ts          # Axios client with Sanctum Bearer token interceptor
    │   ├── authApi.ts         # Parent login, OTP verification, Profile & Logout
    │   ├── childrenApi.ts     # Children list, bio details & uploaded documents
    │   ├── attendanceApi.ts   # Attendance calendar, daily logs & leave applications
    │   ├── feesApi.ts         # Invoices, fee breakdown & payment history
    │   ├── academicsApi.ts    # Homework tasks, class diary notes & timetable
    │   ├── noticesApi.ts      # School circulars & announcements
    │   ├── examsApi.ts        # Exam schedules & report cards
    │   ├── transportApi.ts    # Live bus tracking & driver contact
    │   └── chatApi.ts         # Parent-School inquiry messaging
    ├── config/
    │   ├── constants.ts       # API endpoints, default URLs & Storage keys
    │   └── theme.ts           # Design tokens (Colors, Typography, Spacing, Shadows)
    ├── context/
    │   ├── AuthContext.tsx    # Parent authentication & session state
    │   ├── ActiveChildContext.tsx # Multi-child selection state provider
    │   └── ThemeContext.tsx   # Dynamic ERP branding & Dark/Light mode provider
    ├── navigation/
    │   ├── types.ts           # Type-safe navigation route definitions
    │   ├── RootNavigator.tsx  # Auth vs Main app switch
    │   ├── AuthNavigator.tsx  # Parent Login, OTP Login & Forgot Password stack
    │   ├── ParentTabNavigator.tsx # Bottom tab bar (Home, Attendance, Diary, Fees, Account)
    │   └── ParentStackNavigator.tsx # Stack navigator for all sub-screens
    ├── screens/
    │   ├── auth/              # ParentLoginScreen, OtpLoginScreen, ForgotPasswordScreen
    │   ├── dashboard/         # ParentHomeScreen
    │   ├── children/          # ChildProfileScreen, ChildDocumentsScreen
    │   ├── attendance/        # AttendanceCalendarScreen, ApplyLeaveScreen, LeaveHistoryScreen
    │   ├── academics/         # HomeworkScreen, HomeworkDetailScreen, DigitalDiaryScreen, TimetableScreen
    │   ├── fees/              # FeesOverviewScreen, FeeInvoiceDetailScreen, PaymentHistoryScreen
    │   ├── notices/           # NoticesScreen, NoticeDetailScreen
    │   ├── exams/             # ExamScheduleScreen, ReportCardScreen
    │   ├── transport/         # BusTrackingScreen
    │   ├── communication/     # ParentChatScreen
    │   └── settings/          # ParentSettingsScreen, NotificationSettingsScreen
    ├── components/
    │   ├── common/            # ScreenWrapper, CustomButton, CustomInput, Card, Badge, LoadingIndicator, EmptyState
    │   └── parent/            # ChildSwitcherBar, ChildSwitcherModal, AttendanceStatCard, FeeSummaryCard, QuickActionGrid, HomeworkCard, NoticeCard
    ├── types/                 # TypeScript interfaces for all ERP models
    └── utils/                 # Storage helpers, date/currency formatters, validators
```

---

## 🚀 How to Run Locally

### Prerequisites
- Node.js 18+ installed
- Android Studio with Android SDK / Emulator or physical Android device

### Step 1: Install Dependencies
```bash
cd parent_mobile_app
npm install
```

### Step 2: Configure API Endpoint
Create `.env` from `.env.example`:
```bash
cp .env.example .env
```
- For **Android Emulator**: `API_BASE_URL=http://10.0.2.2:8000/api/v1`
- For **Physical Android Device**: `API_BASE_URL=http://<YOUR_LOCAL_IP>:8000/api/v1` (e.g. `http://192.168.1.5:8000/api/v1`)

### Step 3: Start the React Native App
```bash
npx expo start --android
```
or
```bash
npm run android
```

---

## 📲 Included Parent Features & Screens

1. **Parent Login & OTP Auth**: Strict Parent-Only login with school code, email/phone, and password or OTP.
2. **Multi-Child Switcher**: Tap to switch between multiple children instantly.
3. **Daily & Monthly Attendance**: Attendance calendar with status badges (Present, Absent, Late, Holiday) and Leave request submission.
4. **Fees & Online Payments**: Due amounts, fee breakdown, invoice details, and receipt downloads.
5. **Class Diary & Homework**: Daily teacher notes, homework assignments with attachment downloads and submission status.
6. **Class Timetable**: Day-by-day period schedules with teacher names and timings.
7. **School Notices & Circulars**: Priority announcements, emergency alerts, and circular PDFs.
8. **Exam Timetables & Report Cards**: Upcoming exam schedules and term-wise marks cards with PDF download.
9. **Live Bus Tracking**: Real-time vehicle location, route stops timeline, ETA, and direct driver call button.
10. **Inquiries & Messaging**: Direct chat with class teacher and administration.
11. **Settings & Dark Mode**: Dynamic theme switcher, document viewer, and notification preferences.
