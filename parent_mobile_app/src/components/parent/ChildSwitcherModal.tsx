import React from 'react';
import {
  FlatList,
  Image,
  Modal,
  StyleSheet,
  Text,
  TouchableOpacity,
  TouchableWithoutFeedback,
  View,
} from 'react-native';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { StudentChild } from '../../types/student';

interface ChildSwitcherModalProps {
  visible: boolean;
  onClose: () => void;
}

export const ChildSwitcherModal: React.FC<ChildSwitcherModalProps> = ({
  visible,
  onClose,
}) => {
  const { colors } = useTheme();
  const { childrenList, activeChild, switchChild } = useActiveChild();

  const handleSelectChild = async (child: StudentChild) => {
    await switchChild(child.id);
    onClose();
  };

  const renderChildItem = ({ item }: { item: StudentChild }) => {
    const isSelected = activeChild?.id === item.id;
    const displayName = item.full_name || `${item.first_name} ${item.last_name || ''}`.trim();
    const className = item.class?.name ? `Class ${item.class.name}` : '';
    const sectionName = item.section?.name ? `- ${item.section.name}` : '';

    return (
      <TouchableOpacity
        activeOpacity={0.7}
        onPress={() => handleSelectChild(item)}
        style={[
          styles.childCard,
          {
            backgroundColor: isSelected ? colors.primaryLight + '15' : colors.surface,
            borderColor: isSelected ? colors.primary : colors.border,
          },
        ]}
      >
        <View style={styles.cardLeft}>
          {item.photo || item.avatar ? (
            <Image source={{ uri: item.photo || item.avatar }} style={styles.childAvatar} />
          ) : (
            <View style={[styles.avatarPlaceholder, { backgroundColor: isSelected ? colors.primary : colors.primaryLight }]}>
              <Text style={styles.placeholderText}>
                {item.first_name ? item.first_name[0].toUpperCase() : 'S'}
              </Text>
            </View>
          )}

          <View style={styles.infoCol}>
            <Text style={[styles.childName, { color: colors.text }]}>
              {displayName}
            </Text>
            <Text style={[styles.childClass, { color: colors.textSecondary }]}>
              {className} {sectionName}
            </Text>
            <Text style={[styles.admNumber, { color: colors.textMuted }]}>
              Adm #{item.admission_number}
            </Text>
          </View>
        </View>

        <View
          style={[
            styles.radioCircle,
            {
              borderColor: isSelected ? colors.primary : colors.border,
              backgroundColor: isSelected ? colors.primary : 'transparent',
            },
          ]}
        >
          {isSelected && <View style={styles.radioInner} />}
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <Modal
      visible={visible}
      transparent
      animationType="slide"
      onRequestClose={onClose}
    >
      <TouchableWithoutFeedback onPress={onClose}>
        <View style={styles.overlay}>
          <TouchableWithoutFeedback>
            <View
              style={[
                styles.modalContent,
                { backgroundColor: colors.surface },
              ]}
            >
              <View style={styles.header}>
                <View style={[styles.dragHandle, { backgroundColor: colors.border }]} />
                <Text style={[styles.modalTitle, { color: colors.text }]}>
                  Select Student
                </Text>
                <Text style={[styles.modalSubtitle, { color: colors.textSecondary }]}>
                  Choose which child's academic records you want to view
                </Text>
              </View>

              <FlatList
                data={childrenList}
                keyExtractor={(item) => item.id.toString()}
                renderItem={renderChildItem}
                contentContainerStyle={styles.listContent}
              />
            </View>
          </TouchableWithoutFeedback>
        </View>
      </TouchableWithoutFeedback>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    borderTopLeftRadius: BorderRadius.xxl,
    borderTopRightRadius: BorderRadius.xxl,
    paddingTop: Spacing.md,
    paddingBottom: Spacing.xxxl,
    maxHeight: '80%',
    ...Shadows.card,
  },
  dragHandle: {
    width: 40,
    height: 4,
    borderRadius: 2,
    alignSelf: 'center',
    marginBottom: Spacing.md,
  },
  header: {
    paddingHorizontal: Spacing.xl,
    marginBottom: Spacing.md,
  },
  modalTitle: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  modalSubtitle: {
    fontSize: Typography.size.sm,
    marginTop: 2,
  },
  listContent: {
    paddingHorizontal: Spacing.xl,
    paddingBottom: Spacing.lg,
  },
  childCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: Spacing.md,
    borderRadius: BorderRadius.lg,
    borderWidth: 1.5,
    marginVertical: Spacing.xs + 2,
  },
  cardLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  childAvatar: {
    width: 48,
    height: 48,
    borderRadius: 24,
  },
  avatarPlaceholder: {
    width: 48,
    height: 48,
    borderRadius: 24,
    alignItems: 'center',
    justifyContent: 'center',
  },
  placeholderText: {
    color: '#ffffff',
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  infoCol: {
    marginLeft: Spacing.md,
    flex: 1,
  },
  childName: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  childClass: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  admNumber: {
    fontSize: Typography.size.xs,
    marginTop: 1,
  },
  radioCircle: {
    width: 22,
    height: 22,
    borderRadius: 11,
    borderWidth: 2,
    alignItems: 'center',
    justifyContent: 'center',
    marginLeft: Spacing.sm,
  },
  radioInner: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#ffffff',
  },
});
