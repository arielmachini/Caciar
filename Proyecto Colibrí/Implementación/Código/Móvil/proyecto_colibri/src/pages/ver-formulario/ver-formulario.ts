import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';

/* Importaciones propias */
import { ConectorProvider } from '../../providers/conector/conector';

@IonicPage()
@Component({
  selector: 'page-ver-formulario',
  templateUrl: 'ver-formulario.html',
})
export class VerFormularioPage {

  formulario: any = '';
  camposFormulario: any[999];

  constructor(public navCtrl: NavController, public navParams: NavParams, private servicioConector: ConectorProvider) {
    this.recuperarFormulario(1);
    this.recuperarCampos(1);
  }

  ionViewDidLoad() {
    
  }

  recuperarCampos(id: number) {
    this.servicioConector.recuperarCampos(id).subscribe((camposFormulario: Response) => this.camposFormulario = camposFormulario, error => console.log("E: Se produjo el siguiente error al intentar recuperar los campos del formulario de la base de datos: " + error));
  }

  recuperarFormulario(id: number) {
    this.servicioConector.recuperarFormulario(id).subscribe((formulario: Response) => this.formulario = formulario, error => console.log("E: Se produjo el siguiente error al intentar recuperar el formulario de la base de datos: " + error));
  }

}
