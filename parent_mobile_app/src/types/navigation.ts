import { NavigatorScreenParams } from '@react-navigation/native';
import { FeeInvoice } from './fee';
import { HomeworkItem } from './academic';
import { NoticeItem } from './notice';
import { StudentChild } from './student';

export type AuthStackParamList = {
  ParentLogin: undefined;
  OtpLogin: undefined;
  ForgotPassword: { school_code?: string };
};

export type ParentTabParamList = {
  Home: undefined;
  AttendanceTab: undefined;
  DiaryTab: undefined;
  FeesTab: undefined;
  SettingsTab: undefined;
};

export type ParentStackParamList = {
  MainTabs: NavigatorScreenParams<ParentTabParamList>;
  ChildProfile: { child: StudentChild };
  ChildDocuments: { childId: number };
  AttendanceCalendar: undefined;
  ApplyLeave: undefined;
  LeaveHistory: undefined;
  HomeworkList: undefined;
  HomeworkDetail: { homework: HomeworkItem };
  DigitalDiary: undefined;
  Timetable: undefined;
  FeesOverview: undefined;
  FeeInvoiceDetail: { invoice: FeeInvoice };
  PaymentHistory: undefined;
  NoticesList: undefined;
  NoticeDetail: { notice: NoticeItem };
  ExamSchedule: undefined;
  ReportCard: undefined;
  BusTracking: undefined;
  ParentChat: undefined;
  NotificationSettings: undefined;
  ParentSettings: undefined;
};
