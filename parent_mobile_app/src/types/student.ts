export interface StudentClass {
  id: number;
  name: string;
}

export interface StudentSection {
  id: number;
  name: string;
}

export interface AcademicSession {
  id: number;
  name: string;
  is_current?: boolean;
}

export interface StudentChild {
  id: number;
  school_id: number;
  admission_number: string;
  roll_number?: string;
  first_name: string;
  last_name?: string;
  full_name?: string;
  gender?: string;
  dob?: string;
  blood_group?: string;
  avatar?: string;
  photo?: string;
  class_id?: number;
  section_id?: number;
  class?: StudentClass;
  section?: StudentSection;
  academicSession?: AcademicSession;
  father_name?: string;
  father_phone?: string;
  father_occupation?: string;
  mother_name?: string;
  mother_phone?: string;
  guardian_name?: string;
  guardian_phone?: string;
  guardian_email?: string;
  current_address?: string;
  transport_route?: string;
  bus_number?: string;
  is_active: boolean;
}

export interface StudentDocumentItem {
  id: number;
  document_type: string;
  original_name: string;
  file_url: string;
  created_at: string;
}
