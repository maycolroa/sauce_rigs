import PreventiveLaboralMedicine from './PreventiveOccupationalMedicine.js'
import Administrative from './Administrative.js'

const config = []
        .concat(PreventiveLaboralMedicine)
        .concat(Administrative);



export default class VueTableConfig {
    static get(method){
        let configSelect = config.filter(f => {
            return f.name == method;
        })[0];
        return configSelect;
    }

    static getControllBase(configControlls,controll){
      let controllSelected = configControlls
      .filter(f => {
        return f.type == 'base';
      })
      .map(b => {
        return b.buttons.filter(f => {
          return f.name == controll
        })[0];
      })[0];
      return controllSelected;
    }
}