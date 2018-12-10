import PreventiveLaboralMedicine from './PreventiveOccupationalMedicine.js'

const config = []
        .concat(PreventiveLaboralMedicine);

export default class FilterConfig {
    static get(method){
        let configSelect = config.filter(f => {
            return f.name == method;
        })[0];
        return configSelect;
    }
}