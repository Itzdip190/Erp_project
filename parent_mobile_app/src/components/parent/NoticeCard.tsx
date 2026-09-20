import React from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useTheme } from '../../context/ThemeContext';
import { NoticeItem } from '../../types/notice';
import { formatDate } from '../../utils/formatters';

interface NoticeCardProps {
  notice: NoticeItem;
  onPress: () => void;
}

export const NoticeCard: React.FC<NoticeCardProps> = ({ notice, onPress }) => {
  const { colors } = useTheme();

  const getPriorityBadge = () => {
    switch (notice.priority) {
      case 'urgent':
        return { bg: colors.dangerLight, text: colors.danger, label: 'URGENT' };
      case 'high':
        return { bg: colors.warningLight, text: colors.warning, label: 'IMPORTANT' };
      default:
        return { bg: colors.infoLight, text: colors.info, label: 'NOTICE' };
    }
  };

  const badge = getPriorityBadge();

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
      <View style={styles.headerRow}>
        <View style={[styles.badge, { backgroundColor: badge.bg }]}>
          <Text style={[styles.badgeText, { color: badge.text }]}>
            {badge.label}
          </Text>
        </View>

        <Text style={[styles.dateText, { color: colors.textMuted }]}>
          {formatDate(notice.notice_date || notice.publish_on)}
        </Text>
      </View>

      <Text style={[styles.title, { color: colors.text }]} numberOfLines={2}>
        {notice.title}
      </Text>

      <Text style={[styles.message, { color: colors.textSecondary }]} numberOfLines={3}>
        {notice.message}
      </Text>

      {notice.created_by && (
        <View style={[styles.footerRow, { borderTopColor: colors.borderSubtle }]}>
          <Text style={[styles.authorText, { color: colors.textMuted }]}>
            Published by: {notice.created_by}
          </Text>
        </View>
      )}
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
  headerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.xs + 2,
  },
  badge: {
    paddingHorizontal: Spacing.sm,
    paddingVertical: 2,
    borderRadius: BorderRadius.xs,
  },
  badgeText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
  },
  dateText: {
    fontSize: Typography.size.xs,
  },
  title: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
    marginBottom: 4,
  },
  message: {
    fontSize: Typography.size.xs,
    lineHeight: 18,
    marginBottom: Spacing.xs,
  },
  footerRow: {
    borderTopWidth: 1,
    paddingTop: Spacing.xs + 2,
    marginTop: Spacing.xs,
  },
  authorText: {
    fontSize: 11,
  },
});
