import { Component, OnInit, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatDialog } from '@angular/material/dialog';
import { ApiService } from '../core/api.service';
import { AuthService } from '../core/auth.service';
import { Profile } from '../core/models';
import { downloadBlob } from '../core/download.util';
import { ProfileDetailDialogComponent } from './profile-detail-dialog.component';

@Component({
  selector: 'app-profiles-list',
  standalone: true,
  imports: [MatTableModule, MatButtonModule, MatIconModule, MatCardModule, MatTooltipModule, RouterLink],
  templateUrl: './profiles-list.component.html',
})
export class ProfilesListComponent implements OnInit {
  private api = inject(ApiService);
  readonly auth = inject(AuthService);
  private dialog = inject(MatDialog);
  private router = inject(Router);

  displayedColumns = ['code', 'name', 'created_at', 'actions'];
  profiles: Profile[] = [];

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.api.getProfiles().subscribe((res) => (this.profiles = res.data));
  }

  export(format: 'pdf' | 'excel'): void {
    this.api.exportProfiles(format).subscribe((blob) =>
      downloadBlob(blob, `perfiles.${format === 'pdf' ? 'pdf' : 'xlsx'}`),
    );
  }

  detail(id: string): void {
    this.api.getProfile(id).subscribe((res) => {
      this.dialog.open(ProfileDetailDialogComponent, { data: res.data, width: '480px' });
    });
  }

  edit(id: string): void {
    this.router.navigate(['/perfiles', id, 'editar']);
  }

  remove(id: string): void {
    if (!confirm('¿Eliminar perfil?')) {
      return;
    }
    this.api.deleteProfile(id).subscribe(() => this.load());
  }
}
