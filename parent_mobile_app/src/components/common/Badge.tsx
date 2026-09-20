import React from 'react';
import { StyleSheet, Text, TextStyle, View, ViewStyle } from 'react-native';
import { BorderRadius, Spacing, Typography } from '../../config/theme';

interface BadgeProps {
  label: string;
  backgroundColor?: string;
  textColor?: string;
  size?: 'sm' | 'md';
  style?: ViewStyle;
  textStyle?: TextStyle;
}

export const Badge: React.FC<BadgeProps> = ({
  label,
  backgroundColor = '#e2e8f0',
  textColor = '#334155',
  size = 'md',
  style,
  textStyle,
}) => {
  const isSmall = size === 'sm';

  return (
    <View
      style={[
        styles.badge,
        { backgroundColor },
        isSmall && styles.badgeSm,
        style,
      ]}
    >
      <Text
        style={[
          styles.text,
          { color: textColor },
          isSmall && styles.textSm,
          textStyle,
        ]}
      >
        {label}
      </Text>
    </View>
  );
};

const styles = StyleSheet.create({
  badge: {
    paddingHorizontal: Spacing.sm + 2,
    paddingVertical: Spacing.xs,
    borderRadius: BorderRadius.full,
    alignSelf: 'flex-start',
    alignItems: 'center',
    justifyContent: 'center',
  },
  badgeSm: {
    paddingHorizontal: Spacing.xs + 2,
    paddingVertical: 2,
  },
  text: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.semibold,
  },
  textSm: {
    fontSize: 10,
  },
});
