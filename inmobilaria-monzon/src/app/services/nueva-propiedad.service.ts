import { Injectable } from '@angular/core';
import { RequestService } from './request.service';
import { HttpClient } from '@angular/common/http'; // Importa el módulo HttpClient

@Injectable({
  providedIn: 'root'
})
export class NuevaPropiedadService {

  constructor(private req:RequestService) { }

  // Función para enviar el formulario a tu API

  enviarFormulario(formData: FormData) {
    return this.req.postFormData("guardar_nueva_propiedad.php", formData);
  }

}


