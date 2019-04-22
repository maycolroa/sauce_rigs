import PreventiveLaboralMedicine from './PreventiveOccupationalMedicine.js'
import LegalAspects from './LegalAspects.js'
import Administrative from './Administrative.js'

const config = []
        .concat(PreventiveLaboralMedicine)
        .concat(LegalAspects)
        .concat(Administrative);

export default class FilterConfig {
    static get(method){
        let configSelect = config.filter(f => {
            return f.name == method;
        })[0];
        return configSelect;
    }
} 