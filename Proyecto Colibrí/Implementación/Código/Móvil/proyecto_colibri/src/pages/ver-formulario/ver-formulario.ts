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

  private formulario: any;

  constructor(public navCtrl: NavController, public navParams: NavParams, private servicioConector: ConectorProvider) {
    console.log(this.recuperarFormulario(3));
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad VerFormularioPage');
  }

  recuperarFormulario(id: number) {
    this.servicioConector.recuperarFormulario(id).subscribe((formulario: Response) => this.formulario = formulario);
  }

}
