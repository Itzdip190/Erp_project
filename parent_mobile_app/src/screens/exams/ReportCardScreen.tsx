import React, { useEffect, useState } from 'react';
import {
  FlatList,
  Linking,
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
import { ReportCard, SubjectMark } from '../../types/academic';
import { ParentStackParamList } from '../../types/navigation';

type Props = NativeStackScreenProps<ParentStackParamList, 'ReportCard'>;

export const ReportCardScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();

  const [reportCards, setReportCards] = useState<ReportCard[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchReports = async () => {
    if (!activeChild) return;
    try {
      const data = await ExamsApi.getReportCards(activeChild.id);
      setReportCards(data);
    } catch (e) {
      console.warn('Error fetching report cards:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchReports();
  }, [activeChild?.id]);

  const renderSubjectMarkRow = (subj: SubjectMark, idx: number) => (
    <View key={idx} style={styles.subjRow}>
      <Text style={[styles.subjName, { color: colors.text }]}>{subj.subject_name}</Text>
      <Text style={[styles.subjMarks, { color: colors.text }]}>
        {subj.marks_obtained} / {subj.max_marks}
      </Text>
      <View style={[styles.gradeBadge, { backgroundColor: colors.surfaceSubtle }]}>
        <Text style={[styles.gradeText, { color: colors.primary }]}>{subj.grade || 'A'}</Text>
      </View>
    </View>
  );

  const renderReportCard = ({ item }: { item: ReportCard }) => (
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
        <View>
          <Text style={[styles.examTitle, { color: colors.text }]}>{item.exam_name}</Text>
          <Text style={[styles.sessionText, { color: colors.textMuted }]}>
            Academic Session: {item.academic_session}
          </Text>
        </View>

        <View style={[styles.overallBadge, { backgroundColor: colors.successLight }]}>
          <Text style={[styles.overallGrade, { color: colors.success }]}>
            Grade {item.grade}
          </Text>
        </View>
      </View>

      <View style={[styles.scoreSummary, { backgroundColor: colors.surfaceSubtle }]}>
        <View style={styles.summaryCol}>
          <Text style={[styles.summaryNum, { color: colors.primary }]}>
            {item.obtained_marks}/{item.total_marks}
          </Text>
          <Text style={[styles.summaryLabel, { color: colors.textMuted }]}>Total Marks</Text>
        </View>

        <View style={styles.summaryDivider} />

        <View style={styles.summaryCol}>
          <Text style={[styles.summaryNum, { color: colors.success }]}>
            {item.percentage}%
          </Text>
          <Text style={[styles.summaryLabel, { color: colors.textMuted }]}>Percentage</Text>
        </View>

        {item.rank && (
          <>
            <View style={styles.summaryDivider} />
            <View style={styles.summaryCol}>
              <Text style={[styles.summaryNum, { color: colors.warning }]}>
                #{item.rank}
              </Text>
              <Text style={[styles.summaryLabel, { color: colors.textMuted }]}>Class Rank</Text>
            </View>
          </>
        )}
      </View>

      {/* Subject Marks Breakdown */}
      <View style={styles.subjectList}>
        <Text style={[styles.tableHeader, { color: colors.textSecondary }]}>
          Subject Performance
        </Text>
        {item.subjects && item.subjects.map(renderSubjectMarkRow)}
      </View>

      {item.teacher_remarks ? (
        <View style={[styles.remarksBox, { borderTopColor: colors.borderSubtle }]}>
          <Text style={[styles.remarksLabel, { color: colors.textMuted }]}>
            Teacher Remark:
          </Text>
          <Text style={[styles.remarksContent, { color: colors.textSecondary }]}>
            "{item.teacher_remarks}"
          </Text>
        </View>
      ) : null}

      {item.download_url ? (
        <TouchableOpacity
          style={[styles.downloadBtn, { backgroundColor: colors.primary }]}
          onPress={() => Linking.openURL(item.download_url!)}
        >
          <Text style={styles.downloadBtnText}>📄 Download Official Marksheet PDF</Text>
        </TouchableOpacity>
      ) : null}
    </View>
  );

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Academic Report Cards</Text>
        <View style={{ width: 40 }} />
      </View>

      <ChildSwitcherBar />

      {loading ? (
        <LoadingIndicator message="Fetching marks and report cards..." />
      ) : reportCards.length === 0 ? (
        <EmptyState
          title="No Report Cards"
          description="Evaluation results and published report cards will appear here once finalized by the school."
        />
      ) : (
        <FlatList
          data={reportCards}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderReportCard}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => {
                setRefreshing(true);
                fetchReports();
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
  listContent: {
    paddingHorizontal: Spacing.lg,
    paddingBottom: Spacing.xxxl,
  },
  card: {
    borderRadius: BorderRadius.xl,
    padding: Spacing.lg,
    marginVertical: Spacing.sm,
    borderWidth: 1,
    ...Shadows.card,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.md,
  },
  examTitle: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  sessionText: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  overallBadge: {
    paddingHorizontal: Spacing.md,
    paddingVertical: 3,
    borderRadius: BorderRadius.full,
  },
  overallGrade: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  scoreSummary: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-around',
    paddingVertical: Spacing.md,
    borderRadius: BorderRadius.lg,
    marginBottom: Spacing.md,
  },
  summaryCol: {
    alignItems: 'center',
  },
  summaryNum: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  summaryLabel: {
    fontSize: 10,
    marginTop: 2,
    textTransform: 'uppercase',
  },
  summaryDivider: {
    width: 1,
    height: 24,
    backgroundColor: '#cbd5e1',
  },
  subjectList: {
    marginBottom: Spacing.sm,
  },
  tableHeader: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
    textTransform: 'uppercase',
    marginBottom: Spacing.xs,
  },
  subjRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 4,
  },
  subjName: {
    flex: 1,
    fontSize: Typography.size.xs,
  },
  subjMarks: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.semibold,
    marginRight: Spacing.md,
  },
  gradeBadge: {
    width: 28,
    height: 22,
    borderRadius: 4,
    alignItems: 'center',
    justifyContent: 'center',
  },
  gradeText: {
    fontSize: 10,
    fontWeight: 'bold',
  },
  remarksBox: {
    borderTopWidth: 1,
    paddingTop: Spacing.xs + 2,
    marginTop: Spacing.xs,
  },
  remarksLabel: {
    fontSize: 10,
  },
  remarksContent: {
    fontSize: Typography.size.xs,
    fontStyle: 'italic',
    marginTop: 2,
  },
  downloadBtn: {
    marginTop: Spacing.md,
    paddingVertical: Spacing.sm + 2,
    borderRadius: BorderRadius.md,
    alignItems: 'center',
    justifyContent: 'center',
  },
  downloadBtnText: {
    color: '#ffffff',
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
});
