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
import { NoticesApi } from '../../api/noticesApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { NoticeCard } from '../../components/parent/NoticeCard';
import { Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';
import { NoticeItem } from '../../types/notice';

type Props = NativeStackScreenProps<ParentStackParamList, 'NoticesList'>;

export const NoticesScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();

  const [notices, setNotices] = useState<NoticeItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchNotices = async () => {
    try {
      const data = await NoticesApi.getNotices(activeChild?.id);
      setNotices(data);
    } catch (e) {
      console.warn('Error fetching notices:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchNotices();
  }, [activeChild?.id]);

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>School Circulars</Text>
        <View style={{ width: 40 }} />
      </View>

      {loading ? (
        <LoadingIndicator message="Fetching circulars & notices..." />
      ) : notices.length === 0 ? (
        <EmptyState
          title="No Notices Available"
          description="There are no active notices or announcements published by the school at this time."
        />
      ) : (
        <FlatList
          data={notices}
          keyExtractor={(item) => item.id.toString()}
          renderItem={({ item }) => (
            <NoticeCard
              notice={item}
              onPress={() => navigation.navigate('NoticeDetail', { notice: item })}
            />
          )}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => {
                setRefreshing(true);
                fetchNotices();
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
});
