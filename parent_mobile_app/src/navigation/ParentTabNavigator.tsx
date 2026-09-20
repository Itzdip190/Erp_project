import React from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Spacing, Typography } from '../config/theme';
import { useTheme } from '../context/ThemeContext';
import { AttendanceCalendarScreen } from '../screens/attendance/AttendanceCalendarScreen';
import { ParentHomeScreen } from '../screens/dashboard/ParentHomeScreen';
import { DigitalDiaryScreen } from '../screens/academics/DigitalDiaryScreen';
import { FeesOverviewScreen } from '../screens/fees/FeesOverviewScreen';
import { ParentSettingsScreen } from '../screens/settings/ParentSettingsScreen';
import { ParentTabParamList } from '../types/navigation';

const Tab = createBottomTabNavigator<ParentTabParamList>();

export const ParentTabNavigator: React.FC = () => {
  const { colors } = useTheme();

  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: colors.primary,
        tabBarInactiveTintColor: colors.textMuted,
        tabBarStyle: {
          backgroundColor: colors.surface,
          borderTopColor: colors.borderSubtle,
          height: 62,
          paddingBottom: 8,
          paddingTop: 8,
        },
        tabBarLabelStyle: {
          fontSize: Typography.size.xs,
          fontWeight: Typography.weight.semibold,
        },
      }}
    >
      <Tab.Screen
        name="Home"
        component={ParentHomeScreen}
        options={{
          tabBarLabel: 'Home',
          tabBarIcon: ({ color, focused }) => (
            <Text style={{ fontSize: 22 }}>{focused ? '🏠' : '🏡'}</Text>
          ),
        }}
      />

      <Tab.Screen
        name="AttendanceTab"
        component={AttendanceCalendarScreen}
        options={{
          tabBarLabel: 'Attendance',
          tabBarIcon: ({ color, focused }) => (
            <Text style={{ fontSize: 22 }}>{focused ? '📅' : '📆'}</Text>
          ),
        }}
      />

      <Tab.Screen
        name="DiaryTab"
        component={DigitalDiaryScreen}
        options={{
          tabBarLabel: 'Diary',
          tabBarIcon: ({ color, focused }) => (
            <Text style={{ fontSize: 22 }}>{focused ? '📖' : '📕'}</Text>
          ),
        }}
      />

      <Tab.Screen
        name="FeesTab"
        component={FeesOverviewScreen}
        options={{
          tabBarLabel: 'Fees',
          tabBarIcon: ({ color, focused }) => (
            <Text style={{ fontSize: 22 }}>{focused ? '💳' : '🪙'}</Text>
          ),
        }}
      />

      <Tab.Screen
        name="SettingsTab"
        component={ParentSettingsScreen}
        options={{
          tabBarLabel: 'Account',
          tabBarIcon: ({ color, focused }) => (
            <Text style={{ fontSize: 22 }}>{focused ? '⚙️' : '👤'}</Text>
          ),
        }}
      />
    </Tab.Navigator>
  );
};
