import { Component, OnInit, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { ApiService } from '../core/api.service';
import { AuthService } from '../core/auth.service';
import { AppUser } from '../core/models';
import { downloadBlob } from '../core/download.util';

@Component({
  selector: 'app-users-list',
  standalone: true,
  imports: [MatTableModule, MatButtonModule, MatIconModule, RouterLink],
  templateUrl: './users-list.component.html',
})
export class UsersListComponent implements OnInit {
  private api = inject(ApiService);
  readonly auth = inject(AuthService);
  private router = inject(Router);

  displayedColumns = ['code', 'username', 'name', 'created_at', 'actions'];
  users: AppUser[] = [];

  ngOnInit(): void {
    this.api.getUsers().subscribe((res) => (this.users = res.data));
  }

  export(format: 'pdf' | 'excel'): void {
    this.api.exportUsers(format).subscribe((blob) =>
      downloadBlob(blob, `usuarios.${format === 'pdf' ? 'pdf' : 'xlsx'}`),
    );
  }

  detail(id: string): void {
    this.router.navigate(['/usuarios', id]);
  }

  edit(id: string): void {
    this.router.navigate(['/usuarios', id, 'editar']);
  }

  remove(id: string): void {
    if (!confirm('¿Eliminar usuario?')) {
      return;
    }
    this.api.deleteUser(id).subscribe(() => {
      this.api.getUsers().subscribe((res) => (this.users = res.data));
    });
  }
}
