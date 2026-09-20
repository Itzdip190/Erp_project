import React, { useState } from 'react';
import {
  Alert,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { AttendanceApi } from '../../api/attendanceApi';
import { CustomButton } from '../../components/common/CustomButton';
import { CustomInput } from '../../components/common/CustomInput';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';

type Props = NativeStackScreenProps<ParentStackParamList, 'ApplyLeave'>;

export const ApplyLeaveScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();

  const todayStr = new Date().toISOString().slice(0, 10);
  const [fromDate, setFromDate] = useState(todayStr);
  const [toDate, setToDate] = useState(todayStr);
  const [reason, setReason] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async () => {
    if (!activeChild) {
      Alert.alert('Error', 'Please select an active child first.');
      return;
    }
    if (!reason.trim()) {
      Alert.alert('Validation Error', 'Please specify the reason for absence.');
      return;
    }

    setLoading(true);
    try {
      await AttendanceApi.applyLeave({
        student_id: activeChild.id,
        from_date: fromDate,
        to_date: toDate,
        reason: reason.trim(),
      });

      Alert.alert(
        'Leave Application Submitted',
        'Your leave request has been submitted to the class teacher and administration for review.',
        [{ text: 'OK', onPress: () => navigation.goBack() }]
      );
    } catch (e: any) {
      Alert.alert('Submission Error', e.response?.data?.message || 'Failed to submit leave application.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScreenWrapper>
      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled">
        <View style={styles.topNav}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
          </TouchableOpacity>
          <Text style={[styles.headerTitle, { color: colors.text }]}>Apply Leave</Text>
          <View style={{ width: 40 }} />
        </View>

        <ChildSwitcherBar />

        <View
          style={[
            styles.formCard,
            {
              backgroundColor: colors.surface,
              borderColor: colors.borderSubtle,
            },
          ]}
        >
          <Text style={[styles.formHeading, { color: colors.text }]}>
            Student Absence Application
          </Text>
          <Text style={[styles.formSub, { color: colors.textSecondary }]}>
            Inform the school prior to absence for medical or family reasons.
          </Text>

          <CustomInput
            label="From Date (YYYY-MM-DD)"
            placeholder="2026-09-15"
            value={fromDate}
            onChangeText={setFromDate}
          />

          <CustomInput
            label="To Date (YYYY-MM-DD)"
            placeholder="2026-09-16"
            value={toDate}
            onChangeText={setToDate}
          />

          <CustomInput
            label="Reason for Leave"
            placeholder="Please write the reason for absence (e.g., Medical fever, Family wedding)..."
            value={reason}
            onChangeText={setReason}
            multiline
            numberOfLines={4}
            inputStyle={{ minHeight: 90, textAlignVertical: 'top' }}
          />

          <CustomButton
            title="Submit Leave Application"
            onPress={handleSubmit}
            loading={loading}
            size="lg"
            style={styles.submitBtn}
          />
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
  formCard: {
    marginHorizontal: Spacing.lg,
    marginTop: Spacing.md,
    borderRadius: BorderRadius.xl,
    padding: Spacing.xl,
    borderWidth: 1,
    ...Shadows.card,
  },
  formHeading: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
    marginBottom: 4,
  },
  formSub: {
    fontSize: Typography.size.xs,
    lineHeight: 18,
    marginBottom: Spacing.lg,
  },
  submitBtn: {
    marginTop: Spacing.md,
  },
});
