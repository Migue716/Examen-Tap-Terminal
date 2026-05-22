import { Injectable, signal } from '@angular/core';

const STORAGE_KEY = 'tt-theme';

@Injectable({ providedIn: 'root' })
export class ThemeService {
  readonly isDark = signal(this.readStored());

  constructor() {
    this.apply(this.isDark());
  }

  toggle(): void {
    const next = !this.isDark();
    this.isDark.set(next);
    this.apply(next);
    localStorage.setItem(STORAGE_KEY, next ? 'dark' : 'light');
  }

  private readStored(): boolean {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved === 'dark') {
      return true;
    }
    if (saved === 'light') {
      return false;
    }
    return true;
  }

  private apply(dark: boolean): void {
    document.documentElement.classList.toggle('dark-theme', dark);
    document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
  }
}
