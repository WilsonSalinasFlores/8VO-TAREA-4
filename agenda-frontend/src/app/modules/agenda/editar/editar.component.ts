import { Component, inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { ContactoService } from '../../../core/services/contacto.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-editar',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './editar.component.html',
  styleUrls: []
})
export class EditarComponent implements OnInit {
  private fb = inject(FormBuilder);
  private contactoService = inject(ContactoService);
  private router = inject(Router);
  private route = inject(ActivatedRoute);

  editarForm!: FormGroup;
  mensaje: string = '';
  error: string = '';
  contactoId!: number;
  cargando: boolean = true;

  ngOnInit() {
    this.initForm();
    this.route.paramMap.subscribe(params => {
      const id = params.get('id');
      if (id) {
        this.contactoId = +id;
        this.cargarContacto();
      }
    });
  }

  initForm() {
    this.editarForm = this.fb.group({
      nombres: ['', Validators.required],
      apellidos: ['', Validators.required],
      tipo: ['Personal', Validators.required],
      direccion: ['', Validators.required],
      telefono: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],
      sitio_web: [''],
      empresa: [''],
      cargo: ['']
    });
  }

  cargarContacto() {
    this.contactoService.obtenerPorId(this.contactoId).subscribe({
      next: (res) => {
        if (res.exito && res.data) {
          this.editarForm.patchValue(res.data);
          this.cargando = false;
        } else {
          this.error = 'No se pudo cargar la información del contacto.';
          this.cargando = false;
        }
      },
      error: (err) => {
        this.error = 'Error de servidor.';
        this.cargando = false;
      }
    });
  }

  onSubmit() {
    if (this.editarForm.valid) {
      this.contactoService.actualizar(this.contactoId, this.editarForm.value).subscribe({
        next: (res) => {
          if (res.exito) {
            this.mensaje = 'Contacto actualizado exitosamente.';
            setTimeout(() => this.router.navigate(['/agenda']), 1500);
          } else {
            this.error = res.mensaje || 'Error al actualizar contacto.';
          }
        },
        error: (err) => {
          this.error = 'Error de servidor.';
        }
      });
    } else {
      this.editarForm.markAllAsTouched();
    }
  }
}
