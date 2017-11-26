import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

/* Importaciones propias */
import 'rxjs/add/operator/map';
import { Response } from '@angular/http'

@Injectable()
export class ConectorProvider {

  host: string = "http://esi.uarg.unpa.edu.ar:8103/";

  constructor(public http: HttpClient) {
    console.log('Conexión a la base de datos establecida.');
  }

  private recuperarInformacion(formulario: Response) {
    let cuerpo = formulario;
    return cuerpo || { };
  }

  recuperarFormularios() {
    return this.http.get(this.host + "ColibrIonic/recuperarFormularios.php").map(this.recuperarInformacion);
  }

  recuperarCampos(id: number) {
    return this.http.get(this.host + "ColibrIonic/recuperarCampos.php?id=" + id).map(this.recuperarInformacion);
  }

  recuperarFormulario(id: number) {
    return this.http.get(this.host + "ColibrIonic/recuperarFormulario.php?id=" + id).map(this.recuperarInformacion);
  }

}
