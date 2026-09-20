import React from 'react';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { DigitalDiaryScreen } from '../screens/academics/DigitalDiaryScreen';
import { HomeworkDetailScreen } from '../screens/academics/HomeworkDetailScreen';
import { HomeworkScreen } from '../screens/academics/HomeworkScreen';
import { TimetableScreen } from '../screens/academics/TimetableScreen';
import { ApplyLeaveScreen } from '../screens/attendance/ApplyLeaveScreen';
import { AttendanceCalendarScreen } from '../screens/attendance/AttendanceCalendarScreen';
import { LeaveHistoryScreen } from '../screens/attendance/LeaveHistoryScreen';
import { ChildDocumentsScreen } from '../screens/children/ChildDocumentsScreen';
import { ChildProfileScreen } from '../screens/children/ChildProfileScreen';
import { ParentChatScreen } from '../screens/communication/ParentChatScreen';
import { ExamScheduleScreen } from '../screens/exams/ExamScheduleScreen';
import { ReportCardScreen } from '../screens/exams/ReportCardScreen';
import { FeeInvoiceDetailScreen } from '../screens/fees/FeeInvoiceDetailScreen';
import { FeesOverviewScreen } from '../screens/fees/FeesOverviewScreen';
import { PaymentHistoryScreen } from '../screens/fees/PaymentHistoryScreen';
import { NoticeDetailScreen } from '../screens/notices/NoticeDetailScreen';
import { NoticesScreen } from '../screens/notices/NoticesScreen';
import { NotificationSettingsScreen } from '../screens/settings/NotificationSettingsScreen';
import { ParentSettingsScreen } from '../screens/settings/ParentSettingsScreen';
import { BusTrackingScreen } from '../screens/transport/BusTrackingScreen';
import { ParentStackParamList } from '../types/navigation';
import { ParentTabNavigator } from './ParentTabNavigator';

const Stack = createNativeStackNavigator<ParentStackParamList>();

export const ParentStackNavigator: React.FC = () => {
  return (
    <Stack.Navigator
      initialRouteName="MainTabs"
      screenOptions={{
        headerShown: false,
        animation: 'slide_from_right',
      }}
    >
      <Stack.Screen name="MainTabs" component={ParentTabNavigator} />
      <Stack.Screen name="ChildProfile" component={ChildProfileScreen} />
      <Stack.Screen name="ChildDocuments" component={ChildDocumentsScreen} />
      <Stack.Screen name="AttendanceCalendar" component={AttendanceCalendarScreen} />
      <Stack.Screen name="ApplyLeave" component={ApplyLeaveScreen} />
      <Stack.Screen name="LeaveHistory" component={LeaveHistoryScreen} />
      <Stack.Screen name="HomeworkList" component={HomeworkScreen} />
      <Stack.Screen name="HomeworkDetail" component={HomeworkDetailScreen} />
      <Stack.Screen name="DigitalDiary" component={DigitalDiaryScreen} />
      <Stack.Screen name="Timetable" component={TimetableScreen} />
      <Stack.Screen name="FeesOverview" component={FeesOverviewScreen} />
      <Stack.Screen name="FeeInvoiceDetail" component={FeeInvoiceDetailScreen} />
      <Stack.Screen name="PaymentHistory" component={PaymentHistoryScreen} />
      <Stack.Screen name="NoticesList" component={NoticesScreen} />
      <Stack.Screen name="NoticeDetail" component={NoticeDetailScreen} />
      <Stack.Screen name="ExamSchedule" component={ExamScheduleScreen} />
      <Stack.Screen name="ReportCard" component={ReportCardScreen} />
      <Stack.Screen name="BusTracking" component={BusTrackingScreen} />
      <Stack.Screen name="ParentChat" component={ParentChatScreen} />
      <Stack.Screen name="NotificationSettings" component={NotificationSettingsScreen} />
      <Stack.Screen name="ParentSettings" component={ParentSettingsScreen} />
    </Stack.Navigator>
  );
};
