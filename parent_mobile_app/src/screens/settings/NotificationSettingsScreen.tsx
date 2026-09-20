import React, { useState } from 'react';
import {
  ScrollView,
  StyleSheet,
  Switch,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';

type Props = NativeStackScreenProps<ParentStackParamList, 'NotificationSettings'>;

export const NotificationSettingsScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();

  const [attendanceAlerts, setAttendanceAlerts] = useState(true);
  const [feeReminders, setFeeReminders] = useState(true);
  const [homeworkUpdates, setHomeworkUpdates] = useState(true);
  const [schoolNotices, setSchoolNotices] = useState(true);
  const [busTrackingAlerts, setBusTrackingAlerts] = useState(true);

  const renderToggle = (
    title: string,
    desc: string,
    value: boolean,
    onValueChange: (v: boolean) => void
  ) => (
    <View
      style={[
        styles.row,
        {
          backgroundColor: colors.surface,
          borderColor: colors.borderSubtle,
        },
      ]}
    >
      <View style={{ flex: 1, marginRight: Spacing.md }}>
        <Text style={[styles.rowTitle, { color: colors.text }]}>{title}</Text>
        <Text style={[styles.rowDesc, { color: colors.textSecondary }]}>{desc}</Text>
      </View>

      <Switch
        value={value}
        onValueChange={onValueChange}
        trackColor={{ false: '#cbd5e1', true: colors.primaryLight }}
        thumbColor={value ? colors.primary : '#ffffff'}
      />
    </View>
  );

  return (
    <ScreenWrapper>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.topNav}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
          </TouchableOpacity>
          <Text style={[styles.headerTitle, { color: colors.text }]}>Notification Settings</Text>
          <View style={{ width: 40 }} />
        </View>

        <View style={styles.content}>
          <Text style={[styles.introText, { color: colors.textSecondary }]}>
            Choose the critical updates and alerts you wish to receive on this device.
          </Text>

          {renderToggle(
            'Daily Attendance Push Alerts',
            'Get notified instantly when your child is marked Present, Late, or Absent.',
            attendanceAlerts,
            setAttendanceAlerts
          )}

          {renderToggle(
            'Fee Due & Payment Reminders',
            'Receive alerts before upcoming fee due dates and payment confirmations.',
            feeReminders,
            setFeeReminders
          )}

          {renderToggle(
            'New Homework Assignments',
            'Get updates as soon as teachers post assignments or diary notes.',
            homeworkUpdates,
            setHomeworkUpdates
          )}

          {renderToggle(
            'School Circulars & Emergency Notices',
            'Instant broadcast alerts for holidays, weather alerts, and circulars.',
            schoolNotices,
            setSchoolNotices
          )}

          {renderToggle(
            'School Bus Proximity & Tracking',
            'Alerts when school bus is 5-10 minutes away from pickup or drop-off point.',
            busTrackingAlerts,
            setBusTrackingAlerts
          )}
        </View>
      </ScrollView>
    </ScreenWrapper>
  );
};

const styles = StyleSheet.create({
  scrollContent: {
    paddingBottom: Spacing.xxxl,
  },
  topNav: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: Spacing.xl,
    paddingVertical: Spacing.md,
  },
  backText: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.semibold,
  },
  headerTitle: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  content: {
    paddingHorizontal: Spacing.lg,
  },
  introText: {
    fontSize: Typography.size.xs,
    lineHeight: 18,
    marginBottom: Spacing.md,
    paddingHorizontal: Spacing.xs,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: Spacing.md,
    marginVertical: Spacing.xs,
    borderRadius: BorderRadius.lg,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  rowTitle: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.semibold,
  },
  rowDesc: {
    fontSize: 11,
    lineHeight: 16,
    marginTop: 2,
  },
});
