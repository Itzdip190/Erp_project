import React from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useTheme } from '../../context/ThemeContext';
import { AttendanceMonthlySummary } from '../../types/attendance';

interface AttendanceStatCardProps {
  summary: AttendanceMonthlySummary | null;
  onPressDetails?: () => void;
}

export const AttendanceStatCard: React.FC<AttendanceStatCardProps> = ({ summary }) => {
  const { colors } = useTheme();

  const percentage = summary?.percentage ?? 0;
  const presentDays = summary?.present_days ?? 0;
  const absentDays = summary?.absent_days ?? 0;
  const lateDays = summary?.late_days ?? 0;

  const getPercentageColor = (pct: number): string => {
    if (pct >= 85) return colors.success;
    if (pct >= 75) return colors.warning;
    return colors.danger;
  };

  return (
    <View
      style={[
        styles.card,
        {
          backgroundColor: colors.surface,
          borderColor: colors.borderSubtle,
        },
      ]}
    >
      <View style={styles.headerRow}>
        <View>
          <Text style={[styles.title, { color: colors.text }]}>Attendance Overview</Text>
          <Text style={[styles.subtitle, { color: colors.textSecondary }]}>
            {summary?.month ? `Month of ${summary.month}` : 'Current Month'}
          </Text>
        </View>

        <View
          style={[
            styles.percentageCircle,
            {
              backgroundColor: getPercentageColor(percentage) + '15',
              borderColor: getPercentageColor(percentage),
            },
          ]}
        >
          <Text style={[styles.percentageText, { color: getPercentageColor(percentage) }]}>
            {Math.round(percentage)}%
          </Text>
        </View>
      </View>

      <View style={[styles.statsRow, { borderTopColor: colors.borderSubtle }]}>
        <View style={styles.statItem}>
          <Text style={[styles.statValue, { color: colors.success }]}>{presentDays}</Text>
          <Text style={[styles.statLabel, { color: colors.textMuted }]}>Present</Text>
        </View>

        <View style={styles.statDivider} />

        <View style={styles.statItem}>
          <Text style={[styles.statValue, { color: colors.danger }]}>{absentDays}</Text>
          <Text style={[styles.statLabel, { color: colors.textMuted }]}>Absent</Text>
        </View>

        <View style={styles.statDivider} />

        <View style={styles.statItem}>
          <Text style={[styles.statValue, { color: colors.warning }]}>{lateDays}</Text>
          <Text style={[styles.statLabel, { color: colors.textMuted }]}>Late</Text>
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    borderRadius: BorderRadius.xl,
    padding: Spacing.lg,
    marginHorizontal: Spacing.lg,
    marginVertical: Spacing.xs,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  headerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.md,
  },
  title: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  subtitle: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  percentageCircle: {
    width: 52,
    height: 52,
    borderRadius: 26,
    borderWidth: 2.5,
    alignItems: 'center',
    justifyContent: 'center',
  },
  percentageText: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  statsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-around',
    borderTopWidth: 1,
    paddingTop: Spacing.md,
  },
  statItem: {
    alignItems: 'center',
    flex: 1,
  },
  statValue: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  statLabel: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  statDivider: {
    width: 1,
    height: 24,
    backgroundColor: '#e2e8f0',
  },
});
