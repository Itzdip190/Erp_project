export interface RouteStop {
  id: number;
  stop_name: string;
  expected_time: string;
  is_completed: boolean;
  latitude?: number;
  longitude?: number;
}

export interface BusTrackingInfo {
  bus_number: string;
  vehicle_number: string;
  route_name: string;
  driver_name: string;
  driver_phone: string;
  current_status: 'stopped' | 'in_transit' | 'reached_school' | 'completed';
  current_location?: {
    latitude: number;
    longitude: number;
    speed: number;
    last_updated: string;
  };
  stops: RouteStop[];
  next_stop?: string;
  eta_minutes?: number;
}
