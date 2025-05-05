import Vue from 'vue'

export default class Alerts {

  static error(title= 'Error detectado', message = 'Ha ocurrido un error en el proceso') {
    Vue.notify({
      group: 'app',
      type: 'bg-danger text-white',
      title: title,
      text: message,
      duration: 10000
    });
  }

  static success(title= 'Exito', message = 'El proceso ha finalizado exitosamente') {
    Vue.notify({
      group: 'app',
      type: 'bg-success text-white',
      title: title,
      text: message,
      duration: 10000
    });
  }


  static warning(title = 'Cuidado', message = 'Revise que el proceso haya finalizado exitosamente') {
    Vue.notify({
      group: 'app',
      type: 'bg-warning text-dark',
      title: title,
      text: message,
      duration: 10000
    });
  }

  static info(title= 'Información', message = 'A tener en consideración', time = 10000) {
    Vue.notify({
      group: 'app',
      type: 'bg-info text-white',
      title: title,
      text: message,
      duration: time
    });
  }
}