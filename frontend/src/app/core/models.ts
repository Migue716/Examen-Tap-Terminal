export interface AuthUser {
  id: string;
  code: string;
  name: string;
  username: string;
  profile_photo: string;
  sections: string[];
  write_sections: string[];
  is_admin: boolean;
}

export interface Product {
  id: string;
  code: string;
  name: string;
  brand: string;
  price: number;
  created_at: string;
}

export interface AppUser {
  id: string;
  code: string;
  username: string;
  name: string;
  phone?: string;
  profile_photo?: string;
  profiles?: Profile[];
  created_at: string;
}

export interface Profile {
  id: string;
  code: string;
  name: string;
  sections?: Section[];
  created_at: string;
}

export interface Section {
  id: string;
  code: string;
  name: string;
  module: string;
  can_write: boolean;
}
