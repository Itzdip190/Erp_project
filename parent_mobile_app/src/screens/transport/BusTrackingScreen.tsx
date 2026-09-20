import React, { useEffect, useState } from 'react';
import {
  Linking,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { TransportApi } from '../../api/transportApi';
import { CustomButton } from '../../components/common/CustomButton';
import { EmptyState } from '../../components/common/EmptyState';
import { LoadingIndicator } from '../../components/common/LoadingIndicator';
import { ScreenWrapper } from '../../components/common/ScreenWrapper';
import { ChildSwitcherBar } from '../../components/parent/ChildSwitcherBar';
import { BorderRadius, Shadows, Spacing, Typography } from '../../config/theme';
import { useActiveChild } from '../../context/ActiveChildContext';
import { useTheme } from '../../context/ThemeContext';
import { ParentStackParamList } from '../../types/navigation';
import { BusTrackingInfo, RouteStop } from '../../types/transport';

type Props = NativeStackScreenProps<ParentStackParamList, 'BusTracking'>;

export const BusTrackingScreen: React.FC<Props> = ({ navigation }) => {
  const { colors } = useTheme();
  const { activeChild } = useActiveChild();

  const [tracking, setTracking] = useState<BusTrackingInfo | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchTracking = async () => {
    if (!activeChild) return;
    try {
      const data = await TransportApi.getLiveTracking(activeChild.id);
      setTracking(data);
    } catch (e) {
      console.warn('Error fetching transport info:', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    setLoading(true);
    fetchTracking();
  }, [activeChild?.id]);

  const handleCallDriver = (phone: string) => {
    if (phone) {
      Linking.openURL(`tel:${phone}`).catch((err) => console.warn('Dialer error:', err));
    }
  };

  return (
    <ScreenWrapper>
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => {
              setRefreshing(true);
              fetchTracking();
            }}
            tintColor={colors.primary}
          />
        }
      >
        <View style={styles.topNav}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={[styles.backText, { color: colors.primary }]}>← Back</Text>
          </TouchableOpacity>
          <Text style={[styles.headerTitle, { color: colors.text }]}>Live Bus Tracking</Text>
          <View style={{ width: 40 }} />
        </View>

        <ChildSwitcherBar />

        {loading ? (
          <LoadingIndicator message="Connecting to vehicle GPS stream..." />
        ) : !tracking ? (
          <EmptyState
            title="Transport Not Assigned"
            description="School transport facility is not currently assigned to this student profile."
          />
        ) : (
          <>
            {/* Live Status Header Card */}
            <View
              style={[
                styles.statusCard,
                {
                  backgroundColor: colors.surface,
                  borderColor: colors.borderSubtle,
                },
              ]}
            >
              <View style={styles.busInfoRow}>
                <View style={[styles.busIconBox, { backgroundColor: colors.accent + '20' }]}>
                  <Text style={{ fontSize: 24 }}>🚌</Text>
                </View>
                <View style={{ flex: 1, marginLeft: Spacing.md }}>
                  <Text style={[styles.routeTitle, { color: colors.text }]}>
                    {tracking.route_name || 'School Bus Route'}
                  </Text>
                  <Text style={[styles.busNumber, { color: colors.textSecondary }]}>
                    Bus #{tracking.bus_number} • Plate: {tracking.vehicle_number}
                  </Text>
                </View>

                <View style={[styles.liveBadge, { backgroundColor: colors.successLight }]}>
                  <View style={[styles.liveDot, { backgroundColor: colors.success }]} />
                  <Text style={[styles.liveText, { color: colors.success }]}>LIVE</Text>
                </View>
              </View>

              <View style={[styles.etaBox, { backgroundColor: colors.surfaceSubtle }]}>
                <View>
                  <Text style={[styles.etaLabel, { color: colors.textMuted }]}>Next Stop</Text>
                  <Text style={[styles.nextStopName, { color: colors.text }]}>
                    {tracking.next_stop || 'School Campus'}
                  </Text>
                </View>

                <View style={{ alignItems: 'flex-end' }}>
                  <Text style={[styles.etaLabel, { color: colors.textMuted }]}>Estimated Time</Text>
                  <Text style={[styles.etaVal, { color: colors.primary }]}>
                    {tracking.eta_minutes ? `${tracking.eta_minutes} mins` : 'On Route'}
                  </Text>
                </View>
              </View>

              {/* Driver Contact Box */}
              <View style={[styles.driverBox, { borderTopColor: colors.borderSubtle }]}>
                <View>
                  <Text style={[styles.driverLabel, { color: colors.textMuted }]}>Bus Driver</Text>
                  <Text style={[styles.driverName, { color: colors.text }]}>
                    {tracking.driver_name}
                  </Text>
                </View>

                {tracking.driver_phone ? (
                  <TouchableOpacity
                    style={[styles.callBtn, { backgroundColor: colors.success }]}
                    onPress={() => handleCallDriver(tracking.driver_phone)}
                  >
                    <Text style={styles.callBtnText}>📞 Call Driver</Text>
                  </TouchableOpacity>
                ) : null}
              </View>
            </View>

            {/* Route Stops Timeline */}
            <View style={styles.sectionHeader}>
              <Text style={[styles.sectionTitle, { color: colors.text }]}>Route Stops Timeline</Text>
            </View>

            <View style={[styles.timelineCard, { backgroundColor: colors.surface, borderColor: colors.borderSubtle }]}>
              {tracking.stops && tracking.stops.length > 0 ? (
                tracking.stops.map((stop: RouteStop, index: number) => {
                  const isLast = index === tracking.stops.length - 1;
                  return (
                    <View key={stop.id || index} style={styles.stopItem}>
                      <View style={styles.timelineLeft}>
                        <View
                          style={[
                            styles.dot,
                            {
                              backgroundColor: stop.is_completed ? colors.success : colors.border,
                              borderColor: stop.is_completed ? colors.success : colors.textMuted,
                            },
                          ]}
                        />
                        {!isLast && (
                          <View
                            style={[
                              styles.line,
                              {
                                backgroundColor: stop.is_completed ? colors.success : colors.border,
                              },
                            ]}
                          />
                        )}
                      </View>

                      <View style={styles.stopDetails}>
                        <Text
                          style={[
                            styles.stopName,
                            {
                              color: stop.is_completed ? colors.textSecondary : colors.text,
                              textDecorationLine: stop.is_completed ? 'line-through' : 'none',
                            },
                          ]}
                        >
                          {stop.stop_name}
                        </Text>
                        <Text style={[styles.stopTime, { color: colors.textMuted }]}>
                          Scheduled: {stop.expected_time}
                        </Text>
                      </View>
                    </View>
                  );
                })
              ) : (
                <Text style={[styles.noStops, { color: colors.textMuted }]}>
                  Stops information will be updated as the trip starts.
                </Text>
              )}
            </View>
          </>
        )}
      </ScrollView>
    </ScreenWrapper>
  );
};

