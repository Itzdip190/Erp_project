import React, { useState } from 'react';
import { Alert, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { CustomButton } from '../../components/common/CustomButton';
import { CustomInput } from '../../components/common/CustomInput';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useTheme } from '../../context/ThemeContext';
import { AuthStackParamList } from '../../types/navigation';

type Props = NativeStackScreenProps<AuthStackParamList, 'ForgotPassword'>;

export const ForgotPasswordScreen: React.FC<Props> = ({ navigation, route }) => {
  const { colors } = useTheme();
  const [schoolCode, setSchoolCode] = useState(route.params?.school_code || '');
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);

  const handleResetRequest = () => {
    if (!schoolCode.trim() || !email.trim()) {
      Alert.alert('Required Fields', 'Please enter your School Code and Registered Email.');
      return;
    }

    setLoading(true);
    setTimeout(() => {
      setLoading(false);
      Alert.alert(
        'Password Reset Link Sent',
        'If the email is registered with the school, password reset instructions have been dispatched. You can also contact the school administrator.',
        [{ text: 'OK', onPress: () => navigation.goBack() }]
      );
    }, 1000);
  };

  return (
    <ScreenWrapper>
      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled">
        <TouchableOpacity style={styles.backButton} onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back to Login</Text>
        </TouchableOpacity>

        <View style={styles.header}>
          <Text style={[styles.title, { color: colors.text }]}>Reset Password</Text>
          <Text style={[styles.subtitle, { color: colors.textSecondary }]}>
            Enter your school code and registered email. We will guide you on recovering access.
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
          <CustomInput
            label="School Code"
            placeholder="e.g. SCH001"
            value={schoolCode}
            onChangeText={(t) => setSchoolCode(t.toUpperCase())}
            autoCapitalize="characters"
          />

          <CustomInput
            label="Registered Email"
            placeholder="name@example.com"
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
            autoCapitalize="none"
          />

          <CustomButton
            title="Request Reset Instructions"
            onPress={handleResetRequest}
            loading={loading}
            size="lg"
            style={styles.actionBtn}
          />
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
    marginTop: Spacing.md,
  },
});
