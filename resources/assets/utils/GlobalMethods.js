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

  static getModulesMultiselectGroup(){
    return new Promise((resolve, reject) => {
      axios.post('/selects/modulesGroup')
      .then(response => {
        resolve(response.data);
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          reject(error);
      });
    });
  }

  static getLicenseModulesMultiselectGroup(){
    return new Promise((resolve, reject) => {
      axios.post('/selects/linceseModulesGroup')
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

  static getDataMultiselect(url, params = {}){
    return new Promise((resolve, reject) => {
      axios.post(url, params)
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