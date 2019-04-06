import PreventiveLaboralMedicine from './PreventiveOccupationalMedicine.js'
import LegalAspects from './LegalAspects.js'

const config = []
        .concat(PreventiveLaboralMedicine)
        .concat(LegalAspects);

export default class FilterConfig {
    static get(method){
        let configSelect = config.filter(f => {
            return f.name == method;
        })[0];
        return configSelect;
    }
} 