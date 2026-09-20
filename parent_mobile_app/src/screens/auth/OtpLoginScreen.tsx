import React, { useState } from 'react';
import {
  Alert,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { AuthApi } from '../../api/authApi';
import { CustomButton } from '../../components/common/CustomButton';
import { CustomInput } from '../../components/common/CustomInput';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { AuthStackParamList } from '../../types/navigation';
import { isValidSchoolCode } from '../../utils/validators';

type Props = NativeStackScreenProps<AuthStackParamList, 'OtpLogin'>;

export const OtpLoginScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { verifyOtp } = useAuth();

  const [step, setStep] = useState<'request' | 'verify'>('request');
  const [schoolCode, setSchoolCode] = useState('');
  const [phoneOrEmail, setPhoneOrEmail] = useState('');
  const [otp, setOtp] = useState('');
  const [loading, setLoading] = useState(false);
  const [testOtp, setTestOtp] = useState<string | null>(null);

  const handleSendOtp = async () => {
    if (!isValidSchoolCode(schoolCode)) {
      Alert.alert('Validation Error', 'Please enter your School Code.');
      return;
    }
    if (!phoneOrEmail.trim()) {
      Alert.alert('Validation Error', 'Please enter your registered phone number or email.');
      return;
    }

    setLoading(true);
    try {
      const res = await AuthApi.sendOtp({
        school_code: schoolCode.trim().toUpperCase(),
        phone_or_email: phoneOrEmail.trim(),
      });
      if (res.test_otp) {
        setTestOtp(res.test_otp);
      }
      setStep('verify');
      Alert.alert('OTP Sent', res.message || 'OTP has been sent to your registered contact.');
    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || error.message || 'Failed to send OTP.');
    } finally {
      setLoading(false);
    }
  };

  const handleVerifyOtp = async () => {
    if (!otp.trim() || otp.length < 4) {
      Alert.alert('Validation Error', 'Please enter the 4-6 digit OTP.');
      return;
    }

    setLoading(true);
    try {
      await verifyOtp({
        school_code: schoolCode.trim().toUpperCase(),
        phone_or_email: phoneOrEmail.trim(),
        otp: otp.trim(),
      });
    } catch (error: any) {
      Alert.alert('OTP Verification Failed', error.response?.data?.message || error.message || 'Invalid OTP.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScreenWrapper>
      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled">
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => (step === 'verify' ? setStep('request') : navigation.goBack())}
        >
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>

        <View style={styles.header}>
          <Text style={[styles.title, { color: colors.text }]}>
            {step === 'request' ? 'OTP Login' : 'Verify Security Code'}
          </Text>
          <Text style={[styles.subtitle, { color: colors.textSecondary }]}>
            {step === 'request'
              ? 'Enter your school code and registered mobile number to receive a one-time password.'
              : `Enter the verification code sent to ${phoneOrEmail}`}
          </Text>
        </View>

        <View
          style={[
            styles.card,
            {
              backgroundColor: colors.surface,
              borderColor: colors.borderSubtle,
            },
          ]}
        >
          {step === 'request' ? (
            <>
              <CustomInput
                label="School Code"
                placeholder="e.g. SCH001"
                value={schoolCode}
                onChangeText={(t) => setSchoolCode(t.toUpperCase())}
                autoCapitalize="characters"
              />

              <CustomInput
                label="Registered Mobile Number or Email"
                placeholder="e.g. 9876543210 or user@email.com"
                value={phoneOrEmail}
                onChangeText={setPhoneOrEmail}
                keyboardType="email-address"
                autoCapitalize="none"
              />

              <CustomButton
                title="Send One-Time Password"
                onPress={handleSendOtp}
                loading={loading}
                size="lg"
                style={styles.actionBtn}
              />
            </>
          ) : (
            <>
              {testOtp && (
                <View style={[styles.testOtpBanner, { backgroundColor: colors.infoLight }]}>
                  <Text style={[styles.testOtpText, { color: colors.info }]}>
                    Demo Test OTP: <Text style={{ fontWeight: 'bold' }}>{testOtp}</Text>
                  </Text>
                </View>
              )}

              <CustomInput
                label="Enter 4-6 Digit OTP"
                placeholder="• • • • • •"
                value={otp}
                onChangeText={setOtp}
                keyboardType="number-pad"
                maxLength={6}
                inputStyle={styles.otpInput}
              />

              <CustomButton
                title="Verify & Login"
                onPress={handleVerifyOtp}
                loading={loading}
                size="lg"
                style={styles.actionBtn}
              />

              <TouchableOpacity
                onPress={handleSendOtp}
                disabled={loading}
                style={styles.resendBtn}
              >
                <Text style={[styles.resendText, { color: colors.primary }]}>
                  Didn't receive code? Resend OTP
                </Text>
              </TouchableOpacity>
            </>
          )}
        </View>
      </ScrollView>
    </ScreenWrapper>
  );
};

const styles = StyleSheet.create({
  scrollContent: {
    paddingHorizontal: Spacing.xl,
    paddingVertical: Spacing.xl,
    flexGrow: 1,
  },
  backButton: {
    marginBottom: Spacing.lg,
  },
  backText: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.semibold,
  },
  header: {
    marginBottom: Spacing.xl,
  },
  title: {
    fontSize: Typography.size.xxl,
    fontWeight: Typography.weight.bold,
    marginBottom: Spacing.xs,
  },
  subtitle: {
    fontSize: Typography.size.sm,
    lineHeight: 20,
  },
  card: {
    borderRadius: BorderRadius.xl,
    padding: Spacing.xl,
    borderWidth: 1,
    ...Shadows.card,
  },
  actionBtn: {
    marginTop: Spacing.sm,
  },
  testOtpBanner: {
    padding: Spacing.sm + 2,
    borderRadius: BorderRadius.md,
    marginBottom: Spacing.md,
    alignItems: 'center',
  },
  testOtpText: {
    fontSize: Typography.size.xs,
  },
  otpInput: {
    textAlign: 'center',
    letterSpacing: 8,
    fontSize: Typography.size.xl,
    fontWeight: 'bold',
  },
  resendBtn: {
    marginTop: Spacing.md,
    alignItems: 'center',
  },
  resendText: {
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.semibold,
  },
});
