import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';

/* Importaciones propias */
import { ConectorProvider } from '../../providers/conector/conector';

@IonicPage()
@Component({
  selector: 'page-formularios',
  templateUrl: 'formularios.html',
})
export class FormulariosPage {

  formularios: any[999];

  constructor(public navCtrl: NavController, public navParams: NavParams, private servicioConector: ConectorProvider) {
    this.recuperarFormularios();
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad FormulariosPage');
  }

  recuperarFormularios() {
    this.servicioConector.recuperarFormularios().subscribe((formularios: Response) => this.formularios = formularios, error => console.log("E: Se produjo el siguiente error al intentar recuperar los formularios de la base de datos: " + error));
  }

}
