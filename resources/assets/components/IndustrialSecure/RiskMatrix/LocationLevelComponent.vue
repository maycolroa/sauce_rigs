<template>
    <div class="col-md-12">
        <b-form-row>
            <vue-ajax-advanced-select v-if="inputs.regional == 'SI'" :disabled="viewOnly" class="col-md-6" v-model="locationLevel.employee_regional_id" :selected-object="locationLevel.multiselect_regional" name="employee_regional_id" :label="keywordCheck('regional')" placeholder="Seleccione una opción" :url="regionalsDataUrl" :error="form.errorsFor('locations.employee_regional_id')">
                </vue-ajax-advanced-select>
            <vue-ajax-advanced-select v-if="inputs.headquarter == 'SI'" :disabled="viewOnly || !locationLevel.employee_regional_id" class="col-md-6" v-model="locationLevel.employee_headquarter_id" :selected-object="locationLevel.multiselect_headquarter" name="employee_headquarter_id" :label="keywordCheck('headquarter')" placeholder="Seleccione una opción" :url="headquartersDataUrl" :parameters="{regional: locationLevel.employee_regional_id }" :emptyAll="empty.headquarter" @updateEmpty="updateEmptyKey('headquarter')" :error="form.errorsFor('locations.employee_headquarter_id')">
                </vue-ajax-advanced-select>
        </b-form-row>
        <b-form-row>
            <vue-ajax-advanced-select v-if="inputs.process == 'SI'" :disabled="viewOnly || !locationLevel.employee_headquarter_id" class="col-md-6" v-model="locationLevel.employee_process_id" :selected-object="locationLevel.multiselect_process" name="employee_process_id" :label="keywordCheck('process')" placeholder="Seleccione una opción" :url="processesDataUrl" :parameters="{headquarter: locationLevel.employee_headquarter_id }" :emptyAll="empty.process" @updateEmpty="updateEmptyKey('process')" :error="form.errorsFor('locations.employee_process_id')">
                </vue-ajax-advanced-select>
            <vue-ajax-advanced-select v-if="inputs.area == 'SI'" :disabled="viewOnly || !locationLevel.employee_process_id" class="col-md-6" v-model="locationLevel.employee_area_id" :selected-object="locationLevel.multiselect_area" name="employee_area_id" :label="keywordCheck('area')" placeholder="Seleccione una opción" :url="areasDataUrl" :parameters="{headquarter: locationLevel.employee_headquarter_id, process: locationLevel.employee_process_id }" :emptyAll="empty.area" @updateEmpty="updateEmptyKey('area')" :error="form.errorsFor('locations.employee_area_id')">
                </vue-ajax-advanced-select>
        </b-form-row>
        <b-form-row>
        <vue-ajax-advanced-select v-if="inputs.process == 'SI'" :disabled="viewOnly || !locationLevel.employee_process_id" class="col-md-6" v-model="locationLevel.macroprocess_id" :selected-object="locationLevel.multiselect_macroprocess" name="macroprocess_id" label="Macroproceso" placeholder="Seleccione una opción" :url="macroprocessDataUrl" :parameters="{process: form.locations.employee_process_id }" :emptyAll="empty.macroprocess" @updateEmpty="updateEmptyKey('macroprocess')" :error="form.errorsFor('locations.macroprocess_id')">
                </vue-ajax-advanced-select>
      </b-form-row>
    </div>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Alerts from '@/utils/Alerts.js';

