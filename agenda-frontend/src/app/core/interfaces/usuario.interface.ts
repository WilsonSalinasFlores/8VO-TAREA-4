export interface IUsuario {
  id: number;
  cedula: string;
  nombre: string;
  correo: string;
  rol: string;
  is_active: boolean;
  primer_login: boolean;
}
export interface ILoginResponse {
  token: string;
  user: IUsuario;
  primer_login: boolean;
}
