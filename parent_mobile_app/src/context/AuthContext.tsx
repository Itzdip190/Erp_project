import React, { createContext, useContext, useEffect, useState } from 'react';
import { AuthApi } from '../api/authApi';
import {
  AuthResponseData,
  LoginCredentials,
  OtpVerifyRequest,
  ParentUser,
  SchoolInfo,
} from '../types/auth';
import { Storage } from '../utils/storage';

interface AuthContextType {
  user: ParentUser | null;
  school: SchoolInfo | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (credentials: LoginCredentials) => Promise<AuthResponseData>;
  verifyOtp: (payload: OtpVerifyRequest) => Promise<AuthResponseData>;
  logout: () => Promise<void>;
  refreshProfile: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<ParentUser | null>(null);
  const [school, setSchool] = useState<SchoolInfo | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  // Restore stored session on startup
  useEffect(() => {
    const restoreSession = async () => {
      try {
        const storedToken = await Storage.getToken();
        const storedUser = await Storage.getUser();
        const storedSchool = await Storage.getSchool();

        if (storedToken && storedUser) {
          // Strict Role Guard
          if (storedUser.role === 'parent' || storedUser.role === 'student') {
            setToken(storedToken);
            setUser(storedUser);
            setSchool(storedSchool);
          } else {
            await Storage.clearSession();
          }
        }
      } catch (error) {
        console.warn('Session restoration failed:', error);
      } finally {
        setIsLoading(false);
      }
    };

    restoreSession();
  }, []);

  const login = async (credentials: LoginCredentials): Promise<AuthResponseData> => {
    setIsLoading(true);
    try {
      const data = await AuthApi.login(credentials);

      // Save to storage
      await Storage.setToken(data.token);
      await Storage.setUser(data.user);
      if (data.school) {
        await Storage.setSchool(data.school);
      }

      setToken(data.token);
      setUser(data.user);
      setSchool(data.school);

      return data;
    } finally {
      setIsLoading(false);
    }
  };

  const verifyOtp = async (payload: OtpVerifyRequest): Promise<AuthResponseData> => {
    setIsLoading(true);
    try {
      const data = await AuthApi.verifyOtp(payload);

      await Storage.setToken(data.token);
      await Storage.setUser(data.user);
      if (data.school) {
        await Storage.setSchool(data.school);
      }

      setToken(data.token);
      setUser(data.user);
      setSchool(data.school);

      return data;
    } finally {
      setIsLoading(false);
    }
  };

  const logout = async () => {
    try {
      await AuthApi.logout();
    } catch (e) {
      console.warn('Logout failed on backend:', e);
    } finally {
      await Storage.clearSession();
      setToken(null);
      setUser(null);
      setSchool(null);
    }
  };

  const refreshProfile = async () => {
    try {
      const updatedUser = await AuthApi.getProfile();
      setUser(updatedUser);
      await Storage.setUser(updatedUser);
    } catch (e) {
      console.warn('Failed to refresh user profile:', e);
    }
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        school,
        token,
        isAuthenticated: !!token && !!user,
        isLoading,
        login,
        verifyOtp,
        logout,
        refreshProfile,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
