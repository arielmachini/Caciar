import 'rxjs/add/operator/map';
import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

/*
  Generated class for the ServicioBdProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class ServicioBdProvider {

  url:string = 'http://localhost/colibrimovil/'

  constructor(public http: HttpClient) {
    console.log('ServicioBdProvider iniciado');
  }

  getData() {
    return this.http.get(this.url + "ObtenerCampos.php?id=1");
  }

}
