import React from 'react';
import {
  Linking,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';
import { formatDate } from '../../utils/formatters';

type Props = NativeStackScreenProps<ParentStackParamList, 'HomeworkDetail'>;

export const HomeworkDetailScreen: React.FC<Props> = ({ navigation, route }) => {
  const { colors } = useTheme();
  const { homework } = route.params;

  return (
    <ScreenWrapper>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.topNav}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
          </TouchableOpacity>
          <Text style={[styles.headerTitle, { color: colors.text }]}>Homework Details</Text>
          <View style={{ width: 40 }} />
        </View>

        <View
          style={[
            styles.card,
            {
              backgroundColor: colors.surface,
              borderColor: colors.borderSubtle,
            },
          ]}
        >
          <View style={styles.badgeRow}>
            <View style={[styles.subjectBadge, { backgroundColor: colors.primaryLight + '20' }]}>
              <Text style={[styles.subjectText, { color: colors.primary }]}>
                {homework.subject_name}
              </Text>
            </View>

            <View
              style={[
                styles.statusBadge,
                {
                  backgroundColor: homework.is_submitted
                    ? colors.successLight
                    : colors.warningLight,
                },
              ]}
            >
              <Text
                style={[
                  styles.statusText,
                  {
                    color: homework.is_submitted ? colors.success : colors.warning,
                  },
                ]}
              >
                {homework.is_submitted ? 'SUBMITTED' : 'PENDING SUBMISSION'}
              </Text>
            </View>
          </View>

          <Text style={[styles.title, { color: colors.text }]}>{homework.title}</Text>

          <View style={styles.datesRow}>
            <View style={styles.dateBox}>
              <Text style={[styles.dateLabel, { color: colors.textMuted }]}>Assigned On</Text>
              <Text style={[styles.dateVal, { color: colors.text }]}>
                {formatDate(homework.assigned_date)}
              </Text>
            </View>

            <View style={styles.dateBox}>
              <Text style={[styles.dateLabel, { color: colors.textMuted }]}>Submission Due</Text>
              <Text style={[styles.dateVal, { color: colors.danger }]}>
                {formatDate(homework.submission_date)}
              </Text>
            </View>
          </View>

          {homework.teacher_name && (
            <Text style={[styles.teacherInfo, { color: colors.textSecondary }]}>
              Assigned by teacher: <Text style={{ fontWeight: 'bold' }}>{homework.teacher_name}</Text>
            </Text>
          )}

          <View style={[styles.divider, { backgroundColor: colors.borderSubtle }]} />

          <Text style={[styles.sectionHeading, { color: colors.text }]}>Assignment Instructions:</Text>
          <Text style={[styles.description, { color: colors.textSecondary }]}>
            {homework.description || 'No detailed instructions provided.'}
          </Text>

          {homework.attachment_url ? (
            <TouchableOpacity
              style={[styles.attachmentBtn, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border }]}
              onPress={() => Linking.openURL(homework.attachment_url!)}
            >
              <Text style={{ fontSize: 20, marginRight: Spacing.sm }}>📎</Text>
              <View style={{ flex: 1 }}>
                <Text style={[styles.attachmentTitle, { color: colors.text }]}>
                  View Assignment Attachment
                </Text>
                <Text style={[styles.attachmentSub, { color: colors.textMuted }]}>
                  PDF / Worksheet / Reference Material
                </Text>
              </View>
              <Text style={[styles.arrow, { color: colors.primary }]}>Download ↓</Text>
            </TouchableOpacity>
          ) : null}

          {homework.evaluation_remarks ? (
            <View style={[styles.evaluationBox, { backgroundColor: colors.infoLight }]}>
              <Text style={[styles.evalTitle, { color: colors.info }]}>Teacher Feedback & Remarks</Text>
              <Text style={[styles.evalRemarks, { color: colors.text }]}>
                {homework.evaluation_remarks}
              </Text>
              {homework.marks_obtained !== undefined && homework.total_marks !== undefined && (
                <Text style={[styles.evalMarks, { color: colors.text }]}>
                  Marks: {homework.marks_obtained} / {homework.total_marks}
                </Text>
              )}
            </View>
          ) : null}
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
  card: {
    marginHorizontal: Spacing.lg,
    borderRadius: BorderRadius.xl,
    padding: Spacing.xl,
    borderWidth: 1,
    ...Shadows.card,
  },
  badgeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.md,
  },
  subjectBadge: {
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.xs,
    borderRadius: BorderRadius.sm,
  },
  subjectText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  statusBadge: {
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.xs,
    borderRadius: BorderRadius.sm,
  },
  statusText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
  },
  title: {
    fontSize: Typography.size.xl,
    fontWeight: Typography.weight.bold,
    marginBottom: Spacing.md,
  },
  datesRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.sm,
  },
  dateBox: {
    flex: 1,
  },
  dateLabel: {
    fontSize: 11,
  },
  dateVal: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
    marginTop: 2,
  },
  teacherInfo: {
    fontSize: Typography.size.xs,
    marginTop: Spacing.xs,
  },
  divider: {
    height: 1,
    marginVertical: Spacing.lg,
  },
  sectionHeading: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
    marginBottom: Spacing.xs,
  },
  description: {
    fontSize: Typography.size.sm,
    lineHeight: 22,
  },
  attachmentBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: Spacing.md,
    borderRadius: BorderRadius.lg,
    borderWidth: 1,
    marginTop: Spacing.lg,
  },
  attachmentTitle: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  attachmentSub: {
    fontSize: 10,
  },
  arrow: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  evaluationBox: {
    marginTop: Spacing.lg,
    padding: Spacing.md,
    borderRadius: BorderRadius.lg,
  },
  evalTitle: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
    marginBottom: 4,
  },
  evalRemarks: {
    fontSize: Typography.size.xs,
    lineHeight: 18,
  },
  evalMarks: {
    fontSize: Typography.size.xs,
    fontWeight: 'bold',
    marginTop: 4,
  },
});
