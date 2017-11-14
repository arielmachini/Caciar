import { ServicioBdProvider } from '../../providers/servicio-bd/servicio-bd';
import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';

@IonicPage()
@Component({
  selector: 'page-verformulario',
  templateUrl: 'verformulario.html',
})
export class VerformularioPage {

  campos: any[10];

  constructor(public navCtrl: NavController, public navParams: NavParams, public proveedorServicio: ServicioBdProvider) {
  }

  ionViewDidLoad() {
    console.log('Página verformulario cargada');
  }

  getCampos() {
    this.proveedorServicio.getData().subscribe(
      campo=>this.campos = campo,
      error=>console.log(error)
    );
  }

}