export default {
    components: {
        VueAjaxAdvancedSelect
    },
    props: {
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        form: { type: Object, required: true },
        locationLevel: {
            default() {
                return {
                    employee_regional_id: '',
                    employee_headquarter_id: '',
                    employee_area_id: '',
                    employee_process_id: '',
                    macroprocess_id: '',
                    nomenclature: ''
                };
            }
        }
    },
    data() {
        return {
            regionalsDataUrl: '/selects/regionals',
            headquartersDataUrl: '/selects/headquarters',
            areasDataUrl: '/selects/areas',
            processesDataUrl: '/selects/processes',
            macroprocessDataUrl: '/selects/macroprocess',
            loading: this.isEdit,
            empty: {
                headquarter: false,
                area: false,
                process: false,
                macroprocess: false
            },
            disableWacth: true,
            inputs: {
                regional: 'NO',
                headquarter: 'NO',
                area: 'NO',
                process: 'NO'
            },
            nom_reg: '',
            nom_macro: '',
            nom_proc: ''
        };
    },
    created()
    {
        axios.post('/administration/configurations/locationLevelForms/getConfModule')
        .then(data => {
            if (Object.keys(data.data).length > 0)
                setTimeout(() => {
                    this.inputs = data.data
                    this.disableWacth = false
                }, 3000)
            this.isLoading = false;
        })
        .catch(error => {
            this.isLoading = false;
            Alerts.error('Error', 'Hubo un problema recolectando la información');
        });
    },
    mounted() {
        /*setTimeout(() => {
            this.disableWacth = false
        }, 3000)*/
    },
    watch: {
        'locationLevel.employee_regional_id'() {
            this.updateNomenclatureReg();
            this.emptySelect('employee_area_id', 'area')
            this.emptySelect('employee_process_id', 'process')
            this.emptySelect('employee_headquarter_id', 'headquarter')
        },
        'locationLevel.employee_headquarter_id'() {
            //this.updateNomenclatureHead();
            this.emptySelect('employee_area_id', 'area')
            this.emptySelect('employee_process_id', 'process')
        },
        'locationLevel.employee_process_id'() {
            this.updateNomenclaturePro();
            this.emptySelect('employee_area_id', 'area')
            this.emptySelect('macroprocess_id', 'macroprocess')
        },
        /*'locationLevel.employee_area_id'() {
            this.updateNomenclatureArea();
        },*/
        'locationLevel.macroprocess_id'() {
            this.updateNomenclatureMacro();
        },
        locationLevel: {
            handler(val){
                this.$emit("input", this.locationLevel);
            },
            deep: true
        },
        inputs() {
            this.$emit("configLocation", this.inputs);
        }
    },
    methods: {
        updateEmptyKey(keyEmpty) {
            this.empty[keyEmpty]  = false
        },
        emptySelect(keySelect, keyEmpty) {
            if (this.locationLevel[keySelect] !== '' && !this.disableWacth)
            {
                this.empty[keyEmpty] = true
                this.locationLevel[keySelect] = ''
            }
        },
        updateNomenclatureReg()
        {
            axios.post("/industrialSecurity/risksMatrix/getAbrevRegional", {id: this.locationLevel.employee_regional_id})
            .then(response => {
                this.nom_reg = response.data.data;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
        },
        /*updateNomenclatureHead()
        {
            axios.post("/industrialSecurity/risksMatrix/getAbrevHeadquarter", {id: this.locationLevel.employee_headquarter_id})
            .then(response => {
                this.nomenclature = response.data.data;
                console.log(this.nomenclature)
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
        },*/
        updateNomenclaturePro()
        {
            axios.post("/industrialSecurity/risksMatrix/getAbrevProcess", {id: this.locationLevel.employee_process_id})
            .then(response => {
                this.nom_proc = response.data.data;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
        },
        updateNomenclatureMacro()
        {
            axios.post("/industrialSecurity/risksMatrix/getAbrevMacro", {id: this.locationLevel.macroprocess_id})
            .then(response => {
                this.nom_macro = response.data.data;
                this.locationLevel.nomenclature = this.nom_reg + '.' + this.nom_macro + '.' + this.nom_proc + '.';
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
        },
        /*updateNomenclatureArea()
        {
            axios.post("/industrialSecurity/risksMatrix/getAbrevArea", {id: this.locationLevel.employee_area_id})
            .then(response => {
                this.nomenclature = response.data.data;
                console.log(this.nomenclature)
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
        },*/

    }
}
</script>