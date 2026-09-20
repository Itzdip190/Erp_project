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
import { FeesApi } from '../../api/feesApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { PaymentReceipt } from '../../types/fee';
import { ParentStackParamList } from '../../types/navigation';
import { formatCurrency, formatDate } from '../../utils/formatters';

type Props = NativeStackScreenProps<ParentStackParamList, 'PaymentHistory'>;

export const PaymentHistoryScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();

  const [receipts, setReceipts] = useState<PaymentReceipt[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchReceipts = async () => {
    if (!activeChild) return;
    try {
      const summary = await FeesApi.getFees(activeChild.id);
      // Construct receipts from paid invoices
      const paidInvoices = (summary?.invoices || []).filter((inv) => inv.paid_amount > 0);
      const generatedReceipts: PaymentReceipt[] = paidInvoices.map((inv) => ({
        id: inv.id,
        receipt_number: `RCP-${inv.invoice_number.replace(/\D/g, '') || inv.id}`,
        invoice_id: inv.id,
        amount: inv.paid_amount,
        payment_mode: 'Online / Bank Deposit',
        payment_date: inv.created_at,
      }));
      setReceipts(generatedReceipts);
    } catch (e) {
      console.warn('Error fetching payment history:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchReceipts();
  }, [activeChild?.id]);

  const renderReceiptCard = ({ item }: { item: PaymentReceipt }) => (
    <View
      style={[
        styles.receiptCard,
        {
          backgroundColor: colors.surface,
          borderColor: colors.borderSubtle,
        },
      ]}
    >
      <View style={styles.leftCol}>
        <View style={[styles.iconBox, { backgroundColor: colors.successLight }]}>
          <Text style={{ fontSize: 20 }}>🧾</Text>
        </View>

        <View style={styles.textCol}>
          <Text style={[styles.receiptNum, { color: colors.text }]}>{item.receipt_number}</Text>
          <Text style={[styles.payMode, { color: colors.textSecondary }]}>Mode: {item.payment_mode}</Text>
          <Text style={[styles.dateText, { color: colors.textMuted }]}>
            Date: {formatDate(item.payment_date)}
          </Text>
        </View>
      </View>

      <View style={styles.rightCol}>
        <Text style={[styles.amountText, { color: colors.success }]}>
          {formatCurrency(item.amount, '₹')}
        </Text>
        <Text style={[styles.statusText, { color: colors.success }]}>SUCCESS</Text>
      </View>
    </View>
  );

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Fee Payment Receipts</Text>
        <View style={{ width: 40 }} />
      </View>

      {loading ? (
        <LoadingIndicator message="Loading payment receipts..." />
      ) : receipts.length === 0 ? (
        <EmptyState
          title="No Payment Receipts"
          description="No successful fee payments have been recorded yet."
        />
      ) : (
        <FlatList
          data={receipts}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderReceiptCard}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => {
                setRefreshing(true);
                fetchReceipts();
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
  receiptCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: Spacing.md,
    marginVertical: Spacing.xs + 2,
    borderRadius: BorderRadius.lg,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  leftCol: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  iconBox: {
    width: 44,
    height: 44,
    borderRadius: BorderRadius.md,
    alignItems: 'center',
    justifyContent: 'center',
  },
  textCol: {
    marginLeft: Spacing.md,
    flex: 1,
  },
  receiptNum: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
  },
  payMode: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  dateText: {
    fontSize: 10,
    marginTop: 2,
  },
  rightCol: {
    alignItems: 'flex-end',
    marginLeft: Spacing.sm,
  },
  amountText: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  statusText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
    marginTop: 2,
  },
});
