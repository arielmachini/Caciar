import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

/* Importaciones propias */
import 'rxjs/add/operator/map';
import { Http } from '@angular/http';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class ConectorProvider {

  private host: string = "http://localhost/";

  constructor(public http: HttpClient) {
    console.log('Hello ConectorProvider Provider');
  }

  recuperarFormulario(id: number) {
    return this.http.get(this.host + "recuperarFormulario.php?id=" + id).map(res => res.json());
  }

}
