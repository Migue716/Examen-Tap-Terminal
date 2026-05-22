import { Routes } from '@angular/router';
import { authGuard, sectionGuard } from './core/auth.guard';
import { ShellComponent } from './layout/shell.component';
import { LoginComponent } from './auth/login.component';
import { ForgotPasswordComponent } from './auth/forgot-password.component';
import { ProductsListComponent } from './products/products-list.component';
import { ProductFormComponent } from './products/product-form.component';
import { UsersListComponent } from './users/users-list.component';
import { UserFormComponent } from './users/user-form.component';
import { UserDetailComponent } from './users/user-detail.component';
import { ProfilesListComponent } from './profiles/profiles-list.component';
import { ProfileFormComponent } from './profiles/profile-form.component';

export const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'recuperar', component: ForgotPasswordComponent },
  {
    path: '',
    component: ShellComponent,
    canActivate: [authGuard],
    children: [
      { path: '', pathMatch: 'full', redirectTo: 'productos' },
      {
        path: 'productos',
        canActivate: [sectionGuard('productos')],
        children: [
          { path: '', component: ProductsListComponent },
          { path: 'nuevo', component: ProductFormComponent },
          { path: ':id/editar', component: ProductFormComponent },
        ],
      },
      {
        path: 'usuarios',
        canActivate: [sectionGuard('usuarios')],
        children: [
          { path: '', component: UsersListComponent },
          { path: 'nuevo', component: UserFormComponent },
          { path: ':id/editar', component: UserFormComponent },
          { path: ':id', component: UserDetailComponent },
        ],
      },
      {
        path: 'perfiles',
        canActivate: [sectionGuard('perfiles')],
        children: [
          { path: '', component: ProfilesListComponent },
          { path: 'nuevo', component: ProfileFormComponent },
          { path: ':id/editar', component: ProfileFormComponent },
        ],
      },
    ],
  },
  { path: '**', redirectTo: '' },
];
