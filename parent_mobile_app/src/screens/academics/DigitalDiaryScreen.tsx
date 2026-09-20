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
import { AcademicsApi } from '../../api/academicsApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { DiaryEntry } from '../../types/academic';
import { formatDate } from '../../utils/formatters';

export const DigitalDiaryScreen: React.FC = () => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();
  const navigation = useNavigation<any>();

  const [entries, setEntries] = useState<DiaryEntry[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchDiary = async () => {
    if (!activeChild) return;
    try {
      const data = await AcademicsApi.getDiaryEntries(activeChild.id);
      setEntries(data);
    } catch (e) {
      console.warn('Error fetching diary entries:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchDiary();
  }, [activeChild?.id]);

  const renderDiaryCard = ({ item }: { item: DiaryEntry }) => {
    const isUrgent = item.is_urgent;

    return (
      <View
        style={[
          styles.card,
          {
            backgroundColor: colors.surface,
            borderColor: isUrgent ? colors.danger + '40' : colors.borderSubtle,
          },
        ]}
      >
        <View style={styles.cardHeader}>
          <View style={styles.headerLeft}>
            {item.subject && (
              <View style={[styles.subjectTag, { backgroundColor: colors.primaryLight + '20' }]}>
                <Text style={[styles.subjectTagText, { color: colors.primary }]}>
                  {item.subject}
                </Text>
              </View>
            )}
            <Text style={[styles.dateText, { color: colors.textMuted }]}>
              {formatDate(item.date)}
            </Text>
          </View>

          {isUrgent && (
            <View style={[styles.urgentBadge, { backgroundColor: colors.dangerLight }]}>
              <Text style={[styles.urgentText, { color: colors.danger }]}>ACTION REQUIRED</Text>
            </View>
          )}
        </View>

        <Text style={[styles.title, { color: colors.text }]}>{item.title}</Text>
        <Text style={[styles.content, { color: colors.textSecondary }]}>{item.content}</Text>

        {item.teacher_name && (
          <View style={[styles.footerRow, { borderTopColor: colors.borderSubtle }]}>
            <Text style={[styles.teacherText, { color: colors.textMuted }]}>
              Teacher Remark by: <Text style={{ fontWeight: '600' }}>{item.teacher_name}</Text>
            </Text>
          </View>
        )}
      </View>
    );
  };

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Class Diary</Text>
        <View style={{ width: 40 }} />
      </View>

      <ChildSwitcherBar />

      {loading ? (
        <LoadingIndicator message="Fetching teacher diary updates..." />
      ) : entries.length === 0 ? (
        <EmptyState
          title="No Diary Entries"
          description="The teacher has not added any daily diary notes for this class today."
        />
      ) : (
        <FlatList
          data={entries}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderDiaryCard}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => {
                setRefreshing(true);
                fetchDiary();
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
  headerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  subjectTag: {
    paddingHorizontal: Spacing.sm,
    paddingVertical: 2,
    borderRadius: BorderRadius.xs,
    marginRight: Spacing.sm,
  },
  subjectTagText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
  },
  dateText: {
    fontSize: Typography.size.xs,
  },
  urgentBadge: {
    paddingHorizontal: Spacing.sm,
    paddingVertical: 2,
    borderRadius: BorderRadius.xs,
  },
  urgentText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
  },
  title: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
    marginBottom: 4,
  },
  content: {
    fontSize: Typography.size.xs,
    lineHeight: 18,
    marginBottom: Spacing.xs,
  },
  footerRow: {
    borderTopWidth: 1,
    paddingTop: Spacing.xs + 2,
    marginTop: Spacing.xs,
  },
  teacherText: {
    fontSize: 11,
  },
});
