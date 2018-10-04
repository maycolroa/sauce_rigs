import Alerts from './Alerts.js';

export default class GlobalMethods {
  static getConfigMultiselect(select){
    return new Promise((resolve, reject) => {
      axios.post(`/selects/multiselect`, {
        select: select
      })
      .then(response => {
        resolve(response.data);
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          reject(error);
      });
    });
  }

  static getPermissionsMultiselect(select){
    return new Promise((resolve, reject) => {
      axios.post('/selects/permissions')
      .then(response => {
        resolve(response.data);
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          reject(error);
      });
    });
  }
}