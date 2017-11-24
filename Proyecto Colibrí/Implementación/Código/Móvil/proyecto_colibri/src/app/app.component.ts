import { Component } from '@angular/core';
import { Platform } from 'ionic-angular';
import { StatusBar } from '@ionic-native/status-bar';
import { SplashScreen } from '@ionic-native/splash-screen';

import { TabsPage } from '../pages/tabs/tabs';

/* Borrar más tarde */
import { FormulariosPage } from '../pages/formularios/formularios';
import { VerFormularioPage } from '../pages/ver-formulario/ver-formulario';

@Component({
  templateUrl: 'app.html'
})
export class MyApp {
  rootPage:any = FormulariosPage; // Devolver a TabsPage más tarde

  constructor(platform: Platform, statusBar: StatusBar, splashScreen: SplashScreen) {
    platform.ready().then(() => {
      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      statusBar.styleDefault();
      splashScreen.hide();
    });
  }
}
