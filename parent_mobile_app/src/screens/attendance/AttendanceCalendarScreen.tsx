import React, { useEffect, useState } from 'react';
import {
  FlatList,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { AttendanceApi } from '../../api/attendanceApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { AttendanceStatCard } from '../../components/parent/AttendanceStatCard';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { AttendanceMonthlySummary, AttendanceRecord } from '../../types/attendance';
import { ParentStackParamList } from '../../types/navigation';
import { formatDate, formatTime, getAttendanceColor } from '../../utils/formatters';

export const AttendanceCalendarScreen: React.FC = () => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();
  const navigation = useNavigation<NativeStackNavigationProp<ParentStackParamList>>();

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [summary, setSummary] = useState<AttendanceMonthlySummary | null>(null);

  const loadAttendance = async () => {
    if (!activeChild) return;
    try {
      const data = await AttendanceApi.getMonthlyAttendance(activeChild.id);
      setSummary(data);
    } catch (e) {
      console.warn('Error loading attendance:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    loadAttendance();
  }, [activeChild?.id]);

  const onRefresh = () => {
    setRefreshing(true);
    loadAttendance();
  };

  const recordsList: AttendanceRecord[] = summary?.records
    ? Object.values(summary.records).sort(
        (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()
      )
    : [];

  const renderAttendanceItem = ({ item }: { item: AttendanceRecord }) => {
    const badge = getAttendanceColor(item.status);

    return (
      <View
        style={[
          styles.recordCard,
          {
            backgroundColor: colors.surface,
            borderColor: colors.borderSubtle,
          },
        ]}
      >
        <View style={styles.recordLeft}>
          <Text style={[styles.dateDay, { color: colors.text }]}>
            {formatDate(item.date)}
          </Text>
          {item.check_in_time && (
            <Text style={[styles.timeText, { color: colors.textMuted }]}>
              In: {formatTime(item.check_in_time)}{' '}
              {item.check_out_time ? `| Out: ${formatTime(item.check_out_time)}` : ''}
            </Text>
          )}
          {item.remark ? (
            <Text style={[styles.remarkText, { color: colors.textSecondary }]}>
              {item.remark}
            </Text>
          ) : null}
        </View>

        <View style={[styles.statusBadge, { backgroundColor: badge.bg }]}>
          <Text style={[styles.statusText, { color: badge.text }]}>
            {badge.label}
          </Text>
        </View>
      </View>
    );
  };

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <Text style={[styles.screenTitle, { color: colors.text }]}>Attendance Record</Text>
        <TouchableOpacity
          style={[styles.applyLeaveBtn, { backgroundColor: colors.primary }]}
          onPress={() => navigation.navigate('ApplyLeave')}
        >
          <Text style={styles.applyLeaveText}>+ Apply Leave</Text>
        </TouchableOpacity>
      </View>

      <ChildSwitcherBar />

      {loading ? (
        <LoadingIndicator message="Fetching attendance history..." />
      ) : (
        <FlatList
          data={recordsList}
          keyExtractor={(item) => item.id?.toString() || item.date}
          renderItem={renderAttendanceItem}
          ListHeaderComponent={
            <>
              <AttendanceStatCard summary={summary} />

              <View style={styles.sectionHeader}>
                <Text style={[styles.sectionTitle, { color: colors.text }]}>
                  Daily Attendance Log
                </Text>
                <TouchableOpacity onPress={() => navigation.navigate('LeaveHistory')}>
                  <Text style={[styles.leaveHistoryLink, { color: colors.primary }]}>
                    Leave History →
                  </Text>
                </TouchableOpacity>
              </View>
            </>
          }
          ListEmptyComponent={
            <EmptyState
              title="No Attendance Logs"
              description="No attendance records found for this academic period."
            />
          }
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={onRefresh}
              tintColor={colors.primary}
            />
          }
        />
      )}
    </ScreenWrapper>
  );
};

const styles = StyleSheet.create({
  topNav: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: Spacing.xl,
    paddingTop: Spacing.md,
    paddingBottom: Spacing.xs,
  },
  screenTitle: {
    fontSize: Typography.size.xl,
    fontWeight: Typography.weight.bold,
  },
  applyLeaveBtn: {
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.xs + 2,
    borderRadius: BorderRadius.full,
  },
  applyLeaveText: {
    color: '#ffffff',
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  listContent: {
    paddingBottom: Spacing.xxxl,
  },
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: Spacing.xl,
    marginTop: Spacing.lg,
    marginBottom: Spacing.xs,
  },
  sectionTitle: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  leaveHistoryLink: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.semibold,
  },
  recordCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: Spacing.md,
    marginHorizontal: Spacing.lg,
    marginVertical: Spacing.xs,
    borderRadius: BorderRadius.lg,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  recordLeft: {
    flex: 1,
  },
  dateDay: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.semibold,
  },
  timeText: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  remarkText: {
    fontSize: Typography.size.xs,
    marginTop: 2,
    fontStyle: 'italic',
  },
  statusBadge: {
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.xs,
    borderRadius: BorderRadius.full,
    marginLeft: Spacing.sm,
  },
  statusText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
});
