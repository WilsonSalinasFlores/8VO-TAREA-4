import { Component, inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { CatalogoService } from '../../../core/services/catalogo.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-recuperar',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './recuperar.component.html',
  styleUrls: []
})
export class RecuperarComponent implements OnInit {
  private fb = inject(FormBuilder);
  private authService = inject(AuthService);
  private catalogoService = inject(CatalogoService);
  private router = inject(Router);

  recuperarForm!: FormGroup;
  preguntas: any[] = [];
  mensaje: string = '';
  error: string = '';

  ngOnInit() {
    this.initForm();
    this.cargarPreguntas();
  }

  initForm() {
    this.recuperarForm = this.fb.group({
      cedula: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      respuestas: this.fb.array([])
    });
  }

  get respuestasFormArray() {
    return this.recuperarForm.get('respuestas') as FormArray;
  }

  cargarPreguntas() {
    this.catalogoService.obtenerPreguntas().subscribe({
      next: (res) => {
        if (res.exito) {
          this.preguntas = res.data;
          this.addRespuesta();
          this.addRespuesta();
          this.addRespuesta();
        }
      }
    });
  }

  addRespuesta() {
    this.respuestasFormArray.push(this.fb.group({
      id_pregunta: ['', Validators.required],
      respuesta: ['', Validators.required]
    }));
  }

  onSubmit() {
    if (this.recuperarForm.valid) {
      const selectedIds = this.respuestasFormArray.value.map((r: any) => r.id_pregunta);
      const uniqueIds = new Set(selectedIds);
      if (uniqueIds.size !== 3) {
        this.error = 'Debe seleccionar 3 preguntas diferentes.';
        return;
      }
      this.error = '';
      this.authService.recuperarPassword(this.recuperarForm.value).subscribe({
        next: (res) => {
          if (res.exito) {
            this.mensaje = 'Contraseña restablecida correctamente.';
            setTimeout(() => this.router.navigate(['/auth/login']), 2000);
          } else {
            this.error = res.mensaje || 'Error al recuperar la contraseña.';
          }
        },
        error: (err) => {
          this.error = 'Error verificando las respuestas.';
        }
      });
    } else {
      this.recuperarForm.markAllAsTouched();
    }
  }
}
