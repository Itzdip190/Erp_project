import React from 'react';
import {
  Image,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';
import { formatDate } from '../../utils/formatters';

type Props = NativeStackScreenProps<ParentStackParamList, 'ChildProfile'>;

export const ChildProfileScreen: React.FC<Props> = ({ navigation, route }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();
  const child = route.params?.child || activeChild;

  if (!child) {
    return (
      <ScreenWrapper>
        <View style={styles.center}>
          <Text style={{ color: colors.text }}>No student profile selected.</Text>
        </View>
      </ScreenWrapper>
    );
  }

  const displayName = child.full_name || `${child.first_name} ${child.last_name || ''}`.trim();

  return (
    <ScreenWrapper>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Header */}
        <View style={styles.topNav}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
          </TouchableOpacity>
          <Text style={[styles.headerTitle, { color: colors.text }]}>Student Profile</Text>
          <View style={{ width: 40 }} />
        </View>

        {/* Student ID Card */}
        <View
          style={[
            styles.idCard,
            {
              backgroundColor: colors.surface,
              borderColor: colors.borderSubtle,
            },
          ]}
        >
          <View style={styles.avatarSection}>
            {child.photo || child.avatar ? (
              <Image source={{ uri: child.photo || child.avatar }} style={styles.avatar} />
            ) : (
              <View style={[styles.avatarPlaceholder, { backgroundColor: colors.primary }]}>
                <Text style={styles.avatarText}>{child.first_name[0]?.toUpperCase()}</Text>
              </View>
            )}

            <Text style={[styles.studentName, { color: colors.text }]}>{displayName}</Text>
            <Text style={[styles.classSection, { color: colors.textSecondary }]}>
              Class {child.class?.name || 'N/A'} {child.section?.name ? `(${child.section.name})` : ''}
            </Text>

            <View style={[styles.statusBadge, { backgroundColor: colors.successLight }]}>
              <Text style={[styles.statusText, { color: colors.success }]}>
                {child.is_active ? 'ACTIVE STUDENT' : 'INACTIVE'}
              </Text>
            </View>
          </View>

          {/* Details Table */}
          <View style={[styles.detailsSection, { borderTopColor: colors.borderSubtle }]}>
            <View style={styles.infoRow}>
              <Text style={[styles.infoLabel, { color: colors.textMuted }]}>Admission No.</Text>
              <Text style={[styles.infoValue, { color: colors.text }]}>{child.admission_number || 'N/A'}</Text>
            </View>

            <View style={styles.infoRow}>
              <Text style={[styles.infoLabel, { color: colors.textMuted }]}>Roll Number</Text>
              <Text style={[styles.infoValue, { color: colors.text }]}>{child.roll_number || 'N/A'}</Text>
            </View>

            <View style={styles.infoRow}>
              <Text style={[styles.infoLabel, { color: colors.textMuted }]}>Date of Birth</Text>
              <Text style={[styles.infoValue, { color: colors.text }]}>{formatDate(child.dob) || 'N/A'}</Text>
            </View>

            <View style={styles.infoRow}>
              <Text style={[styles.infoLabel, { color: colors.textMuted }]}>Gender</Text>
              <Text style={[styles.infoValue, { color: colors.text }]}>{child.gender || 'N/A'}</Text>
            </View>

            <View style={styles.infoRow}>
              <Text style={[styles.infoLabel, { color: colors.textMuted }]}>Blood Group</Text>
              <Text style={[styles.infoValue, { color: colors.text }]}>{child.blood_group || 'N/A'}</Text>
            </View>
          </View>
        </View>

        {/* Guardian & Contact Info */}
        <View style={styles.sectionHeading}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Guardian & Emergency Info</Text>
        </View>

        <View style={[styles.card, { backgroundColor: colors.surface, borderColor: colors.borderSubtle }]}>
          <View style={styles.infoRow}>
            <Text style={[styles.infoLabel, { color: colors.textMuted }]}>Father Name</Text>
            <Text style={[styles.infoValue, { color: colors.text }]}>{child.father_name || 'N/A'}</Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={[styles.infoLabel, { color: colors.textMuted }]}>Mother Name</Text>
            <Text style={[styles.infoValue, { color: colors.text }]}>{child.mother_name || 'N/A'}</Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={[styles.infoLabel, { color: colors.textMuted }]}>Guardian Contact</Text>
            <Text style={[styles.infoValue, { color: colors.text }]}>{child.guardian_phone || child.father_phone || 'N/A'}</Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={[styles.infoLabel, { color: colors.textMuted }]}>Residential Address</Text>
            <Text style={[styles.infoValue, { color: colors.text }]} numberOfLines={2}>
              {child.current_address || 'Registered on file'}
            </Text>
          </View>
        </View>

        {/* View Documents Button */}
        <TouchableOpacity
          activeOpacity={0.8}
          onPress={() => navigation.navigate('ChildDocuments', { childId: child.id })}
          style={[styles.docButton, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border }]}
        >
          <Text style={{ fontSize: 20, marginRight: Spacing.sm }}>📁</Text>
          <View style={{ flex: 1 }}>
            <Text style={[styles.docButtonTitle, { color: colors.text }]}>Student Documents</Text>
            <Text style={[styles.docButtonSub, { color: colors.textSecondary }]}>
              Birth certificate, ID proof, medical & academic certificates
            </Text>
          </View>
          <Text style={[styles.arrow, { color: colors.primary }]}>→</Text>
        </TouchableOpacity>
      </ScrollView>
    </ScreenWrapper>
  );
};

const styles = StyleSheet.create({
  scrollContent: {
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.md,
    paddingBottom: Spacing.xxxl,
  },
  center: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  topNav: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: Spacing.lg,
  },
  backText: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.semibold,
  },
  headerTitle: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  idCard: {
    borderRadius: BorderRadius.xl,
    padding: Spacing.xl,
    borderWidth: 1,
    ...Shadows.card,
  },
  avatarSection: {
    alignItems: 'center',
    marginBottom: Spacing.lg,
  },
  avatar: {
    width: 84,
    height: 84,
    borderRadius: 42,
    marginBottom: Spacing.sm,
  },
  avatarPlaceholder: {
    width: 84,
    height: 84,
    borderRadius: 42,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: Spacing.sm,
  },
  avatarText: {
    color: '#ffffff',
    fontSize: Typography.size.display,
    fontWeight: Typography.weight.bold,
  },
  studentName: {
    fontSize: Typography.size.xl,
    fontWeight: Typography.weight.bold,
  },
  classSection: {
    fontSize: Typography.size.sm,
    marginTop: 2,
  },
  statusBadge: {
    paddingHorizontal: Spacing.md,
    paddingVertical: 3,
    borderRadius: BorderRadius.full,
    marginTop: Spacing.sm,
  },
  statusText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
    letterSpacing: 0.5,
  },
  detailsSection: {
    borderTopWidth: 1,
    paddingTop: Spacing.md,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: Spacing.xs + 2,
  },
  infoLabel: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.medium,
  },
  infoValue: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.semibold,
    maxWidth: '60%',
    textAlign: 'right',
  },
  sectionHeading: {
    marginTop: Spacing.xl,
    marginBottom: Spacing.xs,
  },
  sectionTitle: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  card: {
    borderRadius: BorderRadius.xl,
    padding: Spacing.lg,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  docButton: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: Spacing.md,
    borderRadius: BorderRadius.lg,
    borderWidth: 1,
    marginTop: Spacing.lg,
  },
  docButtonTitle: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
  },
  docButtonSub: {
    fontSize: 11,
    marginTop: 2,
  },
  arrow: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
    marginLeft: Spacing.sm,
  },
});
