import React from 'react';
import {
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useTheme } from '../../context/ThemeContext';

export interface ActionItem {
  id: string;
  title: string;
  emoji: string;
  color: string;
  onPress: () => void;
  badge?: number | string;
}

interface QuickActionGridProps {
  actions: ActionItem[];
}

export const QuickActionGrid: React.FC<QuickActionGridProps> = ({ actions }) => {
  const { colors } = useTheme();

  return (
    <View style={styles.gridContainer}>
      {actions.map((action) => (
        <TouchableOpacity
          key={action.id}
          activeOpacity={0.7}
          onPress={action.onPress}
          style={[
            styles.actionCard,
            {
              backgroundColor: colors.surface,
              borderColor: colors.borderSubtle,
            },
          ]}
        >
          {action.badge && (
            <View style={[styles.badge, { backgroundColor: colors.danger }]}>
              <Text style={styles.badgeText}>{action.badge}</Text>
            </View>
          )}

          <View
            style={[
              styles.iconWrapper,
              { backgroundColor: action.color + '18' },
            ]}
          >
            <Text style={styles.emojiText}>{action.emoji}</Text>
          </View>

          <Text
            style={[styles.actionTitle, { color: colors.text }]}
            numberOfLines={2}
          >
            {action.title}
          </Text>
        </TouchableOpacity>
      ))}
    </View>
  );
};

const styles = StyleSheet.create({
  gridContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    paddingHorizontal: Spacing.md,
    justifyContent: 'flex-start',
  },
  actionCard: {
    width: '22%',
    aspectRatio: 0.9,
    borderRadius: BorderRadius.lg,
    padding: Spacing.xs,
    margin: '1.5%',
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    ...Shadows.subtle,
    position: 'relative',
  },
  iconWrapper: {
    width: 44,
    height: 44,
    borderRadius: 22,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: Spacing.xs,
  },
  emojiText: {
    fontSize: 22,
  },
  actionTitle: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.medium,
    textAlign: 'center',
    lineHeight: 14,
  },
  badge: {
    position: 'absolute',
    top: -4,
    right: -4,
    minWidth: 18,
    height: 18,
    borderRadius: 9,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 4,
    zIndex: 2,
  },
  badgeText: {
    color: '#ffffff',
    fontSize: 10,
    fontWeight: 'bold',
  },
});
