import React, { useState } from 'react';
import {
  Image,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { ChildSwitcherModal } from './ChildSwitcherModal';

export const ChildSwitcherBar: React.FC = () => {
  const { colors } = useTheme();
  const { activeChild, childrenList } = useActiveChild();
  const [modalVisible, setModalVisible] = useState(false);

  if (!activeChild) return null;

  const displayName = activeChild.full_name || `${activeChild.first_name} ${activeChild.last_name || ''}`.trim();
  const className = activeChild.class?.name ? `Class ${activeChild.class.name}` : '';
  const sectionName = activeChild.section?.name ? `- ${activeChild.section.name}` : '';
  const rollNumber = activeChild.roll_number ? ` • Roll #${activeChild.roll_number}` : '';

  return (
    <>
      <TouchableOpacity
        activeOpacity={0.8}
        onPress={() => setModalVisible(true)}
        style={[
          styles.container,
          {
            backgroundColor: colors.surface,
            borderColor: colors.borderSubtle,
          },
        ]}
      >
        <View style={styles.childInfoContainer}>
          {activeChild.photo || activeChild.avatar ? (
            <Image
              source={{ uri: activeChild.photo || activeChild.avatar }}
              style={styles.avatar}
            />
          ) : (
            <View style={[styles.avatarFallback, { backgroundColor: colors.primaryLight }]}>
              <Text style={styles.avatarText}>
                {activeChild.first_name ? activeChild.first_name[0].toUpperCase() : 'S'}
              </Text>
            </View>
          )}

          <View style={styles.textContainer}>
            <View style={styles.nameRow}>
              <Text style={[styles.childName, { color: colors.text }]} numberOfLines={1}>
                {displayName}
              </Text>
              {childrenList.length > 1 && (
                <View style={[styles.switchBadge, { backgroundColor: colors.primaryLight + '20' }]}>
                  <Text style={[styles.switchBadgeText, { color: colors.primary }]}>
                    Switch ({childrenList.length})
                  </Text>
                </View>
              )}
            </View>

            <Text style={[styles.classDetail, { color: colors.textSecondary }]}>
              {className} {sectionName} {rollNumber}
            </Text>
          </View>
        </View>
      </TouchableOpacity>

      <ChildSwitcherModal
        visible={modalVisible}
        onClose={() => setModalVisible(false)}
      />
    </>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.sm + 2,
    borderRadius: BorderRadius.xl,
    marginHorizontal: Spacing.lg,
    marginVertical: Spacing.sm,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  childInfoContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  avatar: {
    width: 44,
    height: 44,
    borderRadius: 22,
  },
  avatarFallback: {
    width: 44,
    height: 44,
    borderRadius: 22,
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarText: {
    color: '#ffffff',
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  textContainer: {
    marginLeft: Spacing.md,
    flex: 1,
  },
  nameRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  childName: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
    flex: 1,
  },
  switchBadge: {
    paddingHorizontal: Spacing.sm,
    paddingVertical: 2,
    borderRadius: BorderRadius.sm,
    marginLeft: Spacing.sm,
  },
  switchBadgeText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.semibold,
  },
  classDetail: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
});
