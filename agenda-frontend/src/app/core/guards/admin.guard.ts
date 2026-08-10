import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const adminGuard = (route: any, state: any) => {
  const auth = inject(AuthService);
  const router = inject(Router);
  if (!auth.isLoggedIn()) return router.parseUrl('/login');
  if (auth.isPrimerLogin()) return router.parseUrl('/primer-login');
  if (!auth.isSuperusuario()) return router.parseUrl('/agenda');
  return true;
};
