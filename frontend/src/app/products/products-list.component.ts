import { Component, OnInit, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { ApiService } from '../core/api.service';
import { AuthService } from '../core/auth.service';
import { Product } from '../core/models';
import { downloadBlob } from '../core/download.util';
import { ProductViewDialogComponent } from './product-view-dialog.component';

@Component({
  selector: 'app-products-list',
  standalone: true,
  imports: [MatTableModule, MatButtonModule, MatIconModule, MatDialogModule, RouterLink],
  templateUrl: './products-list.component.html',
})
export class ProductsListComponent implements OnInit {
  private api = inject(ApiService);
  readonly auth = inject(AuthService);
  private dialog = inject(MatDialog);
  private router = inject(Router);

  displayedColumns = ['code', 'name', 'price', 'created_at', 'actions'];
  products: Product[] = [];

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.api.getProducts().subscribe((res) => (this.products = res.data));
  }

  export(format: 'pdf' | 'excel'): void {
    this.api.exportProducts(format).subscribe((blob) =>
      downloadBlob(blob, `productos.${format === 'pdf' ? 'pdf' : 'xlsx'}`),
    );
  }

  view(product: Product): void {
    this.dialog.open(ProductViewDialogComponent, { data: product, width: '420px' });
  }

  edit(id: string): void {
    this.router.navigate(['/productos', id, 'editar']);
  }

  remove(id: string): void {
    if (!confirm('¿Eliminar producto?')) {
      return;
    }
    this.api.deleteProduct(id).subscribe(() => this.load());
  }
}
