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
import { AcademicsApi } from '../../api/academicsApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { HomeworkCard } from '../../components/parent/HomeworkCard';
import { BorderRadius, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { HomeworkItem } from '../../types/academic';
import { ParentStackParamList } from '../../types/navigation';

export const HomeworkScreen: React.FC = () => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();
  const navigation = useNavigation<NativeStackNavigationProp<ParentStackParamList>>();

  const [homeworkList, setHomeworkList] = useState<HomeworkItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [activeTab, setActiveTab] = useState<'pending' | 'submitted' | 'all'>('pending');

  const fetchHomework = async () => {
    if (!activeChild) return;
    try {
      const list = await AcademicsApi.getHomework(activeChild.id);
      setHomeworkList(list);
    } catch (e) {
      console.warn('Error fetching homework:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchHomework();
  }, [activeChild?.id]);

  const filteredList = homeworkList.filter((item) => {
    if (activeTab === 'pending') return !item.is_submitted;
    if (activeTab === 'submitted') return item.is_submitted;
    return true;
  });

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.screenTitle, { color: colors.text }]}>Homework & Tasks</Text>
        <View style={{ width: 40 }} />
      </View>

      <ChildSwitcherBar />

      {/* Filter Tabs */}
      <View style={[styles.tabsContainer, { backgroundColor: colors.surfaceSubtle }]}>
        <TouchableOpacity
          style={[
            styles.tabButton,
            activeTab === 'pending' && { backgroundColor: colors.surface },
          ]}
          onPress={() => setActiveTab('pending')}
        >
          <Text
            style={[
              styles.tabText,
              { color: activeTab === 'pending' ? colors.primary : colors.textSecondary },
            ]}
          >
            Pending ({homeworkList.filter((h) => !h.is_submitted).length})
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[
            styles.tabButton,
            activeTab === 'submitted' && { backgroundColor: colors.surface },
          ]}
          onPress={() => setActiveTab('submitted')}
        >
          <Text
            style={[
              styles.tabText,
              { color: activeTab === 'submitted' ? colors.primary : colors.textSecondary },
            ]}
          >
            Submitted ({homeworkList.filter((h) => h.is_submitted).length})
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[
            styles.tabButton,
            activeTab === 'all' && { backgroundColor: colors.surface },
          ]}
          onPress={() => setActiveTab('all')}
        >
          <Text
            style={[
              styles.tabText,
              { color: activeTab === 'all' ? colors.primary : colors.textSecondary },
            ]}
          >
            All
          </Text>
        </TouchableOpacity>
      </View>

      {loading ? (
        <LoadingIndicator message="Fetching assigned homework..." />
      ) : filteredList.length === 0 ? (
        <EmptyState
          title="No Homework Found"
          description={
            activeTab === 'pending'
              ? 'Awesome! All homework assignments are completed.'
              : 'No homework records under this tab.'
          }
        />
      ) : (
        <FlatList
          data={filteredList}
          keyExtractor={(item) => item.id.toString()}
          renderItem={({ item }) => (
            <HomeworkCard
              item={item}
              onPress={() => navigation.navigate('HomeworkDetail', { homework: item })}
            />
          )}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => {
                setRefreshing(true);
                fetchHomework();
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
  backText: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.semibold,
  },
  screenTitle: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  tabsContainer: {
    flexDirection: 'row',
    marginHorizontal: Spacing.lg,
    marginVertical: Spacing.sm,
    padding: 4,
    borderRadius: BorderRadius.lg,
  },
  tabButton: {
    flex: 1,
    paddingVertical: Spacing.sm,
    alignItems: 'center',
    borderRadius: BorderRadius.md,
  },
  tabText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  listContent: {
    paddingHorizontal: Spacing.lg,
    paddingBottom: Spacing.xxxl,
  },
});
