import { Platform } from 'react-native';

// In Android Emulator, 10.0.2.2 points to host localhost. On physical devices, replace with your local IP or production domain.
export const API_BASE_URL = Platform.select({
  android: 'http://10.0.2.2:8000/api/v1',
  ios: 'http://localhost:8000/api/v1',
  default: 'http://localhost:8000/api/v1',
});

export const APP_NAME = 'Educorerp';

export const STORAGE_KEYS = {
  AUTH_TOKEN: '@schoolcloud_parent_auth_token',
  USER_DATA: '@schoolcloud_parent_user',
  SCHOOL_DATA: '@schoolcloud_parent_school',
  ACTIVE_CHILD_ID: '@schoolcloud_parent_active_child_id',
  THEME_MODE: '@schoolcloud_parent_theme_mode',
  NOTIFICATIONS_ENABLED: '@schoolcloud_parent_notifications_enabled',
  SAVED_SCHOOL_CODE: '@schoolcloud_parent_saved_school_code',
};

export const API_ENDPOINTS = {
  // Auth
  LOGIN: '/parent/login',
  OTP_SEND: '/otp/send',
  OTP_VERIFY: '/otp/verify',
  LOGOUT: '/logout',
  ME: '/me',

  // Config & Banners
  APP_CONFIG: '/mobile/config',
  APP_BANNERS: '/mobile/banners',

  // Children
  CHILDREN: '/parent/children',
  CHILD_PROFILE: (id: number) => `/parent/children/${id}/profile`,
  CHILD_DOCUMENTS: (id: number) => `/parent/children/${id}/documents`,

  // Features
  ATTENDANCE: '/parent/attendance',
  LEAVES: '/parent/leaves',
  LEAVE_APPLY: '/parent/leaves/store',
  FEES: '/parent/fees',
  FEE_RECEIPT: (id: number) => `/parent/fees/${id}/receipt`,
  HOMEWORK: '/parent/assignments',
  DIARY: '/parent/diary',
  TIMETABLE: '/parent/timetable',
  NOTICES: '/parent/notices',
  EXAMS: '/parent/exams',
  REPORT_CARDS: '/parent/report-cards',
  TRANSPORT: '/parent/transport',
  CHAT_MESSAGES: '/parent/chat',
  CHAT_SEND: '/parent/chat/send',
};
