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
import { AttendanceApi } from '../../api/attendanceApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { LeaveRequest } from '../../types/attendance';
import { ParentStackParamList } from '../../types/navigation';
import { formatDate } from '../../utils/formatters';

type Props = NativeStackScreenProps<ParentStackParamList, 'LeaveHistory'>;

export const LeaveHistoryScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();

  const [leaves, setLeaves] = useState<LeaveRequest[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchLeaves = async () => {
    if (!activeChild) return;
    try {
      const data = await AttendanceApi.getLeaves(activeChild.id);
      setLeaves(data);
    } catch (e) {
      console.warn('Error fetching leave history:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchLeaves();
  }, [activeChild?.id]);

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'approved':
        return { bg: colors.successLight, text: colors.success, label: 'APPROVED' };
      case 'rejected':
        return { bg: colors.dangerLight, text: colors.danger, label: 'REJECTED' };
      case 'pending':
      default:
        return { bg: colors.warningLight, text: colors.warning, label: 'PENDING' };
    }
  };

  const renderItem = ({ item }: { item: LeaveRequest }) => {
    const badge = getStatusBadge(item.status);

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
        <View style={styles.cardHeader}>
          <View>
            <Text style={[styles.dateRange, { color: colors.text }]}>
              {formatDate(item.from_date)} - {formatDate(item.to_date)}
            </Text>
            <Text style={[styles.appliedDate, { color: colors.textMuted }]}>
              Applied: {formatDate(item.created_at)}
            </Text>
          </View>

          <View style={[styles.badge, { backgroundColor: badge.bg }]}>
            <Text style={[styles.badgeText, { color: badge.text }]}>
              {badge.label}
            </Text>
          </View>
        </View>

        <Text style={[styles.reason, { color: colors.textSecondary }]}>
          Reason: {item.reason}
        </Text>

        {item.rejection_reason ? (
          <Text style={[styles.rejectionReason, { color: colors.danger }]}>
            Remark: {item.rejection_reason}
          </Text>
        ) : null}
      </View>
    );
  };

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Leave Applications</Text>
        <TouchableOpacity onPress={() => navigation.navigate('ApplyLeave')}>
          <Text style={[styles.applyText, { color: colors.primary }]}>+ Apply</Text>
        </TouchableOpacity>
      </View>

      {loading ? (
        <LoadingIndicator message="Loading leave applications..." />
      ) : leaves.length === 0 ? (
        <EmptyState
          title="No Leave Applications"
          description="You haven't submitted any leave requests for this student yet."
          actionTitle="Apply for Leave"
          onAction={() => navigation.navigate('ApplyLeave')}
        />
      ) : (
        <FlatList
          data={leaves}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderItem}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => {
                setRefreshing(true);
                fetchLeaves();
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
  applyText: {
    fontSize: Typography.size.sm,
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
  dateRange: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
  },
  appliedDate: {
    fontSize: 10,
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
  reason: {
    fontSize: Typography.size.xs,
    lineHeight: 18,
    marginTop: Spacing.xs,
  },
  rejectionReason: {
    fontSize: Typography.size.xs,
    fontStyle: 'italic',
    marginTop: 4,
  },
});
