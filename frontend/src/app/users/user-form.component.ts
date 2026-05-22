import { Component, OnInit, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatSelectModule } from '@angular/material/select';
import { ApiService } from '../core/api.service';
import { Profile } from '../core/models';

@Component({
  selector: 'app-user-form',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    RouterLink,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatSelectModule,
  ],
  templateUrl: './user-form.component.html',
})
export class UserFormComponent implements OnInit {
  private fb = inject(FormBuilder);
  private api = inject(ApiService);
  private route = inject(ActivatedRoute);
  private router = inject(Router);

  id: string | null = null;
  profiles: Profile[] = [];

  form = this.fb.group({
    name: ['', Validators.required],
    username: ['', [Validators.required, Validators.email]],
    phone: [''],
    profile_photo: ['', Validators.required],
    profile_ids: [[] as string[]],
    password: [''],
  });

  ngOnInit(): void {
    this.api.getProfiles().subscribe((res) => (this.profiles = res.data));
    this.id = this.route.snapshot.paramMap.get('id');
    if (this.id) {
      this.api.getUser(this.id).subscribe((res) => {
        const u = res.data;
        this.form.patchValue({
          name: u.name,
          username: u.username,
          phone: u.phone ?? '',
          profile_photo: u.profile_photo ?? '',
          profile_ids: (u.profiles ?? []).map((p) => p.id),
        });
      });
    }
  }

  onPhotoSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) {
      return;
    }
    const reader = new FileReader();
    reader.onload = () => this.form.patchValue({ profile_photo: reader.result as string });
    reader.readAsDataURL(file);
  }

  submit(): void {
    if (this.form.invalid) {
      return;
    }
    this.api.saveUser(this.form.getRawValue(), this.id ?? undefined).subscribe(() => {
      this.router.navigate(['/usuarios']);
    });
  }
}
