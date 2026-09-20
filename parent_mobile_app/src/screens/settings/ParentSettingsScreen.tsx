import React from 'react';
import {
  Alert,
  ScrollView,
  StyleSheet,
  Switch,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';

export const ParentSettingsScreen: React.FC = () => {
  const { colors, isDarkMode, toggleTheme } = useTheme();
  const { user, school, logout } = useAuth();
  const { activeChild } = useActiveChild();
  const navigation = useNavigation<NativeStackNavigationProp<ParentStackParamList>>();

  const handleLogout = () => {
    Alert.alert(
      'Sign Out',
      'Are you sure you want to log out of the Parent Portal?',
      [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Sign Out', style: 'destructive', onPress: logout },
      ]
    );
  };

  const renderSettingRow = (
    icon: string,
    title: string,
    subtitle?: string,
    onPress?: () => void,
    rightComponent?: React.ReactNode
  ) => (
    <TouchableOpacity
      activeOpacity={0.7}
      disabled={!onPress}
      onPress={onPress}
      style={[
        styles.settingRow,
        {
          backgroundColor: colors.surface,
          borderColor: colors.borderSubtle,
        },
      ]}
    >
      <View style={styles.settingLeft}>
        <Text style={styles.settingIcon}>{icon}</Text>
        <View style={{ marginLeft: Spacing.md }}>
          <Text style={[styles.settingTitle, { color: colors.text }]}>{title}</Text>
          {subtitle && (
            <Text style={[styles.settingSubtitle, { color: colors.textSecondary }]}>
              {subtitle}
            </Text>
          )}
        </View>
      </View>

      {rightComponent || <Text style={[styles.arrow, { color: colors.textMuted }]}>→</Text>}
    </TouchableOpacity>
  );

  return (
    <ScreenWrapper>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Header */}
        <View style={styles.header}>
          <Text style={[styles.title, { color: colors.text }]}>Settings & Account</Text>
        </View>

        {/* Parent Profile Card */}
        <View
          style={[
            styles.profileCard,
            {
              backgroundColor: colors.surface,
              borderColor: colors.borderSubtle,
            },
          ]}
        >
          <View style={[styles.avatar, { backgroundColor: colors.primary }]}>
            <Text style={styles.avatarText}>{user?.name ? user.name[0].toUpperCase() : 'P'}</Text>
          </View>
          <View style={styles.profileInfo}>
            <Text style={[styles.userName, { color: colors.text }]}>{user?.name || 'Parent'}</Text>
            <Text style={[styles.userEmail, { color: colors.textSecondary }]}>{user?.email}</Text>
            <View style={[styles.badge, { backgroundColor: colors.primaryLight + '20' }]}>
              <Text style={[styles.badgeText, { color: colors.primary }]}>
                {school?.name || 'Enrolled School'}
              </Text>
            </View>
          </View>
        </View>

        {/* Child Selector */}
        <View style={styles.sectionHeader}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Active Student Ward</Text>
        </View>
        <ChildSwitcherBar />

        {/* Student Management Options */}
        <View style={styles.sectionHeader}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Student Academic Records</Text>
        </View>

        {activeChild && (
          <>
            {renderSettingRow(
              '👤',
              'Student Bio & ID Details',
              'View admission number, blood group & contact info',
              () => navigation.navigate('ChildProfile', { child: activeChild })
            )}

            {renderSettingRow(
              '📁',
              'Uploaded Student Documents',
              'Birth certificate, medical records & ID proofs',
              () => navigation.navigate('ChildDocuments', { childId: activeChild.id })
            )}
          </>
        )}

        {/* Preferences */}
        <View style={styles.sectionHeader}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>App Preferences</Text>
        </View>

        {renderSettingRow(
          '🌙',
          'Dark Mode Theme',
          'Switch dark & light interface colors',
          undefined,
          <Switch
            value={isDarkMode}
            onValueChange={toggleTheme}
            trackColor={{ false: '#cbd5e1', true: colors.primaryLight }}
            thumbColor={isDarkMode ? colors.primary : '#ffffff'}
          />
        )}

        {renderSettingRow(
          '🔔',
          'Push Notifications',
          'Attendance alerts, homework updates & fees',
          () => navigation.navigate('NotificationSettings')
        )}

        {/* School & Support */}
        <View style={styles.sectionHeader}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>School Information</Text>
        </View>

        {renderSettingRow(
          '🏫',
          school?.name || 'School ERP',
          `School Code: ${school?.code || 'SCH001'}`,
          undefined,
          <View />
        )}

        {/* Sign out */}
        <TouchableOpacity
          activeOpacity={0.8}
          onPress={handleLogout}
          style={[styles.logoutBtn, { backgroundColor: colors.dangerLight, borderColor: colors.danger + '30' }]}
        >
          <Text style={[styles.logoutText, { color: colors.danger }]}>🚪 Sign Out from Parent App</Text>
        </TouchableOpacity>

        <Text style={[styles.versionText, { color: colors.textMuted }]}>
          SchoolCloud Parent App • Version 1.0.0 (Android)
        </Text>
      </ScrollView>
    </ScreenWrapper>
  );
};

const styles = StyleSheet.create({
  scrollContent: {
    paddingBottom: Spacing.xxxl,
  },
  header: {
    paddingHorizontal: Spacing.xl,
    paddingTop: Spacing.md,
    paddingBottom: Spacing.sm,
  },
  title: {
    fontSize: Typography.size.xl,
    fontWeight: Typography.weight.bold,
  },
  profileCard: {
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: Spacing.lg,
    marginVertical: Spacing.sm,
    padding: Spacing.lg,
    borderRadius: BorderRadius.xl,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  avatar: {
    width: 54,
    height: 54,
    borderRadius: 27,
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarText: {
    color: '#ffffff',
    fontSize: Typography.size.xl,
    fontWeight: Typography.weight.bold,
  },
  profileInfo: {
    marginLeft: Spacing.md,
    flex: 1,
  },
  userName: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  userEmail: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  badge: {
    paddingHorizontal: Spacing.sm,
    paddingVertical: 2,
    borderRadius: BorderRadius.sm,
    alignSelf: 'flex-start',
    marginTop: Spacing.xs,
  },
  badgeText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
  },
  sectionHeader: {
    paddingHorizontal: Spacing.xl,
    marginTop: Spacing.lg,
    marginBottom: Spacing.xs,
  },
  sectionTitle: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
  },
  settingRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: Spacing.md,
    marginHorizontal: Spacing.lg,
    marginVertical: Spacing.xs,
    borderRadius: BorderRadius.lg,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  settingLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  settingIcon: {
    fontSize: 22,
  },
  settingTitle: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.semibold,
  },
  settingSubtitle: {
    fontSize: 11,
    marginTop: 2,
  },
  arrow: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  logoutBtn: {
    marginHorizontal: Spacing.lg,
    marginTop: Spacing.xl,
    padding: Spacing.md,
    borderRadius: BorderRadius.lg,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
  },
  logoutText: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
  },
  versionText: {
    textAlign: 'center',
    fontSize: 11,
    marginTop: Spacing.lg,
  },
});
