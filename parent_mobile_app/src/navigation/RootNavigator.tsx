import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { LoadingIndicator } from '../components/common/LoadingIndicator';
import { ScreenWrapper } from '../components/common/ScreenWrapper';
import { ActiveChildProvider } from '../context/ActiveChildContext';
import { useAuth } from '../context/AuthContext';
import { AuthNavigator } from './AuthNavigator';
import { ParentStackNavigator } from './ParentStackNavigator';

export const RootNavigator: React.FC = () => {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return (
      <ScreenWrapper>
        <LoadingIndicator message="Connecting to Parent Portal..." />
      </ScreenWrapper>
    );
  }

  return (
    <NavigationContainer>
      {isAuthenticated ? (
        <ActiveChildProvider>
          <ParentStackNavigator />
        </ActiveChildProvider>
      ) : (
        <AuthNavigator />
      )}
    </NavigationContainer>
  );
};
