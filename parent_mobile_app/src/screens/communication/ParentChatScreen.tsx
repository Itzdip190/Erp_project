import React, { useEffect, useState } from 'react';
import {
  FlatList,
  KeyboardAvoidingView,
  Platform,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { ChatApi, ChatMessage } from '../../api/chatApi';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';
import { formatTime } from '../../utils/formatters';

type Props = NativeStackScreenProps<ParentStackParamList, 'ParentChat'>;

export const ParentChatScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { user } = useAuth();
  const { activeChild } = useActiveChild();

  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [inputText, setInputText] = useState('');
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);

  const fetchMessages = async () => {
    if (!activeChild) return;
    try {
      const data = await ChatApi.getMessages(activeChild.id);
      setMessages(data);
    } catch (e) {
      console.warn('Error loading chat messages:', e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchMessages();
  }, [activeChild?.id]);

  const handleSend = async () => {
    if (!inputText.trim() || !activeChild) return;

    const messageText = inputText.trim();
    setInputText('');
    setSending(true);

    // Optimistic UI update
    const tempMsg: ChatMessage = {
      id: Date.now(),
      sender_id: user?.id || 1,
      sender_type: 'parent',
      sender_name: user?.name || 'You',
      message: messageText,
      created_at: new Date().toISOString(),
      is_mine: true,
    };
    setMessages((prev) => [...prev, tempMsg]);

    try {
      await ChatApi.sendMessage(activeChild.id, messageText);
    } catch (e) {
      console.warn('Send message failed on server:', e);
    } finally {
      setSending(false);
    }
  };

  const renderMessageBubble = ({ item }: { item: ChatMessage }) => {
    const isMine = item.is_mine || item.sender_type === 'parent';

    return (
      <View style={[styles.bubbleContainer, isMine ? styles.myBubbleRow : styles.otherBubbleRow]}>
        <View
          style={[
            styles.bubble,
            {
              backgroundColor: isMine ? colors.primary : colors.surface,
              borderColor: isMine ? colors.primary : colors.borderSubtle,
            },
          ]}
        >
          {!isMine && (
            <Text style={[styles.senderName, { color: colors.primary }]}>
              {item.sender_name} ({item.sender_type})
            </Text>
          )}

          <Text style={[styles.bubbleText, { color: isMine ? '#ffffff' : colors.text }]}>
            {item.message}
          </Text>

          <Text
            style={[
              styles.timeText,
              { color: isMine ? 'rgba(255,255,255,0.7)' : colors.textMuted },
            ]}
          >
            {formatTime(item.created_at)}
          </Text>
        </View>
      </View>
    );
  };

  return (
    <ScreenWrapper>
      <View style={styles.topNav}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>School Inquiries</Text>
        <View style={{ width: 40 }} />
      </View>

      <ChildSwitcherBar />

      <KeyboardAvoidingView
        style={styles.chatContainer}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 0}
      >
        {loading ? (
          <LoadingIndicator message="Loading conversation history..." />
        ) : messages.length === 0 ? (
          <EmptyState
            title="Start a Conversation"
            description="Have a question for your child's class teacher or school administration? Type a message below."
          />
        ) : (
          <FlatList
            data={messages}
            keyExtractor={(item) => item.id.toString()}
            renderItem={renderMessageBubble}
            contentContainerStyle={styles.messagesList}
          />
        )}

        {/* Message Input Box */}
        <View style={[styles.inputBar, { backgroundColor: colors.surface, borderTopColor: colors.borderSubtle }]}>
          <TextInput
            style={[styles.textInput, { backgroundColor: colors.surfaceSubtle, color: colors.text }]}
            placeholder="Type your message to school..."
            placeholderTextColor={colors.textMuted}
            value={inputText}
            onChangeText={setInputText}
            multiline
          />

          <TouchableOpacity
            style={[styles.sendButton, { backgroundColor: colors.primary, opacity: inputText.trim() ? 1 : 0.6 }]}
            onPress={handleSend}
            disabled={!inputText.trim() || sending}
          >
            <Text style={styles.sendText}>➤</Text>
          </TouchableOpacity>
        </View>
      </KeyboardAvoidingView>
    </ScreenWrapper>
  );
};

const styles = StyleSheet.create({
  topNav: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: Spacing.xl,
    paddingVertical: Spacing.md,
  },
  backText: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.semibold,
  },
  headerTitle: {
    fontSize: Typography.size.lg,
    fontWeight: Typography.weight.bold,
  },
  chatContainer: {
    flex: 1,
  },
  messagesList: {
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.md,
  },
  bubbleContainer: {
    marginVertical: 4,
    flexDirection: 'row',
  },
  myBubbleRow: {
    justifyContent: 'flex-end',
  },
  otherBubbleRow: {
    justifyContent: 'flex-start',
  },
  bubble: {
    maxWidth: '80%',
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.sm,
    borderRadius: BorderRadius.lg,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  senderName: {
    fontSize: 10,
    fontWeight: 'bold',
    marginBottom: 2,
    textTransform: 'capitalize',
  },
  bubbleText: {
    fontSize: Typography.size.sm,
    lineHeight: 19,
  },
  timeText: {
    fontSize: 9,
    alignSelf: 'flex-end',
    marginTop: 4,
  },
  inputBar: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.sm,
    borderTopWidth: 1,
  },
  textInput: {
    flex: 1,
    borderRadius: BorderRadius.xl,
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.sm,
    maxHeight: 100,
    fontSize: Typography.size.sm,
  },
  sendButton: {
    width: 44,
    height: 44,
    borderRadius: 22,
    alignItems: 'center',
    justifyContent: 'center',
    marginLeft: Spacing.sm,
  },
  sendText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
