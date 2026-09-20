import AsyncStorage from '@react-native-async-storage/async-storage';
import { STORAGE_KEYS } from '../config/constants';
import { ParentUser, SchoolInfo } from '../types/auth';

export const Storage = {
  // Token
  async setToken(token: string): Promise<void> {
    try {
      await AsyncStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token);
    } catch (e) {
      console.error('Failed to save auth token:', e);
    }
  },

  async getToken(): Promise<string | null> {
    try {
      return await AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);
    } catch (e) {
      console.error('Failed to get auth token:', e);
      return null;
    }
  },

  async removeToken(): Promise<void> {
    try {
      await AsyncStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN);
    } catch (e) {
      console.error('Failed to remove auth token:', e);
    }
  },

  // User
  async setUser(user: ParentUser): Promise<void> {
    try {
      await AsyncStorage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(user));
    } catch (e) {
      console.error('Failed to save user data:', e);
    }
  },

  async getUser(): Promise<ParentUser | null> {
    try {
      const data = await AsyncStorage.getItem(STORAGE_KEYS.USER_DATA);
      return data ? JSON.parse(data) : null;
    } catch (e) {
      console.error('Failed to get user data:', e);
      return null;
    }
  },

  // School
  async setSchool(school: SchoolInfo): Promise<void> {
    try {
      await AsyncStorage.setItem(STORAGE_KEYS.SCHOOL_DATA, JSON.stringify(school));
      if (school.code) {
        await AsyncStorage.setItem(STORAGE_KEYS.SAVED_SCHOOL_CODE, school.code);
      }
    } catch (e) {
      console.error('Failed to save school data:', e);
    }
  },

  async getSchool(): Promise<SchoolInfo | null> {
    try {
      const data = await AsyncStorage.getItem(STORAGE_KEYS.SCHOOL_DATA);
      return data ? JSON.parse(data) : null;
    } catch (e) {
      console.error('Failed to get school data:', e);
      return null;
    }
  },

  async getSavedSchoolCode(): Promise<string | null> {
    try {
      return await AsyncStorage.getItem(STORAGE_KEYS.SAVED_SCHOOL_CODE);
    } catch (e) {
      return null;
    }
  },

  // Active Child ID
  async setActiveChildId(childId: number): Promise<void> {
    try {
      await AsyncStorage.setItem(STORAGE_KEYS.ACTIVE_CHILD_ID, childId.toString());
    } catch (e) {
      console.error('Failed to save active child ID:', e);
    }
  },

  async getActiveChildId(): Promise<number | null> {
    try {
      const id = await AsyncStorage.getItem(STORAGE_KEYS.ACTIVE_CHILD_ID);
      return id ? parseInt(id, 10) : null;
    } catch (e) {
      return null;
    }
  },

  // Clear Session
  async clearSession(): Promise<void> {
    try {
      await AsyncStorage.multiRemove([
        STORAGE_KEYS.AUTH_TOKEN,
        STORAGE_KEYS.USER_DATA,
        STORAGE_KEYS.SCHOOL_DATA,
        STORAGE_KEYS.ACTIVE_CHILD_ID,
      ]);
    } catch (e) {
      console.error('Failed to clear storage session:', e);
    }
  },
};
