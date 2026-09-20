import React, { useEffect, useState } from 'react';
import {
  FlatList,
  Linking,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { ChildrenApi } from '../../api/childrenApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';
import { StudentDocumentItem } from '../../types/student';
import { formatDate } from '../../utils/formatters';

type Props = NativeStackScreenProps<ParentStackParamList, 'ChildDocuments'>;

export const ChildDocumentsScreen: React.FC<Props> = ({ navigation, route }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();
  const childId = route.params?.childId || activeChild?.id;

  const [documents, setDocuments] = useState<StudentDocumentItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadDocs = async () => {
      if (!childId) return;
      try {
        const data = await ChildrenApi.getChildDocuments(childId);
        setDocuments(data);
      } catch (e) {
        console.warn('Error loading student documents:', e);
      } finally {
        setLoading(false);
      }
    };
    loadDocs();
  }, [childId]);

  const handleOpenDoc = (url: string) => {
    if (url) {
      Linking.openURL(url).catch((err) => console.warn('Cannot open document URL:', err));
    }
  };

  const renderItem = ({ item }: { item: StudentDocumentItem }) => (
    <View
      style={[
        styles.docCard,
        {
          backgroundColor: colors.surface,
          borderColor: colors.borderSubtle,
        },
      ]}
    >
      <View style={styles.leftCol}>
        <View style={[styles.iconBox, { backgroundColor: colors.primaryLight + '20' }]}>
          <Text style={{ fontSize: 20 }}>📄</Text>
        </View>
        <View style={styles.textCol}>
          <Text style={[styles.docType, { color: colors.text }]}>{item.document_type || 'Document'}</Text>
          <Text style={[styles.originalName, { color: colors.textSecondary }]} numberOfLines={1}>
            {item.original_name}
          </Text>
          <Text style={[styles.dateText, { color: colors.textMuted }]}>
            Uploaded: {formatDate(item.created_at)}
          </Text>
        </View>
      </View>

      {item.file_url ? (
        <TouchableOpacity
          style={[styles.downloadBtn, { backgroundColor: colors.primary }]}
          onPress={() => handleOpenDoc(item.file_url)}
        >
          <Text style={styles.downloadText}>View</Text>
        </TouchableOpacity>
      ) : null}
    </View>
  );

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Documents</Text>
        <View style={{ width: 40 }} />
      </View>

      {loading ? (
        <LoadingIndicator message="Loading documents..." />
      ) : documents.length === 0 ? (
        <EmptyState
          title="No Documents Uploaded"
          description="There are currently no uploaded certificates or verification documents for this student."
        />
      ) : (
        <FlatList
          data={documents}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderItem}
          contentContainerStyle={styles.listContainer}
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
  listContainer: {
    paddingHorizontal: Spacing.lg,
    paddingBottom: Spacing.xl,
  },
  docCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: Spacing.md,
    borderRadius: BorderRadius.lg,
    marginVertical: Spacing.xs + 2,
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
  docType: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
  },
  originalName: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  dateText: {
    fontSize: 10,
    marginTop: 2,
  },
  downloadBtn: {
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.xs + 2,
    borderRadius: BorderRadius.sm,
    marginLeft: Spacing.sm,
  },
  downloadText: {
    color: '#ffffff',
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
});
