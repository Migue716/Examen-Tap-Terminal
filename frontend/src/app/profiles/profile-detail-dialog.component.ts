import { Component, inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { MatListModule } from '@angular/material/list';
import { Profile } from '../core/models';

@Component({
  selector: 'app-profile-detail-dialog',
  standalone: true,
  imports: [MatDialogModule, MatButtonModule, MatListModule],
  template: `
    <h2 mat-dialog-title>Detalle del perfil</h2>
    <mat-dialog-content>
      <p><strong>Código:</strong> {{ data.code }}</p>
      <p><strong>Nombre:</strong> {{ data.name }}</p>
      <p><strong>Fecha creación:</strong> {{ data.created_at }}</p>
      <h3>Secciones relacionadas</h3>
      <mat-list>
        @for (section of data.sections ?? []; track section.id) {
          <mat-list-item>{{ section.code }} — {{ section.name }}</mat-list-item>
        }
      </mat-list>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cerrar</button>
    </mat-dialog-actions>
  `,
})
export class ProfileDetailDialogComponent {
  data = inject<Profile>(MAT_DIALOG_DATA);
}
