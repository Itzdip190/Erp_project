import React from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useTheme } from '../../context/ThemeContext';
import { HomeworkItem } from '../../types/academic';
import { formatDate } from '../../utils/formatters';

interface HomeworkCardProps {
  item: HomeworkItem;
  onPress: () => void;
}

export const HomeworkCard: React.FC<HomeworkCardProps> = ({ item, onPress }) => {
  const { colors } = useTheme();

  return (
    <TouchableOpacity
      activeOpacity={0.7}
      onPress={onPress}
      style={[
        styles.card,
        {
          backgroundColor: colors.surface,
          borderColor: colors.borderSubtle,
        },
      ]}
    >
      <View style={styles.topRow}>
        <View style={[styles.subjectTag, { backgroundColor: colors.primaryLight + '20' }]}>
          <Text style={[styles.subjectText, { color: colors.primary }]}>
            {item.subject_name}
          </Text>
        </View>

        <View
          style={[
            styles.statusTag,
            {
              backgroundColor: item.is_submitted
                ? colors.successLight
                : colors.warningLight,
            },
          ]}
        >
          <Text
            style={[
              styles.statusText,
              {
                color: item.is_submitted ? colors.success : colors.warning,
              },
            ]}
          >
            {item.is_submitted ? 'Submitted' : 'Pending'}
          </Text>
        </View>
      </View>

      <Text style={[styles.title, { color: colors.text }]} numberOfLines={2}>
        {item.title}
      </Text>

      {item.description ? (
        <Text style={[styles.description, { color: colors.textSecondary }]} numberOfLines={2}>
          {item.description}
        </Text>
      ) : null}

      <View style={[styles.bottomRow, { borderTopColor: colors.borderSubtle }]}>
        <Text style={[styles.dateText, { color: colors.textMuted }]}>
          Due: {formatDate(item.submission_date)}
        </Text>

        {item.teacher_name && (
          <Text style={[styles.teacherText, { color: colors.textSecondary }]}>
            By: {item.teacher_name}
          </Text>
        )}
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  card: {
    borderRadius: BorderRadius.lg,
    padding: Spacing.md,
    marginVertical: Spacing.xs + 2,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  topRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.xs + 2,
  },
  subjectTag: {
    paddingHorizontal: Spacing.sm,
    paddingVertical: 2,
    borderRadius: BorderRadius.xs,
  },
  subjectText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  statusTag: {
    paddingHorizontal: Spacing.sm,
    paddingVertical: 2,
    borderRadius: BorderRadius.xs,
  },
  statusText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.semibold,
  },
  title: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.semibold,
    marginBottom: 4,
  },
  description: {
    fontSize: Typography.size.xs,
    lineHeight: 18,
    marginBottom: Spacing.sm,
  },
  bottomRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    paddingTop: Spacing.xs + 2,
  },
  dateText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.medium,
  },
  teacherText: {
    fontSize: Typography.size.xs,
  },
});
