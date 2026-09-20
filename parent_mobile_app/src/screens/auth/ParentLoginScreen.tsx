import React, { useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Animated,
  Dimensions,
  Easing,
  KeyboardAvoidingView,
  LayoutChangeEvent,
  Platform,
  ScrollView,
  StatusBar,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { APP_NAME } from '../../config/constants';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { AuthStackParamList } from '../../types/navigation';
import { Storage } from '../../utils/storage';
import { isValidPassword, isValidSchoolCode } from '../../utils/validators';

type Props = NativeStackScreenProps<AuthStackParamList, 'ParentLogin'>;

const { width } = Dimensions.get('window');

export const ParentLoginScreen: React.FC<Props> = ({ navigation }) => {
  const { colors, isDarkMode } = useTheme();
  const { login } = useAuth();

  const [schoolCode, setSchoolCode] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [rememberMe, setRememberMe] = useState(true);
  const [loading, setLoading] = useState(false);
  const [activeTab, setActiveTab] = useState<'password' | 'otp'>('password');
  const [focusedInput, setFocusedInput] = useState<'schoolCode' | 'email' | 'password' | null>(null);
  const [errors, setErrors] = useState<{ schoolCode?: string; email?: string; password?: string }>({});
  const [tabContainerWidth, setTabContainerWidth] = useState(width - 96);

  // Animations
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideAnim = useRef(new Animated.Value(35)).current;
  const logoScale = useRef(new Animated.Value(0.85)).current;
  const logoFloat = useRef(new Animated.Value(0)).current;
  const buttonScale = useRef(new Animated.Value(1)).current;
  const tabAnim = useRef(new Animated.Value(0)).current;
  const pulseAnim = useRef(new Animated.Value(1)).current;
  const shakeAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    // 1. Entrance animations with smooth spring & cubic easing
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 650,
        useNativeDriver: true,
        easing: Easing.out(Easing.cubic),
      }),
      Animated.spring(slideAnim, {
        toValue: 0,
        tension: 45,
        friction: 8,
        useNativeDriver: true,
      }),
      Animated.spring(logoScale, {
        toValue: 1,
        tension: 50,
        friction: 6,
        useNativeDriver: true,
      }),
    ]).start();

    // 2. Subtle floating animation for the brand logo
    Animated.loop(
      Animated.sequence([
        Animated.timing(logoFloat, {
          toValue: -5,
          duration: 2200,
          easing: Easing.inOut(Easing.sin),
          useNativeDriver: true,
        }),
        Animated.timing(logoFloat, {
          toValue: 0,
          duration: 2200,
          easing: Easing.inOut(Easing.sin),
          useNativeDriver: true,
        }),
      ])
    ).start();

    // 3. Subtle background ambient pulse (matching web ERP glowing circles)
    Animated.loop(
      Animated.sequence([
        Animated.timing(pulseAnim, {
          toValue: 1.08,
          duration: 3500,
          easing: Easing.inOut(Easing.ease),
          useNativeDriver: true,
        }),
        Animated.timing(pulseAnim, {
          toValue: 1,
          duration: 3500,
          easing: Easing.inOut(Easing.ease),
          useNativeDriver: true,
        }),
      ])
    ).start();

    // 4. Load saved school code if previously remembered
    const loadSavedSchoolCode = async () => {
      const code = await Storage.getSavedSchoolCode();
      if (code) setSchoolCode(code);
    };
    loadSavedSchoolCode();
  }, []);

  // Trigger smooth horizontal shake when validation fails or login rejected
  const triggerShake = () => {
    Animated.sequence([
      Animated.timing(shakeAnim, { toValue: 8, duration: 50, useNativeDriver: true }),
      Animated.timing(shakeAnim, { toValue: -8, duration: 50, useNativeDriver: true }),
      Animated.timing(shakeAnim, { toValue: 6, duration: 50, useNativeDriver: true }),
      Animated.timing(shakeAnim, { toValue: -6, duration: 50, useNativeDriver: true }),
      Animated.timing(shakeAnim, { toValue: 0, duration: 50, useNativeDriver: true }),
    ]).start();
  };

  const handleTabChange = (tab: 'password' | 'otp') => {
    setActiveTab(tab);
    Animated.spring(tabAnim, {
      toValue: tab === 'password' ? 0 : 1,
      tension: 65,
      friction: 9,
      useNativeDriver: true,
    }).start();

    if (tab === 'otp') {
      navigation.navigate('OtpLogin');
      // Reset tab indicator after user transitions
      setTimeout(() => {
        setActiveTab('password');
        tabAnim.setValue(0);
      }, 500);
    }
  };

  const handlePressIn = () => {
    Animated.spring(buttonScale, {
      toValue: 0.96,
      useNativeDriver: true,
    }).start();
  };

  const handlePressOut = () => {
    Animated.spring(buttonScale, {
      toValue: 1,
      friction: 4,
      tension: 40,
      useNativeDriver: true,
    }).start();
  };

  const handleLogin = async () => {
    const newErrors: { schoolCode?: string; email?: string; password?: string } = {};

    if (!isValidSchoolCode(schoolCode)) {
      newErrors.schoolCode = 'Please enter a valid school code.';
    }
    if (!email.trim()) {
      newErrors.email = 'Please enter your registered email or phone.';
    }
    if (!isValidPassword(password)) {
      newErrors.password = 'Password must be at least 4 characters.';
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      triggerShake();
      return;
    }

    setErrors({});
    setLoading(true);

    try {
      await login({
        school_code: schoolCode.trim().toUpperCase(),
        email: email.trim(),
        password: password,
      });
      // AuthContext updates isAuthenticated -> RootNavigator switches to MainTabs smoothly
    } catch (error: any) {
      triggerShake();
      const msg =
        error.response?.data?.message ||
        error.message ||
        'Invalid credentials. Please verify your school code, email and password.';
      Alert.alert('Login Failed', msg);
    } finally {
      setLoading(false);
    }
  };

  const halfTabWidth = tabContainerWidth / 2;
  const tabIndicatorTranslateX = tabAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [0, halfTabWidth],
  });

  return (
    <View style={[styles.root, { backgroundColor: isDarkMode ? '#080e1e' : '#f8fafc' }]}>
      <StatusBar
        barStyle={isDarkMode ? 'light-content' : 'dark-content'}
        backgroundColor="transparent"
        translucent
      />

      {/* Decorative Ambient Geometry matching Educorerp Web Login */}
      <Animated.View
        style={[
          styles.bgCircle1,
          {
            backgroundColor: isDarkMode ? 'rgba(2, 82, 217, 0.22)' : 'rgba(2, 82, 217, 0.08)',
            transform: [{ scale: pulseAnim }],
          },
        ]}
      />
      <Animated.View
        style={[
          styles.bgCircle2,
          {
            backgroundColor: isDarkMode ? 'rgba(0, 210, 255, 0.15)' : 'rgba(0, 210, 255, 0.07)',
            transform: [{ scale: pulseAnim }],
          },
        ]}
      />

      {/* Decorative Dot Matrix Pattern in Corners */}
      <View style={styles.dotMatrixLeft} pointerEvents="none">
        {[...Array(12)].map((_, i) => (
          <View
            key={i}
            style={[
              styles.matrixDot,
              { backgroundColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(2, 82, 217, 0.12)' },
            ]}
          />
        ))}
      </View>
      <View style={styles.dotMatrixRight} pointerEvents="none">
        {[...Array(12)].map((_, i) => (
          <View
            key={i}
            style={[
              styles.matrixDot,
              { backgroundColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0, 210, 255, 0.12)' },
            ]}
          />
        ))}
      </View>

      <KeyboardAvoidingView
        style={{ flex: 1 }}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      >
        <ScrollView
          contentContainerStyle={styles.scrollContent}
          keyboardShouldPersistTaps="handled"
          showsVerticalScrollIndicator={false}
        >
          {/* Top Brand Logo Section */}
          <Animated.View
            style={[
              styles.brandSection,
              {
                opacity: fadeAnim,
                transform: [{ scale: logoScale }, { translateY: logoFloat }],
              },
            ]}
          >
            <View style={styles.logoIconBox}>
              <Text style={styles.logoGradIcon}>🎓</Text>
            </View>
            <Text style={[styles.brandTitle, { color: isDarkMode ? '#ffffff' : '#0b1727' }]}>
              {APP_NAME}
            </Text>
            <Text style={[styles.brandSubtitle, { color: colors.textSecondary }]}>
              Cloud School Management Platform
            </Text>
          </Animated.View>

          {/* Main Animated Login Card with Spring & Shake */}
          <Animated.View
            style={[
              styles.card,
              {
                backgroundColor: colors.surface,
                borderColor: isDarkMode ? '#1e293b' : '#e2e8f0',
                opacity: fadeAnim,
                transform: [{ translateY: slideAnim }, { translateX: shakeAnim }],
              },
            ]}
          >
            {/* Header */}
            <View style={styles.cardHeader}>
              <Text style={[styles.welcomeTitle, { color: colors.text }]}>
                Welcome back
              </Text>
              <Text style={[styles.welcomeSubtitle, { color: colors.textSecondary }]}>
                Please enter your details to access your account.
              </Text>
            </View>

            {/* Segmented Switcher with Animated Sliding Pill */}
            <View
              style={[styles.tabSwitcher, { backgroundColor: isDarkMode ? '#17223b' : '#f1f5f9' }]}
              onLayout={(e: LayoutChangeEvent) => {
                const w = e.nativeEvent.layout.width - 8;
                if (w > 0) setTabContainerWidth(w);
              }}
            >
              <Animated.View
                style={[
                  styles.animatedPill,
                  {
                    width: halfTabWidth,
                    transform: [{ translateX: tabIndicatorTranslateX }],
                  },
                ]}
              />

              <TouchableOpacity
                activeOpacity={0.8}
                onPress={() => handleTabChange('password')}
                style={styles.tabBtn}
              >
                <Text style={{ fontSize: 13, marginRight: 6 }}>🔑</Text>
                <Text
                  style={[
                    styles.tabBtnText,
                    activeTab === 'password'
                      ? { color: '#ffffff', fontWeight: '700' }
                      : { color: colors.textSecondary },
                  ]}
                >
                  Password Login
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                activeOpacity={0.8}
                onPress={() => handleTabChange('otp')}
                style={styles.tabBtn}
              >
                <Text style={{ fontSize: 13, marginRight: 6 }}>📱</Text>
                <Text
                  style={[
                    styles.tabBtnText,
                    activeTab === 'otp'
                      ? { color: '#ffffff', fontWeight: '700' }
                      : { color: colors.textSecondary },
                  ]}
                >
                  OTP Login
                </Text>
              </TouchableOpacity>
            </View>

            {/* Form Fields */}
            <View style={styles.form}>
              {/* School Code */}
              <View style={styles.inputGroup}>
                <Text style={[styles.inputLabel, { color: colors.textSecondary }]}>
                  School Code
                </Text>
                <View
                  style={[
                    styles.inputWrapper,
                    {
                      backgroundColor: isDarkMode ? '#0b1325' : '#f8fafc',
                      borderColor: errors.schoolCode
                        ? colors.danger
                        : focusedInput === 'schoolCode'
                        ? '#0252D9'
                        : colors.border,
                      borderWidth: focusedInput === 'schoolCode' ? 1.8 : 1.2,
                    },
                  ]}
                >
                  <Text style={styles.inputLeadingIcon}>🏫</Text>
                  <TextInput
                    style={[styles.textInput, { color: colors.text }]}
                    placeholder="e.g. SCH001"
                    placeholderTextColor={colors.textMuted}
                    value={schoolCode}
                    onFocus={() => setFocusedInput('schoolCode')}
                    onBlur={() => setFocusedInput(null)}
                    onChangeText={(t) => {
                      setSchoolCode(t.toUpperCase());
                      if (errors.schoolCode) setErrors({ ...errors, schoolCode: undefined });
                    }}
                    autoCapitalize="characters"
                    autoCorrect={false}
                  />
                </View>
                {errors.schoolCode && (
                  <Text style={[styles.fieldError, { color: colors.danger }]}>
                    {errors.schoolCode}
                  </Text>
                )}
              </View>

              {/* Email / Mobile Number */}
              <View style={styles.inputGroup}>
                <Text style={[styles.inputLabel, { color: colors.textSecondary }]}>
                  Email or Mobile Number
                </Text>
                <View
                  style={[
                    styles.inputWrapper,
                    {
                      backgroundColor: isDarkMode ? '#0b1325' : '#f8fafc',
                      borderColor: errors.email
                        ? colors.danger
                        : focusedInput === 'email'
                        ? '#0252D9'
                        : colors.border,
                      borderWidth: focusedInput === 'email' ? 1.8 : 1.2,
                    },
                  ]}
                >
                  <Text style={styles.inputLeadingIcon}>✉️</Text>
                  <TextInput
                    style={[styles.textInput, { color: colors.text }]}
                    placeholder="name@example.com or mobile"
                    placeholderTextColor={colors.textMuted}
                    value={email}
                    onFocus={() => setFocusedInput('email')}
                    onBlur={() => setFocusedInput(null)}
                    onChangeText={(t) => {
                      setEmail(t);
                      if (errors.email) setErrors({ ...errors, email: undefined });
                    }}
                    keyboardType="email-address"
                    autoCapitalize="none"
                    autoCorrect={false}
                  />
                </View>
                {errors.email && (
                  <Text style={[styles.fieldError, { color: colors.danger }]}>
                    {errors.email}
                  </Text>
                )}
              </View>

              {/* Password */}
              <View style={styles.inputGroup}>
                <Text style={[styles.inputLabel, { color: colors.textSecondary }]}>
                  Password
                </Text>
                <View
                  style={[
                    styles.inputWrapper,
                    {
                      backgroundColor: isDarkMode ? '#0b1325' : '#f8fafc',
                      borderColor: errors.password
                        ? colors.danger
                        : focusedInput === 'password'
                        ? '#0252D9'
                        : colors.border,
                      borderWidth: focusedInput === 'password' ? 1.8 : 1.2,
                    },
                  ]}
                >
                  <Text style={styles.inputLeadingIcon}>🔒</Text>
                  <TextInput
                    style={[styles.textInput, { color: colors.text }]}
                    placeholder="Enter your password"
                    placeholderTextColor={colors.textMuted}
                    value={password}
                    onFocus={() => setFocusedInput('password')}
                    onBlur={() => setFocusedInput(null)}
                    onChangeText={(t) => {
                      setPassword(t);
                      if (errors.password) setErrors({ ...errors, password: undefined });
                    }}
                    secureTextEntry={!showPassword}
                    autoCapitalize="none"
                    autoCorrect={false}
                  />
                  <TouchableOpacity
                    onPress={() => setShowPassword(!showPassword)}
                    style={styles.eyeBtn}
                    activeOpacity={0.7}
                  >
                    <Text style={{ fontSize: 16 }}>{showPassword ? '👁️' : '🙈'}</Text>
                  </TouchableOpacity>
                </View>
                {errors.password && (
                  <Text style={[styles.fieldError, { color: colors.danger }]}>
                    {errors.password}
                  </Text>
                )}
              </View>

              {/* Options Row: Remember Me & Forgot Password */}
              <View style={styles.optionsRow}>
                <TouchableOpacity
                  activeOpacity={0.8}
                  style={styles.rememberWrap}
                  onPress={() => setRememberMe(!rememberMe)}
                >
                  <View
                    style={[
                      styles.checkbox,
                      rememberMe && { backgroundColor: '#0252D9', borderColor: '#0252D9' },
                      !rememberMe && { borderColor: colors.border },
                    ]}
                  >
                    {rememberMe && <Text style={styles.checkMark}>✓</Text>}
                  </View>
                  <Text style={[styles.rememberText, { color: colors.textSecondary }]}>
                    Remember me
                  </Text>
                </TouchableOpacity>

                <TouchableOpacity
                  activeOpacity={0.7}
                  onPress={() => navigation.navigate('ForgotPassword', { school_code: schoolCode })}
                >
                  <Text style={[styles.forgotText, { color: '#0252D9' }]}>
                    Forgot Password?
                  </Text>
                </TouchableOpacity>
              </View>

              {/* Animated Login Button with Spring Physics */}
              <Animated.View style={{ transform: [{ scale: buttonScale }] }}>
                <TouchableOpacity
                  activeOpacity={0.9}
                  onPressIn={handlePressIn}
                  onPressOut={handlePressOut}
                  onPress={handleLogin}
                  disabled={loading}
                  style={styles.loginBtn}
                >
                  {loading ? (
                    <ActivityIndicator color="#ffffff" size="small" />
                  ) : (
                    <View style={styles.loginBtnContent}>
                      <Text style={styles.loginBtnText}>Login</Text>
                      <Text style={styles.loginBtnArrow}>→</Text>
                    </View>
                  )}
                </TouchableOpacity>
              </Animated.View>
            </View>
          </Animated.View>

          {/* Footer Note */}
          <View style={styles.footer}>
            <Text style={[styles.footerText, { color: colors.textMuted }]}>
              {APP_NAME} • Secure & Encrypted Connection
            </Text>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
};

const styles = StyleSheet.create({
  root: {
    flex: 1,
  },
  bgCircle1: {
    position: 'absolute',
    top: -120,
    right: -100,
    width: 320,
    height: 320,
    borderRadius: 160,
  },
  bgCircle2: {
    position: 'absolute',
    bottom: -60,
    left: -80,
    width: 260,
    height: 260,
    borderRadius: 130,
  },
  dotMatrixLeft: {
    position: 'absolute',
    top: 50,
    left: 20,
    width: 48,
    height: 48,
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  dotMatrixRight: {
    position: 'absolute',
    top: 50,
    right: 20,
    width: 48,
    height: 48,
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  matrixDot: {
    width: 4,
    height: 4,
    borderRadius: 2,
  },
  scrollContent: {
    paddingHorizontal: Spacing.xl,
    paddingTop: Platform.OS === 'ios' ? 60 : 45,
    paddingBottom: Spacing.xxxl,
    flexGrow: 1,
    justifyContent: 'center',
  },
  brandSection: {
    alignItems: 'center',
    marginBottom: Spacing.xl,
  },
  logoIconBox: {
    width: 58,
    height: 58,
    borderRadius: 18,
    backgroundColor: '#0252D9',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: Spacing.sm,
    shadowColor: '#0252D9',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.35,
    shadowRadius: 14,
    elevation: 8,
  },
  logoGradIcon: {
    fontSize: 28,
  },
  brandTitle: {
    fontSize: 28,
    fontWeight: '800',
    letterSpacing: -0.5,
  },
  brandSubtitle: {
    fontSize: 12,
    fontWeight: '500',
    marginTop: 2,
    letterSpacing: 0.2,
  },
  card: {
    borderRadius: BorderRadius.xxl,
    padding: Spacing.xl,
    borderWidth: 1,
    ...Shadows.card,
  },
  cardHeader: {
    marginBottom: Spacing.lg,
  },
  welcomeTitle: {
    fontSize: 22,
    fontWeight: '800',
    letterSpacing: -0.3,
  },
  welcomeSubtitle: {
    fontSize: Typography.size.sm,
    lineHeight: 20,
    marginTop: 3,
  },
  tabSwitcher: {
    flexDirection: 'row',
    borderRadius: 12,
    padding: 4,
    marginBottom: Spacing.lg,
    position: 'relative',
  },
  animatedPill: {
    position: 'absolute',
    top: 4,
    left: 4,
    bottom: 4,
    backgroundColor: '#0252D9',
    borderRadius: 9,
    shadowColor: '#0252D9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 5,
    elevation: 3,
  },
  tabBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 9,
    zIndex: 2,
  },
  tabBtnText: {
    fontSize: 12,
    fontWeight: '600',
  },
  form: {
    width: '100%',
  },
  inputGroup: {
    marginBottom: Spacing.md,
  },
  inputLabel: {
    fontSize: Typography.size.xs,
    fontWeight: '600',
    marginBottom: 6,
  },
  inputWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    borderRadius: BorderRadius.md,
    paddingHorizontal: Spacing.md,
    height: 50,
  },
  inputLeadingIcon: {
    fontSize: 16,
    marginRight: Spacing.sm,
  },
  textInput: {
    flex: 1,
    fontSize: Typography.size.md,
    height: '100%',
  },
  eyeBtn: {
    padding: Spacing.xs,
  },
  fieldError: {
    fontSize: Typography.size.xs,
    marginTop: 4,
    fontWeight: '500',
  },
  optionsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 2,
    marginBottom: Spacing.xl,
  },
  rememberWrap: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  checkbox: {
    width: 18,
    height: 18,
    borderRadius: 4,
    borderWidth: 1.5,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 8,
  },
  checkMark: {
    color: '#ffffff',
    fontSize: 11,
    fontWeight: 'bold',
  },
  rememberText: {
    fontSize: Typography.size.xs,
    fontWeight: '500',
  },
  forgotText: {
    fontSize: Typography.size.xs,
    fontWeight: '600',
  },
  loginBtn: {
    height: 50,
    borderRadius: BorderRadius.md,
    backgroundColor: '#0252D9',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#0252D9',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.35,
    shadowRadius: 10,
    elevation: 5,
  },
  loginBtnContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  loginBtnText: {
    color: '#ffffff',
    fontSize: Typography.size.md,
    fontWeight: '700',
    letterSpacing: 0.3,
  },
  loginBtnArrow: {
    color: '#ffffff',
    fontSize: 18,
    fontWeight: '700',
    marginLeft: Spacing.sm,
  },
  footer: {
    marginTop: Spacing.xl,
    alignItems: 'center',
  },
  footerText: {
    fontSize: Typography.size.xs,
  },
});
