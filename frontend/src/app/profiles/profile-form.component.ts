import { Component, OnInit, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatSelectModule } from '@angular/material/select';
import { ApiService } from '../core/api.service';
import { Section } from '../core/models';

@Component({
  selector: 'app-profile-form',
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
  templateUrl: './profile-form.component.html',
})
export class ProfileFormComponent implements OnInit {
  private fb = inject(FormBuilder);
  private api = inject(ApiService);
  private route = inject(ActivatedRoute);
  private router = inject(Router);

  id: string | null = null;
  sections: Section[] = [];

  form = this.fb.group({
    name: ['', Validators.required],
    section_ids: [[] as string[], Validators.required],
  });

  ngOnInit(): void {
    this.api.getSections().subscribe((res) => (this.sections = res.data));
    this.id = this.route.snapshot.paramMap.get('id');
    if (this.id) {
      this.api.getProfile(this.id).subscribe((res) => {
        this.form.patchValue({
          name: res.data.name,
          section_ids: (res.data.sections ?? []).map((s) => s.id),
        });
      });
    }
  }

  submit(): void {
    if (this.form.invalid) {
      return;
    }
    this.api.saveProfile(this.form.getRawValue(), this.id ?? undefined).subscribe(() => {
      this.router.navigate(['/perfiles']);
    });
  }
}
