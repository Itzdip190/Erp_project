import React, { createContext, useContext, useEffect, useState } from 'react';
import { useColorScheme } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { AuthApi } from '../api/authApi';
import { STORAGE_KEYS } from '../config/constants';
import { DarkColors, LightColors, ThemeColors } from '../config/theme';

interface ThemeContextType {
  isDarkMode: boolean;
  colors: ThemeColors;
  toggleTheme: () => void;
  setScheme: (mode: 'light' | 'dark' | 'system') => void;
}

const ThemeContext = createContext<ThemeContextType | undefined>(undefined);

export const ThemeProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const systemScheme = useColorScheme();
  const [themeMode, setThemeMode] = useState<'light' | 'dark' | 'system'>('system');
  const [brandingPrimary, setBrandingPrimary] = useState<string | null>(null);

  useEffect(() => {
    const loadThemePreference = async () => {
      try {
        const saved = await AsyncStorage.getItem(STORAGE_KEYS.THEME_MODE);
        if (saved === 'light' || saved === 'dark' || saved === 'system') {
          setThemeMode(saved);
        }
      } catch (e) {
        console.warn('Error reading theme mode:', e);
      }
    };
    loadThemePreference();
  }, []);

  // Fetch School Custom Branding if available
  useEffect(() => {
    const loadBranding = async () => {
      try {
        const config = await AuthApi.getAppConfig();
        if (config?.theme?.primary) {
          setBrandingPrimary(config.theme.primary);
        }
      } catch {
        // Fallback to default palette
      }
    };
    loadBranding();
  }, []);

  const isDarkMode = themeMode === 'system' ? systemScheme === 'dark' : themeMode === 'dark';

  const baseColors = isDarkMode ? DarkColors : LightColors;
  const colors: ThemeColors = {
    ...baseColors,
    primary: brandingPrimary || baseColors.primary,
  };

  const toggleTheme = async () => {
    const nextMode = isDarkMode ? 'light' : 'dark';
    setThemeMode(nextMode);
    await AsyncStorage.setItem(STORAGE_KEYS.THEME_MODE, nextMode);
  };

  const setScheme = async (mode: 'light' | 'dark' | 'system') => {
    setThemeMode(mode);
    await AsyncStorage.setItem(STORAGE_KEYS.THEME_MODE, mode);
  };

  return (
    <ThemeContext.Provider value={{ isDarkMode, colors, toggleTheme, setScheme }}>
      {children}
    </ThemeContext.Provider>
  );
};

export const useTheme = (): ThemeContextType => {
  const context = useContext(ThemeContext);
  if (!context) {
    throw new Error('useTheme must be used within a ThemeProvider');
  }
  return context;
};
