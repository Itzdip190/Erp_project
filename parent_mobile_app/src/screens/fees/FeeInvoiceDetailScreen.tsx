import React, { useState } from 'react';
import {
  Alert,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { CustomButton } from '../../components/common/CustomButton';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';
import { formatCurrency, formatDate, getFeeStatusColor } from '../../utils/formatters';

type Props = NativeStackScreenProps<ParentStackParamList, 'FeeInvoiceDetail'>;

export const FeeInvoiceDetailScreen: React.FC<Props> = ({ navigation, route }) => {
  const { colors } = useTheme();
  const { invoice } = route.params;
  const [paying, setPaying] = useState(false);

  const badge = getFeeStatusColor(invoice.status);
  const currency = '₹';

  const handlePayNow = () => {
    setPaying(true);
    // Simulating gateway initiation / Razorpay modal trigger
    setTimeout(() => {
      setPaying(false);
      Alert.alert(
        'Online Payment Gateway',
        `Proceeding to secure checkout for ${formatCurrency(invoice.due_amount, currency)}. Payment receipt will be automatically generated upon bank confirmation.`,
        [{ text: 'Close' }]
      );
    }, 1200);
  };

  return (
    <ScreenWrapper>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.topNav}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
          </TouchableOpacity>
          <Text style={[styles.headerTitle, { color: colors.text }]}>Invoice Details</Text>
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
          <View style={styles.headerRow}>
            <View>
              <Text style={[styles.invoiceNumber, { color: colors.text }]}>
                {invoice.invoice_number}
              </Text>
              <Text style={[styles.invoiceDate, { color: colors.textMuted }]}>
                Created: {formatDate(invoice.created_at)}
              </Text>
            </View>

            <View style={[styles.badge, { backgroundColor: badge.bg }]}>
              <Text style={[styles.badgeText, { color: badge.text }]}>
                {badge.label}
              </Text>
            </View>
          </View>

          <View style={[styles.divider, { backgroundColor: colors.borderSubtle }]} />

          <Text style={[styles.sectionTitle, { color: colors.text }]}>Fee Breakdown</Text>

          {invoice.items && invoice.items.length > 0 ? (
            invoice.items.map((item) => (
              <View key={item.id} style={styles.itemRow}>
                <View style={{ flex: 1 }}>
                  <Text style={[styles.itemName, { color: colors.text }]}>{item.title}</Text>
                  <Text style={[styles.itemSub, { color: colors.textMuted }]}>
                    Due: {formatDate(item.due_date)}
                  </Text>
                </View>
                <Text style={[styles.itemAmount, { color: colors.text }]}>
                  {formatCurrency(item.amount, currency)}
                </Text>
              </View>
            ))
          ) : (
            <View style={styles.itemRow}>
              <Text style={[styles.itemName, { color: colors.text }]}>{invoice.title}</Text>
              <Text style={[styles.itemAmount, { color: colors.text }]}>
                {formatCurrency(invoice.total_amount, currency)}
              </Text>
            </View>
          )}

          <View style={[styles.divider, { backgroundColor: colors.borderSubtle }]} />

          {/* Summary Totals */}
          <View style={styles.totalRow}>
            <Text style={[styles.totalLabel, { color: colors.textSecondary }]}>Subtotal:</Text>
            <Text style={[styles.totalVal, { color: colors.text }]}>
              {formatCurrency(invoice.total_amount, currency)}
            </Text>
          </View>

          <View style={styles.totalRow}>
            <Text style={[styles.totalLabel, { color: colors.textSecondary }]}>Amount Paid:</Text>
            <Text style={[styles.totalVal, { color: colors.success }]}>
              {formatCurrency(invoice.paid_amount, currency)}
            </Text>
          </View>

          <View style={[styles.totalRow, { marginTop: Spacing.sm }]}>
            <Text style={[styles.dueLabel, { color: colors.text }]}>Balance Due:</Text>
            <Text style={[styles.dueVal, { color: invoice.due_amount > 0 ? colors.danger : colors.success }]}>
              {formatCurrency(invoice.due_amount, currency)}
            </Text>
          </View>

          {invoice.due_amount > 0 && (
            <CustomButton
              title={`Pay Now (${formatCurrency(invoice.due_amount, currency)})`}
              onPress={handlePayNow}
              loading={paying}
              size="lg"
              style={styles.payBtn}
            />
          )}
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
  headerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  invoiceNumber: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  invoiceDate: {
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
  divider: {
    height: 1,
    marginVertical: Spacing.md,
  },
  sectionTitle: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
    marginBottom: Spacing.sm,
  },
  itemRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: Spacing.xs + 2,
  },
  itemName: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.medium,
  },
  itemSub: {
    fontSize: 10,
    marginTop: 1,
  },
  itemAmount: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  totalRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 3,
  },
  totalLabel: {
    fontSize: Typography.size.xs,
  },
  totalVal: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.semibold,
  },
  dueLabel: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  dueVal: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  payBtn: {
    marginTop: Spacing.xl,
  },
});
