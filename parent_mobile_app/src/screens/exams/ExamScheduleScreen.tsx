import React, { useEffect, useState } from 'react';
import {
  FlatList,
  RefreshControl,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { ExamsApi } from '../../api/examsApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { ExamScheduleItem } from '../../types/academic';
import { ParentStackParamList } from '../../types/navigation';
import { formatDate, formatTime } from '../../utils/formatters';

type Props = NativeStackScreenProps<ParentStackParamList, 'ExamSchedule'>;

export const ExamScheduleScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();

  const [exams, setExams] = useState<ExamScheduleItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchExams = async () => {
    if (!activeChild) return;
    try {
      const list = await ExamsApi.getExamSchedules(activeChild.id);
      setExams(list);
    } catch (e) {
      console.warn('Error fetching exam schedules:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchExams();
  }, [activeChild?.id]);

  const renderExamCard = ({ item }: { item: ExamScheduleItem }) => (
    <View
      style={[
        styles.card,
        {
          backgroundColor: colors.surface,
          borderColor: colors.borderSubtle,
        },
      ]}
    >
      <View style={styles.cardHeader}>
        <View style={[styles.examBadge, { backgroundColor: colors.primaryLight + '20' }]}>
          <Text style={[styles.examBadgeText, { color: colors.primary }]}>{item.exam_name}</Text>
        </View>

        <Text style={[styles.dateText, { color: colors.danger }]}>
          {formatDate(item.exam_date)}
        </Text>
      </View>

      <Text style={[styles.subjectName, { color: colors.text }]}>{item.subject_name}</Text>

      <View style={[styles.detailsRow, { borderTopColor: colors.borderSubtle }]}>
        <Text style={[styles.timeText, { color: colors.textSecondary }]}>
          ⏰ {formatTime(item.start_time)} - {formatTime(item.end_time)}
        </Text>

        <Text style={[styles.marksText, { color: colors.textMuted }]}>
          Max Marks: <Text style={{ fontWeight: 'bold', color: colors.text }}>{item.max_marks}</Text> (Pass: {item.passing_marks})
        </Text>
      </View>
    </View>
  );

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Exams & Timetable</Text>
        <TouchableOpacity onPress={() => navigation.navigate('ReportCard')}>
          <Text style={[styles.resultsLink, { color: colors.primary }]}>Results 📊</Text>
        </TouchableOpacity>
      </View>

      <ChildSwitcherBar />

      {loading ? (
        <LoadingIndicator message="Fetching upcoming examination schedules..." />
      ) : exams.length === 0 ? (
        <EmptyState
          title="No Upcoming Exams"
          description="There are currently no active exam schedules announced for this class."
          actionTitle="View Term Report Cards"
          onAction={() => navigation.navigate('ReportCard')}
        />
      ) : (
        <FlatList
          data={exams}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderExamCard}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => {
                setRefreshing(true);
                fetchExams();
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
  resultsLink: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  listContent: {
    paddingHorizontal: Spacing.lg,
    paddingBottom: Spacing.xxxl,
  },
  card: {
    borderRadius: BorderRadius.lg,
    padding: Spacing.md,
    marginVertical: Spacing.xs + 2,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.xs + 2,
  },
  examBadge: {
    paddingHorizontal: Spacing.sm,
    paddingVertical: 2,
    borderRadius: BorderRadius.xs,
  },
  examBadgeText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
  },
  dateText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  subjectName: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
    marginBottom: Spacing.sm,
  },
  detailsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    paddingTop: Spacing.xs + 2,
  },
  timeText: {
    fontSize: Typography.size.xs,
  },
  marksText: {
    fontSize: 11,
  },
});
