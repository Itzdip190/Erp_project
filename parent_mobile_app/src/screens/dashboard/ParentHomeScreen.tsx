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
import { FeesApi } from '../../api/feesApi';
import { NoticesApi } from '../../api/noticesApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { AttendanceStatCard } from '../../components/parent/AttendanceStatCard';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { FeeSummaryCard } from '../../components/parent/FeeSummaryCard';
import { NoticeCard } from '../../components/parent/NoticeCard';
import { ActionItem, QuickActionGrid } from '../../components/parent/QuickActionGrid';
import { BorderRadius, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { AttendanceMonthlySummary } from '../../types/attendance';
import { FeeSummary } from '../../types/fee';
import { ParentStackParamList } from '../../types/navigation';
import { NoticeItem } from '../../types/notice';

export const ParentHomeScreen: React.FC = () => {
  const { colors } = useTheme();
  const { user, school } = useAuth();
  const { activeChild, isLoadingChildren, refreshChildren } = useActiveChild();
  const navigation = useNavigation<NativeStackNavigationProp<ParentStackParamList>>();

  const [refreshing, setRefreshing] = useState(false);
  const [attendanceSummary, setAttendanceSummary] = useState<AttendanceMonthlySummary | null>(null);
  const [feeSummary, setFeeSummary] = useState<FeeSummary | null>(null);
  const [latestNotices, setLatestNotices] = useState<NoticeItem[]>([]);
  const [loadingStats, setLoadingStats] = useState(false);

  const loadChildData = async () => {
    if (!activeChild) return;
    setLoadingStats(true);
    try {
      const [att, fee, notices] = await Promise.allSettled([
        AttendanceApi.getMonthlyAttendance(activeChild.id),
        FeesApi.getFees(activeChild.id),
        NoticesApi.getNotices(activeChild.id),
      ]);

      if (att.status === 'fulfilled') setAttendanceSummary(att.value);
      if (fee.status === 'fulfilled') setFeeSummary(fee.value);
      if (notices.status === 'fulfilled') setLatestNotices(notices.value.slice(0, 3));
    } catch (e) {
      console.warn('Error loading dashboard stats:', e);
    } finally {
      setLoadingStats(false);
    }
  };

  useEffect(() => {
    loadChildData();
  }, [activeChild?.id]);

  const onRefresh = async () => {
    setRefreshing(true);
    await Promise.all([refreshChildren(), loadChildData()]);
    setRefreshing(false);
  };

  const quickActions: ActionItem[] = [
    {
      id: 'attendance',
      title: 'Attendance',
      emoji: '📅',
      color: '#10b981',
      onPress: () => navigation.navigate('AttendanceCalendar'),
    },
    {
      id: 'homework',
      title: 'Homework',
      emoji: '📝',
      color: '#3b82f6',
      onPress: () => navigation.navigate('HomeworkList'),
    },
    {
      id: 'diary',
      title: 'Class Diary',
      emoji: '📖',
      color: '#8b5cf6',
      onPress: () => navigation.navigate('DigitalDiary'),
    },
    {
      id: 'timetable',
      title: 'Timetable',
      emoji: '⏰',
      color: '#f59e0b',
      onPress: () => navigation.navigate('Timetable'),
    },
    {
      id: 'fees',
      title: 'Fee Dues',
      emoji: '💳',
      color: '#ec4899',
      onPress: () => navigation.navigate('FeesOverview'),
      badge: feeSummary && feeSummary.total_due > 0 ? 'Due' : undefined,
    },
    {
      id: 'transport',
      title: 'Bus Tracking',
      emoji: '🚌',
      color: '#06b6d4',
      onPress: () => navigation.navigate('BusTracking'),
    },
    {
      id: 'exams',
      title: 'Results / Exams',
      emoji: '📊',
      color: '#6366f1',
      onPress: () => navigation.navigate('ExamSchedule'),
    },
    {
      id: 'leave',
      title: 'Apply Leave',
      emoji: '✉️',
      color: '#14b8a6',
      onPress: () => navigation.navigate('ApplyLeave'),
    },
  ];

  return (
    <ScreenWrapper>
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            tintColor={colors.primary}
            colors={[colors.primary]}
          />
        }
      >
        {/* Header with School Branding & Parent Greeting */}
        <View style={styles.topHeader}>
          <View>
            <Text style={[styles.schoolName, { color: colors.primary }]} numberOfLines={1}>
              {school?.name || 'School ERP'}
            </Text>
            <Text style={[styles.parentGreeting, { color: colors.text }]}>
              Hello, {user?.name || 'Parent'} 👋
            </Text>
          </View>

          <TouchableOpacity
            style={[styles.chatIconButton, { backgroundColor: colors.surfaceSubtle }]}
            onPress={() => navigation.navigate('ParentChat')}
          >
            <Text style={{ fontSize: 20 }}>💬</Text>
          </TouchableOpacity>
        </View>

        {/* Active Child Switcher Bar */}
        <ChildSwitcherBar />

        {!activeChild && !isLoadingChildren && (
          <EmptyState
            title="No Child Linked"
            description="We could not find any active student profile associated with this account. Please contact the school administrative office."
            actionTitle="Refresh"
            onAction={onRefresh}
          />
        )}

        {activeChild && (
          <>
            {/* Quick Action Grid */}
            <View style={styles.sectionHeader}>
              <Text style={[styles.sectionTitle, { color: colors.text }]}>
                Quick Actions
              </Text>
            </View>
            <QuickActionGrid actions={quickActions} />

            {/* Attendance & Fee Overviews */}
            <View style={styles.sectionHeader}>
              <Text style={[styles.sectionTitle, { color: colors.text }]}>
                Academic & Fee Status
              </Text>
            </View>

            <AttendanceStatCard
              summary={attendanceSummary}
              onPressDetails={() => navigation.navigate('AttendanceCalendar')}
            />

            <FeeSummaryCard
              feeSummary={feeSummary}
              onPressPay={() => navigation.navigate('FeesOverview')}
              onPressViewAll={() => navigation.navigate('FeesOverview')}
            />

            {/* Latest Notices */}
            <View style={[styles.sectionHeader, { marginTop: Spacing.md }]}>
              <Text style={[styles.sectionTitle, { color: colors.text }]}>
                Latest Notices & Circulars
              </Text>
              <TouchableOpacity onPress={() => navigation.navigate('NoticesList')}>
                <Text style={[styles.seeAllText, { color: colors.primary }]}>
                  View All →
                </Text>
              </TouchableOpacity>
            </View>

            <View style={styles.noticesContainer}>
              {latestNotices.length > 0 ? (
                latestNotices.map((notice) => (
                  <NoticeCard
                    key={notice.id}
                    notice={notice}
                    onPress={() => navigation.navigate('NoticeDetail', { notice })}
                  />
                ))
              ) : (
                <View style={styles.noNoticesBox}>
                  <Text style={[styles.noNoticesText, { color: colors.textMuted }]}>
                    No new circulars or notices for today.
                  </Text>
                </View>
              )}
            </View>
          </>
        )}
      </ScrollView>
    </ScreenWrapper>
  );
};

const styles = StyleSheet.create({
  scrollContent: {
    paddingBottom: Spacing.xxxl,
  },
  topHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: Spacing.xl,
    paddingTop: Spacing.md,
    paddingBottom: Spacing.xs,
  },
  schoolName: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
    letterSpacing: 0.5,
    textTransform: 'uppercase',
  },
  parentGreeting: {
    fontSize: Typography.size.xl,
    fontWeight: Typography.weight.bold,
    marginTop: 2,
  },
  chatIconButton: {
    width: 44,
    height: 44,
    borderRadius: 22,
    alignItems: 'center',
    justifyContent: 'center',
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
  seeAllText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  noticesContainer: {
    paddingHorizontal: Spacing.lg,
  },
  noNoticesBox: {
    padding: Spacing.lg,
    alignItems: 'center',
    justifyContent: 'center',
  },
  noNoticesText: {
    fontSize: Typography.size.xs,
  },
});
