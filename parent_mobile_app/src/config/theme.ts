export interface ThemeColors {
  primary: string;
  primaryDark: string;
  primaryLight: string;
  accent: string;
  background: string;
  surface: string;
  surfaceSubtle: string;
  text: string;
  textSecondary: string;
  textMuted: string;
  border: string;
  borderSubtle: string;
  success: string;
  successLight: string;
  warning: string;
  warningLight: string;
  danger: string;
  dangerLight: string;
  info: string;
  infoLight: string;
  card: string;
  white: string;
  black: string;
}

export const LightColors: ThemeColors = {
  primary: '#0252D9',        // Educorerp Royal Blue
  primaryDark: '#0143B5',
  primaryLight: '#2563eb',
  accent: '#00d2ff',         // Educorerp Cyan Accent
  background: '#f8fafc',     // Slate 50
  surface: '#ffffff',
  surfaceSubtle: '#f1f5f9',
  text: '#0f172a',           // Slate 900
  textSecondary: '#475569',  // Slate 600
  textMuted: '#94a3b8',      // Slate 400
  border: '#e2e8f0',         // Slate 200
  borderSubtle: '#f1f5f9',
  success: '#10b981',        // Emerald 500
  successLight: '#d1fae5',
  warning: '#f59e0b',        // Amber 500
  warningLight: '#fef3c7',
  danger: '#ef4444',         // Red 500
  dangerLight: '#fee2e2',
  info: '#3b82f6',           // Blue 500
  infoLight: '#dbeafe',
  card: '#ffffff',
  white: '#ffffff',
  black: '#000000',
};

export const DarkColors: ThemeColors = {
  primary: '#3b82f6',
  primaryDark: '#1d4ed8',
  primaryLight: '#60a5fa',
  accent: '#22d3ee',
  background: '#0f172a',
  surface: '#1e293b',
  surfaceSubtle: '#334155',
  text: '#f8fafc',
  textSecondary: '#cbd5e1',
  textMuted: '#64748b',
  border: '#334155',
  borderSubtle: '#1e293b',
  success: '#34d399',
  successLight: '#064e3b',
  warning: '#fbbf24',
  warningLight: '#78350f',
  danger: '#f87171',
  dangerLight: '#7f1d1d',
  info: '#60a5fa',
  infoLight: '#1e3a8a',
  card: '#1e293b',
  white: '#ffffff',
  black: '#000000',
};

export const Spacing = {
  xs: 4,
  sm: 8,
  md: 12,
  lg: 16,
  xl: 20,
  xxl: 24,
  xxxl: 32,
};

export const Typography = {
  size: {
    xs: 11,
    sm: 13,
    md: 15,
    lg: 17,
    xl: 20,
    xxl: 24,
    display: 30,
  },
  weight: {
    regular: '400' as const,
    medium: '500' as const,
    semibold: '600' as const,
    bold: '700' as const,
  },
};

export const BorderRadius = {
  xs: 4,
  sm: 8,
  md: 12,
  lg: 16,
  xl: 20,
  xxl: 24,
  full: 9999,
};

export const Shadows = {
  subtle: {
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 2,
  },
  medium: {
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.08,
    shadowRadius: 6,
    elevation: 4,
  },
  card: {
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.07,
    shadowRadius: 10,
    elevation: 5,
  },
};
