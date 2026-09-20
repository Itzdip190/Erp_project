import React, { useEffect, useState } from 'react';
import {
  FlatList,
  RefreshControl,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { FeesApi } from '../../api/feesApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { FeeSummaryCard } from '../../components/parent/FeeSummaryCard';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { FeeInvoice, FeeSummary } from '../../types/fee';
import { ParentStackParamList } from '../../types/navigation';
import { formatCurrency, formatDate, getFeeStatusColor } from '../../utils/formatters';

export const FeesOverviewScreen: React.FC = () => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();
  const navigation = useNavigation<NativeStackNavigationProp<ParentStackParamList>>();

  const [feeSummary, setFeeSummary] = useState<FeeSummary | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchFees = async () => {
    if (!activeChild) return;
    try {
      const data = await FeesApi.getFees(activeChild.id);
      setFeeSummary(data);
    } catch (e) {
      console.warn('Error fetching fees:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchFees();
  }, [activeChild?.id]);

  const currency = feeSummary?.currency_symbol || '₹';
  const invoices = feeSummary?.invoices || [];

  const renderInvoiceCard = ({ item }: { item: FeeInvoice }) => {
    const badge = getFeeStatusColor(item.status);

    return (
      <TouchableOpacity
        activeOpacity={0.7}
        onPress={() => navigation.navigate('FeeInvoiceDetail', { invoice: item })}
        style={[
          styles.invoiceCard,
          {
            backgroundColor: colors.surface,
            borderColor: colors.borderSubtle,
          },
        ]}
      >
        <View style={styles.cardHeader}>
          <View>
            <Text style={[styles.invoiceNumber, { color: colors.text }]}>
              {item.invoice_number}
            </Text>
            <Text style={[styles.invoiceTitle, { color: colors.textSecondary }]}>
              {item.title}
            </Text>
          </View>

          <View style={[styles.badge, { backgroundColor: badge.bg }]}>
            <Text style={[styles.badgeText, { color: badge.text }]}>
              {badge.label}
            </Text>
          </View>
        </View>

        <View style={[styles.amountRow, { borderTopColor: colors.borderSubtle }]}>
          <View>
            <Text style={[styles.amountLabel, { color: colors.textMuted }]}>Total</Text>
            <Text style={[styles.amountVal, { color: colors.text }]}>
              {formatCurrency(item.total_amount, currency)}
            </Text>
          </View>

          <View>
            <Text style={[styles.amountLabel, { color: colors.textMuted }]}>Paid</Text>
            <Text style={[styles.amountVal, { color: colors.success }]}>
              {formatCurrency(item.paid_amount, currency)}
            </Text>
          </View>

          <View>
            <Text style={[styles.amountLabel, { color: colors.textMuted }]}>Due</Text>
            <Text style={[styles.amountVal, { color: item.due_amount > 0 ? colors.danger : colors.textMuted }]}>
              {formatCurrency(item.due_amount, currency)}
            </Text>
          </View>
        </View>

        <View style={styles.cardFooter}>
          <Text style={[styles.dueDateText, { color: colors.textMuted }]}>
            Due Date: {formatDate(item.due_date)}
          </Text>
          <Text style={[styles.viewDetailsText, { color: colors.primary }]}>
            View Breakdown →
          </Text>
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <Text style={[styles.screenTitle, { color: colors.text }]}>School Fees</Text>
        <TouchableOpacity onPress={() => navigation.navigate('PaymentHistory')}>
          <Text style={[styles.historyLink, { color: colors.primary }]}>Receipts History</Text>
        </TouchableOpacity>
      </View>

      <ChildSwitcherBar />

      {loading ? (
        <LoadingIndicator message="Calculating student fee records..." />
      ) : (
        <FlatList
          data={invoices}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderInvoiceCard}
          ListHeaderComponent={
            <>
              <FeeSummaryCard feeSummary={feeSummary} />

              <View style={styles.sectionHeader}>
                <Text style={[styles.sectionTitle, { color: colors.text }]}>
                  Fee Invoices & Dues
                </Text>
              </View>
            </>
          }
          ListEmptyComponent={
            <EmptyState
              title="No Invoices Found"
              description="All fees have been cleared and no outstanding invoices are active."
            />
          }
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => {
                setRefreshing(true);
                fetchFees();
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
    paddingTop: Spacing.md,
    paddingBottom: Spacing.xs,
  },
  screenTitle: {
    fontSize: Typography.size.xl,
    fontWeight: Typography.weight.bold,
  },
  historyLink: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  sectionHeader: {
    paddingHorizontal: Spacing.xl,
    marginTop: Spacing.lg,
    marginBottom: Spacing.xs,
  },
  sectionTitle: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  listContent: {
    paddingBottom: Spacing.xxxl,
  },
  invoiceCard: {
    borderRadius: BorderRadius.xl,
    padding: Spacing.lg,
    marginHorizontal: Spacing.lg,
    marginVertical: Spacing.xs + 2,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    justifyContent: 'space-between',
    marginBottom: Spacing.sm,
  },
  invoiceNumber: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
  },
  invoiceTitle: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  badge: {
    paddingHorizontal: Spacing.sm + 2,
    paddingVertical: 2,
    borderRadius: BorderRadius.xs,
  },
  badgeText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
  },
  amountRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    paddingTop: Spacing.sm,
    marginBottom: Spacing.sm,
  },
  amountLabel: {
    fontSize: 10,
    textTransform: 'uppercase',
  },
  amountVal: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
    marginTop: 2,
  },
  cardFooter: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: Spacing.xs,
  },
  dueDateText: {
    fontSize: Typography.size.xs,
  },
  viewDetailsText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
});
