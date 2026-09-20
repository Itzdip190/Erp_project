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
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { AcademicsApi } from '../../api/academicsApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { DayTimetable, TimetablePeriod } from '../../types/academic';
import { ParentStackParamList } from '../../types/navigation';
import { formatTime } from '../../utils/formatters';

type Props = NativeStackScreenProps<ParentStackParamList, 'Timetable'>;

const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as const;

export const TimetableScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();

  const [timetable, setTimetable] = useState<DayTimetable[]>([]);
  const [selectedDay, setSelectedDay] = useState<string>('Monday');
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchTimetable = async () => {
    if (!activeChild) return;
    try {
      const data = await AcademicsApi.getTimetable(activeChild.id);
      setTimetable(data);
    } catch (e) {
      console.warn('Error fetching timetable:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchTimetable();
  }, [activeChild?.id]);

  const currentDaySchedule = timetable.find((d) => d.day.toLowerCase() === selectedDay.toLowerCase());
  const periods: TimetablePeriod[] = currentDaySchedule?.periods || [];

  const renderPeriodItem = ({ item }: { item: TimetablePeriod }) => (
    <View
      style={[
        styles.periodCard,
        {
          backgroundColor: colors.surface,
          borderColor: colors.borderSubtle,
        },
      ]}
    >
      <View style={styles.periodNumberBox}>
        <View style={[styles.periodNumBadge, { backgroundColor: colors.primaryLight + '20' }]}>
          <Text style={[styles.periodNumText, { color: colors.primary }]}>P{item.period_number}</Text>
        </View>
        <Text style={[styles.timeText, { color: colors.textMuted }]}>
          {formatTime(item.start_time)} - {formatTime(item.end_time)}
        </Text>
      </View>

      <View style={styles.periodDetails}>
        <Text style={[styles.subjectName, { color: colors.text }]}>{item.subject_name}</Text>
        {item.teacher_name && (
          <Text style={[styles.teacherName, { color: colors.textSecondary }]}>
            Teacher: {item.teacher_name}
          </Text>
        )}
        {item.room_number && (
          <Text style={[styles.roomText, { color: colors.textMuted }]}>
            Room: {item.room_number}
          </Text>
        )}
      </View>
    </View>
  );

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Class Timetable</Text>
        <View style={{ width: 40 }} />
      </View>

      <ChildSwitcherBar />

      {/* Days Horizontal Tab Bar */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.daysScroll}
      >
        {DAYS.map((day) => {
          const isSelected = selectedDay === day;
          return (
            <TouchableOpacity
              key={day}
              activeOpacity={0.8}
              onPress={() => setSelectedDay(day)}
              style={[
                styles.dayTab,
                {
                  backgroundColor: isSelected ? colors.primary : colors.surfaceSubtle,
                },
              ]}
            >
              <Text
                style={[
                  styles.dayTabText,
                  { color: isSelected ? '#ffffff' : colors.textSecondary },
                ]}
              >
                {day.slice(0, 3)}
              </Text>
            </TouchableOpacity>
          );
        })}
      </ScrollView>

      {loading ? (
        <LoadingIndicator message="Loading class schedule..." />
      ) : periods.length === 0 ? (
        <EmptyState
          title={`No Periods for ${selectedDay}`}
          description="There is no timetable scheduled for this day or it is an off-day."
        />
      ) : (
        <FlatList
          data={periods}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderPeriodItem}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => {
                setRefreshing(true);
                fetchTimetable();
              }}
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
  daysScroll: {
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.xs,
  },
  dayTab: {
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.sm,
    borderRadius: BorderRadius.full,
    marginRight: Spacing.sm,
  },
  dayTabText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
    textTransform: 'uppercase',
  },
  listContent: {
    paddingHorizontal: Spacing.lg,
    paddingTop: Spacing.md,
    paddingBottom: Spacing.xxxl,
  },
  periodCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: Spacing.md,
    marginVertical: Spacing.xs + 2,
    borderRadius: BorderRadius.lg,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  periodNumberBox: {
    width: 90,
    alignItems: 'center',
    justifyContent: 'center',
    paddingRight: Spacing.sm,
  },
  periodNumBadge: {
    paddingHorizontal: Spacing.sm,
    paddingVertical: 2,
    borderRadius: BorderRadius.xs,
    marginBottom: 4,
  },
  periodNumText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
  },
  timeText: {
    fontSize: 9,
    textAlign: 'center',
  },
  periodDetails: {
    flex: 1,
    borderLeftWidth: 1,
    borderLeftColor: '#e2e8f0',
    paddingLeft: Spacing.md,
  },
  subjectName: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
  },
  teacherName: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  roomText: {
    fontSize: 10,
    marginTop: 2,
  },
});
