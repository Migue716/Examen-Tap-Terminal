import { Component, inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { Product } from '../core/models';

@Component({
  selector: 'app-product-view-dialog',
  standalone: true,
  imports: [MatDialogModule, MatButtonModule],
  template: `
    <h2 mat-dialog-title>Detalle de producto</h2>
    <mat-dialog-content>
      <p><strong>Código:</strong> {{ data.code }}</p>
      <p><strong>Nombre:</strong> {{ data.name }}</p>
      <p><strong>Marca:</strong> {{ data.brand }}</p>
      <p><strong>Precio:</strong> {{ data.price }}</p>
      <p><strong>Fecha:</strong> {{ data.created_at }}</p>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cerrar</button>
    </mat-dialog-actions>
  `,
})
export class ProductViewDialogComponent {
  data = inject<Product>(MAT_DIALOG_DATA);
}
