export interface IContacto {
  id: number;
  nombres: string;
  apellidos: string;
  tipo: string;
  direccion: string;
  telefono: string;
  sitio_web?: string;
  empresa?: string;
  cargo?: string;
  eliminado?: boolean;
}
