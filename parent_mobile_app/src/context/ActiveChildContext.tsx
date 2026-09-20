import React, { createContext, useContext, useEffect, useState } from 'react';
import { ChildrenApi } from '../api/childrenApi';
import { StudentChild } from '../types/student';
import { Storage } from '../utils/storage';
import { useAuth } from './AuthContext';

interface ActiveChildContextType {
  childrenList: StudentChild[];
  activeChild: StudentChild | null;
  isLoadingChildren: boolean;
  switchChild: (childId: number) => Promise<void>;
  refreshChildren: () => Promise<void>;
}

const ActiveChildContext = createContext<ActiveChildContextType | undefined>(undefined);

export const ActiveChildProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { isAuthenticated } = useAuth();
  const [childrenList, setChildrenList] = useState<StudentChild[]>([]);
  const [activeChild, setActiveChild] = useState<StudentChild | null>(null);
  const [isLoadingChildren, setIsLoadingChildren] = useState<boolean>(false);

  const fetchChildren = async () => {
    if (!isAuthenticated) return;
    setIsLoadingChildren(true);
    try {
      const list = await ChildrenApi.getChildren();
      setChildrenList(list);

      if (list.length > 0) {
        const savedChildId = await Storage.getActiveChildId();
        const matched = list.find((c) => c.id === savedChildId);
        if (matched) {
          setActiveChild(matched);
        } else {
          // Default to the first child
          setActiveChild(list[0]);
          await Storage.setActiveChildId(list[0].id);
        }
      } else {
        setActiveChild(null);
      }
    } catch (error) {
      console.warn('Failed to load parent children:', error);
    } finally {
      setIsLoadingChildren(false);
    }
  };

  useEffect(() => {
    if (isAuthenticated) {
      fetchChildren();
    } else {
      setChildrenList([]);
      setActiveChild(null);
    }
  }, [isAuthenticated]);

  const switchChild = async (childId: number) => {
    const selected = childrenList.find((c) => c.id === childId);
    if (selected) {
      setActiveChild(selected);
      await Storage.setActiveChildId(childId);
    }
  };

  const refreshChildren = async () => {
    await fetchChildren();
  };

  return (
    <ActiveChildContext.Provider
      value={{
        childrenList,
        activeChild,
        isLoadingChildren,
        switchChild,
        refreshChildren,
      }}
    >
      {children}
    </ActiveChildContext.Provider>
  );
};

export const useActiveChild = (): ActiveChildContextType => {
  const context = useContext(ActiveChildContext);
  if (!context) {
    throw new Error('useActiveChild must be used within an ActiveChildProvider');
  }
  return context;
};