const styles = StyleSheet.create({
  scrollContent: {
    paddingBottom: Spacing.xxxl,
  },
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
  statusCard: {
    marginHorizontal: Spacing.lg,
    borderRadius: BorderRadius.xl,
    padding: Spacing.xl,
    borderWidth: 1,
    ...Shadows.card,
  },
  busInfoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: Spacing.md,
  },
  busIconBox: {
    width: 48,
    height: 48,
    borderRadius: BorderRadius.lg,
    alignItems: 'center',
    justifyContent: 'center',
  },
  routeTitle: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  busNumber: {
    fontSize: Typography.size.xs,
    marginTop: 2,
  },
  liveBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: Spacing.sm + 2,
    paddingVertical: 3,
    borderRadius: BorderRadius.full,
  },
  liveDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    marginRight: 4,
  },
  liveText: {
    fontSize: 10,
    fontWeight: Typography.weight.bold,
    letterSpacing: 0.5,
  },
  etaBox: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: Spacing.md,
    borderRadius: BorderRadius.lg,
    marginBottom: Spacing.md,
  },
  etaLabel: {
    fontSize: 10,
    textTransform: 'uppercase',
  },
  nextStopName: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
    marginTop: 2,
  },
  etaVal: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
    marginTop: 2,
  },
  driverBox: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    paddingTop: Spacing.md,
  },
  driverLabel: {
    fontSize: 10,
  },
  driverName: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.bold,
    marginTop: 2,
  },
  callBtn: {
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.xs + 2,
    borderRadius: BorderRadius.md,
  },
  callBtnText: {
    color: '#ffffff',
    fontSize: Typography.size.xs,
    fontWeight: Typography.weight.bold,
  },
  sectionHeader: {
    paddingHorizontal: Spacing.xl,
    marginTop: Spacing.xl,
    marginBottom: Spacing.xs,
  },
  sectionTitle: {
    fontSize: Typography.size.md,
    fontWeight: Typography.weight.bold,
  },
  timelineCard: {
    marginHorizontal: Spacing.lg,
    borderRadius: BorderRadius.xl,
    padding: Spacing.lg,
    borderWidth: 1,
    ...Shadows.subtle,
  },
  stopItem: {
    flexDirection: 'row',
    minHeight: 52,
  },
  timelineLeft: {
    alignItems: 'center',
    width: 24,
  },
  dot: {
    width: 14,
    height: 14,
    borderRadius: 7,
    borderWidth: 2,
  },
  line: {
    width: 2,
    flex: 1,
    marginVertical: 2,
  },
  stopDetails: {
    flex: 1,
    marginLeft: Spacing.md,
  },
  stopName: {
    fontSize: Typography.size.sm,
    fontWeight: Typography.weight.semibold,
  },
  stopTime: {
    fontSize: 10,
    marginTop: 2,
  },
  noStops: {
    fontSize: Typography.size.xs,
    textAlign: 'center',
    padding: Spacing.md,
  },
});
