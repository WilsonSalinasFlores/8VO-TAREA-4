import { Component, Input } from '@angular/core';
import { NgClass, NgIf } from '@angular/common';

@Component({
  selector: 'app-alerta',
  standalone: true,
  imports: [NgClass, NgIf],
  template: `
    <div *ngIf="mensaje" class="alert alert-{{tipo}} alert-dismissible fade show" role="alert">
      {{ mensaje }}
      <button *ngIf="dismissible" type="button" class="btn-close" (click)="mensaje = ''"></button>
    </div>
  `
})
export class AlertaComponent {
  @Input() tipo: 'success'|'danger'|'warning'|'info' = 'info';
  @Input() mensaje = '';
  @Input() dismissible = true;
}
