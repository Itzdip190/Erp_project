import React from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useTheme } from '../../context/ThemeContext';
import { FeeSummary } from '../../types/fee';
import { formatCurrency, formatDate } from '../../utils/formatters';

interface FeeSummaryCardProps {
  feeSummary: FeeSummary | null;
  onPressPay?: () => void;
  onPressViewAll?: () => void;
}

export const FeeSummaryCard: React.FC<FeeSummaryCardProps> = ({
  feeSummary,
  onPressPay,
  onPressViewAll,
}) => {
  const { colors } = useTheme();

  const totalDue = feeSummary?.total_due ?? 0;
  const totalPaid = feeSummary?.total_paid ?? 0;
  const currency = feeSummary?.currency_symbol || '₹';
  const hasDue = totalDue > 0;

  return (
    <View
      style={[
        styles.card,
        {
          backgroundColor: hasDue ? colors.surface : colors.surface,
          borderColor: hasDue ? colors.danger + '30' : colors.borderSubtle,
        },
      ]}
    >
      <View style={styles.topRow}>
        <View>
          <Text style={[styles.label, { color: colors.textSecondary }]}>Fee Dues</Text>
          <Text
            style={[
              styles.dueAmount,
              { color: hasDue ? colors.danger : colors.success },
            ]}
          >
            {formatCurrency(totalDue, currency)}
          </Text>
        </View>

        {hasDue ? (
          <TouchableOpacity
            activeOpacity={0.8}
            onPress={onPressPay}
            style={[styles.payButton, { backgroundColor: colors.primary }]}
          >
            <Text style={styles.payButtonText}>Pay Now</Text>
          </TouchableOpacity>
        ) : (
          <View style={[styles.clearedBadge, { backgroundColor: colors.successLight }]}>
            <Text style={[styles.clearedBadgeText, { color: colors.success }]}>
              All Cleared
            </Text>
          </View>
        )}
      </View>

      <View style={[styles.bottomRow, { borderTopColor: colors.borderSubtle }]}>
        <Text style={[styles.paidInfo, { color: colors.textSecondary }]}>
          Total Paid: <Text style={{ fontWeight: 'bold' }}>{formatCurrency(totalPaid, currency)}</Text>
        </Text>

        {feeSummary?.upcoming_due_date && hasDue && (
          <Text style={[styles.dueDate, { color: colors.warning }]}>
            Due by {formatDate(feeSummary.upcoming_due_date)}
          </Text>
        )}

        {onPressViewAll && (
          <TouchableOpacity onPress={onPressViewAll}>
            <Text style={[styles.viewAllText, { color: colors.primary }]}>
              View Details →
            </Text>
          </TouchableOpacity>
        )}
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
  topRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.md,
  },
  label: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.medium,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  dueAmount: {
    fontSize: Typography.size.xxl,
    fontWeight: Typography.weight.bold,
    marginTop: 2,
  },
  payButton: {
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.sm + 2,
    borderRadius: BorderRadius.md,
  },
  payButtonText: {
    color: '#ffffff',
    fontWeight: Typography.weight.bold,
    fontSize: Typography.size.sm,
  },
  clearedBadge: {
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.xs + 2,
    borderRadius: BorderRadius.full,
  },
  clearedBadgeText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  bottomRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    paddingTop: Spacing.sm + 2,
  },
  paidInfo: {
    fontSize: Typography.size.xs,
  },
  dueDate: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.semibold,
  },
  viewAllText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
});
