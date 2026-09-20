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

type Props = NativeStackScreenProps<ParentStackParamList, 'NoticeDetail'>;

export const NoticeDetailScreen: React.FC<Props> = ({ navigation, route }) => {
  const { colors } = useTheme();
  const { notice } = route.params;

  return (
    <ScreenWrapper>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.topNav}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
          </TouchableOpacity>
          <Text style={[styles.headerTitle, { color: colors.text }]}>Circular Details</Text>
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
            <View style={[styles.badge, { backgroundColor: colors.infoLight }]}>
              <Text style={[styles.badgeText, { color: colors.info }]}>
                {notice.category || 'NOTICE'}
              </Text>
            </View>

            <Text style={[styles.dateText, { color: colors.textMuted }]}>
              {formatDate(notice.notice_date || notice.publish_on)}
            </Text>
          </View>

          <Text style={[styles.title, { color: colors.text }]}>{notice.title}</Text>

          {notice.created_by && (
            <Text style={[styles.author, { color: colors.textSecondary }]}>
              Published by: <Text style={{ fontWeight: 'bold' }}>{notice.created_by}</Text>
            </Text>
          )}

          <View style={[styles.divider, { backgroundColor: colors.borderSubtle }]} />

          <Text style={[styles.message, { color: colors.textSecondary }]}>
            {notice.message}
          </Text>

          {notice.attachment_url && (
            <TouchableOpacity
              style={[styles.attachmentBtn, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border }]}
              onPress={() => Linking.openURL(notice.attachment_url!)}
            >
              <Text style={{ fontSize: 20, marginRight: Spacing.sm }}>📎</Text>
              <View style={{ flex: 1 }}>
                <Text style={[styles.attachmentTitle, { color: colors.text }]}>
                  View Attached Document / Circular PDF
                </Text>
              </View>
              <Text style={[styles.arrow, { color: colors.primary }]}>Open →</Text>
            </TouchableOpacity>
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
  badgeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.sm,
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
  dateText: {
    fontSize: Typography.size.xs,
  },
  title: {
    fontSize: Typography.size.xl,
    fontWeight: Typography.weight.bold,
    marginBottom: Spacing.xs,
  },
  author: {
    fontSize: Typography.size.xs,
    marginBottom: Spacing.md,
  },
  divider: {
    height: 1,
    marginVertical: Spacing.md,
  },
  message: {
    fontSize: Typography.size.sm,
    lineHeight: 22,
  },
  attachmentBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: Spacing.md,
    borderRadius: BorderRadius.lg,
    borderWidth: 1,
    marginTop: Spacing.xl,
  },
  attachmentTitle: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  arrow: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
});
