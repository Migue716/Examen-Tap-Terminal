import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { AppUser, Product, Profile, Section } from './models';

@Injectable({ providedIn: 'root' })
export class ApiService {
  constructor(private http: HttpClient) {}

  getProducts() {
    return this.http.get<{ data: Product[] }>(`${environment.apiUrl}/products`);
  }

  getProduct(id: string) {
    return this.http.get<{ data: Product }>(`${environment.apiUrl}/products/${id}`);
  }

  saveProduct(payload: Partial<Product>, id?: string) {
    if (id) {
      return this.http.put<{ data: Product }>(`${environment.apiUrl}/products/${id}`, payload);
    }
    return this.http.post<{ data: Product }>(`${environment.apiUrl}/products`, payload);
  }

  deleteProduct(id: string) {
    return this.http.delete(`${environment.apiUrl}/products/${id}`);
  }

  exportProducts(format: 'pdf' | 'excel') {
    return this.http.get(`${environment.apiUrl}/products-export/${format}`, {
      responseType: 'blob',
    });
  }

  getUsers() {
    return this.http.get<{ data: AppUser[] }>(`${environment.apiUrl}/users`);
  }

  getUser(id: string) {
    return this.http.get<{ data: AppUser }>(`${environment.apiUrl}/users/${id}`);
  }

  saveUser(payload: Record<string, unknown>, id?: string) {
    if (id) {
      return this.http.put<{ data: AppUser }>(`${environment.apiUrl}/users/${id}`, payload);
    }
    return this.http.post<{ data: AppUser }>(`${environment.apiUrl}/users`, payload);
  }

  deleteUser(id: string) {
    return this.http.delete(`${environment.apiUrl}/users/${id}`);
  }

  exportUsers(format: 'pdf' | 'excel') {
    return this.http.get(`${environment.apiUrl}/users-export/${format}`, { responseType: 'blob' });
  }

  getProfiles() {
    return this.http.get<{ data: Profile[] }>(`${environment.apiUrl}/profiles`);
  }

  getProfile(id: string) {
    return this.http.get<{ data: Profile }>(`${environment.apiUrl}/profiles/${id}`);
  }

  saveProfile(payload: Record<string, unknown>, id?: string) {
    if (id) {
      return this.http.put<{ data: Profile }>(`${environment.apiUrl}/profiles/${id}`, payload);
    }
    return this.http.post<{ data: Profile }>(`${environment.apiUrl}/profiles`, payload);
  }

  deleteProfile(id: string) {
    return this.http.delete(`${environment.apiUrl}/profiles/${id}`);
  }

  exportProfiles(format: 'pdf' | 'excel') {
    return this.http.get(`${environment.apiUrl}/profiles-export/${format}`, {
      responseType: 'blob',
    });
  }

  getSections() {
    return this.http.get<{ data: Section[] }>(`${environment.apiUrl}/sections`);
  }
}
