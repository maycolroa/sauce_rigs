<template>
    <div class="col-md-12">
        <b-form-row>
            <vue-input v-if="inputs.regional == 'SI'" :disabled="viewOnly" class="col-md-6" v-model="locationLevel.regional" name="regional" :label="keywordCheck('regional')" placeholder="Seleccione una opción">
                </vue-input>
            <vue-input v-if="inputs.headquarter == 'SI'" :disabled="viewOnly" class="col-md-6" v-model="locationLevel.headquarter" name="headquarter" :label="keywordCheck('headquarter')" placeholder="Seleccione una opción">
                </vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input v-if="inputs.process == 'SI'" :disabled="viewOnly" class="col-md-6" v-model="locationLevel.process"  name="process" :label="keywordCheck('process')" placeholder="Seleccione una opción">
                </vue-input>
            <vue-input v-if="inputs.area == 'SI'" :disabled="viewOnly" class="col-md-6" v-model="locationLevel.area"  name="area" :label="keywordCheck('area')" placeholder="Seleccione una opción">
                </vue-input>
        </b-form-row>
    </div>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from '../Inputs/VueInput.vue';

export default {
    components: {
        VueAjaxAdvancedSelect,
        VueInput
    },
    props: {
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        form: { type: Object, required: true },
        locationLevel: {
            default() {
                return {
                    regional: '',
                    headquarter: '',
                    area: '',
                    process: ''
                };
            }
        }
    },
    data() {
        return {
            loading: this.isEdit,
            disableWacth: true,
            inputs: {
                regional: 'NO',
                headquarter: 'NO',
                area: 'NO',
                process: 'NO'
            }
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
            this.emptySelect('employee_area_id', 'area')
            this.emptySelect('employee_process_id', 'process')
            this.emptySelect('employee_headquarter_id', 'headquarter')
        },
        'locationLevel.employee_headquarter_id'() {
            this.emptySelect('employee_area_id', 'area')
            this.emptySelect('employee_process_id', 'process')
        },
        'locationLevel.employee_process_id'() {
            this.emptySelect('employee_area_id', 'area')
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
        }
    }
}
</script>