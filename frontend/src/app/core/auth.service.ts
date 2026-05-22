import { Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { tap } from 'rxjs/operators';
import { environment } from '../../environments/environment';
import { AuthUser } from './models';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly tokenKey = 'tap_token';
  readonly user = signal<AuthUser | null>(null);

  constructor(
    private http: HttpClient,
    private router: Router,
  ) {
    const token = localStorage.getItem(this.tokenKey);
    if (token) {
      this.loadMe().subscribe({ error: () => this.clearSession() });
    }
  }

  login(username: string, password: string) {
    return this.http
      .post<{ token: string; user: AuthUser }>(`${environment.apiUrl}/auth/login`, {
        username,
        password,
      })
      .pipe(
        tap((res) => {
          localStorage.setItem(this.tokenKey, res.token);
          this.user.set(res.user);
        }),
      );
  }

  forgotPassword(username: string) {
    return this.http.post<{ message: string }>(`${environment.apiUrl}/auth/forgot-password`, {
      username,
    });
  }

  loadMe() {
    return this.http.get<{ user: AuthUser }>(`${environment.apiUrl}/auth/me`).pipe(
      tap((res) => this.user.set(res.user)),
    );
  }

  logout() {
    this.http.post(`${environment.apiUrl}/auth/logout`, {}).subscribe({
      complete: () => this.clearSession(),
      error: () => this.clearSession(),
    });
  }

  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  canRead(module: string): boolean {
    const u = this.user();
    return !!u && (u.is_admin || u.sections.includes(module));
  }

  canWrite(module: string): boolean {
    const u = this.user();
    return !!u && (u.is_admin || u.write_sections.includes(module));
  }

  private clearSession(): void {
    localStorage.removeItem(this.tokenKey);
    this.user.set(null);
    this.router.navigate(['/login']);
  }
}
