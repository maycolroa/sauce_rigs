<template>
  <div>
    <header-module
        title="AUDIOMETRIAS"
        subtitle="INFORME INDIVIDUAL"
        url="biologicalmonitoring-audiometry"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <b-row>
                <b-col>
                    <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                        <vue-ajax-advanced-select class="col-md-12" v-model="employee_id"  name="employee_id" label="Empleado" placeholder="Seleccione el empleado" :url="employeesDataUrl" :selected-object="selectedObject">
                            </vue-ajax-advanced-select>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <information-general :employee="employee_information"/>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                        <audiometry-table
                            :view-index="false"
                            configNameTable="biologicalmonitoring-audiometry-employee"
                            :employee-id="employee_id"/>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card border-variant="primary" title="Oído Derecho Aéreo" class="mb-3 box-shadow-none">
                        <chart-line-multiple
                            :chart-data="audiometries_right_ear"
                            title="Oído Derecho Aéreo"
                            ref="audiometries_right_ear"/>
                    </b-card>
                </b-col>
                <b-col>
                    <b-card border-variant="secondary" title="Oído Izquierdo Aéreo" class="mb-3 box-shadow-none">
                        <chart-line-multiple
                            :chart-data="audiometries_left_ear"
                            title="Oído Izquierdo Aéreo"
                            ref="audiometries_left_ear"/>
                    </b-card>
                </b-col>
            </b-row>
        </b-card-body>
      </b-card>
    </div>

    <div class="row float-right pt-10 pr-10">
        <template>
            <b-btn variant="default" :to="{name: 'biologicalmonitoring-audiometry'}">Atras</b-btn>
        </template>
    </div>
  </div>
</template>

<script>
import InformationGeneral from '@/components/Administrative/Employees/InformationGeneral.vue';
import AudiometryTable from '@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/index.vue';
import ChartLineMultiple from '@/components/ECharts/ChartLineMultiple.vue';
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Alerts from '@/utils/Alerts.js';

export default {
    name: 'audiometry-informs-individual',
    metaInfo: {
        title: 'Audiometrias - Informe Individual'
    },
    components:{
        InformationGeneral,
        AudiometryTable,
        ChartLineMultiple,
        VueAjaxAdvancedSelect
    },
    data () {
        return {
            isLoading: false,
            employee_id: '-1',
            selectedObject: null,
            employeesDataUrl: '/selects/employees',
            employee_information: {
                identification: '',
                name: '',
                date_of_birth: '',
                age: '',
                sex: '',
                email: '',
                income_date: '',
                regional: '',
                headquarter: '',
                area: '',
                process: '',
                position: '',
                business: '',
                eps: ''
            },
            audiometries_right_ear: {
                xAxis: [],
                legend: [],
                datasets: []
            },
            audiometries_left_ear: {
                xAxis: [],
                legend: [],
                datasets: []
            }
        }
    },
    watch:{
        employee_id () {
            if (!this.isLoading)
                this.fetch()
        }
    },
    mounted() {
        if (this.$route.params.id)
        {
            this.isLoading = true;
            this.employee_id = this.$route.params.id
            
            axios.post('/biologicalmonitoring/audiometry/informs/individual', {
                employee_id: this.$route.params.id
            })
            .then(data => {
                this.selectedObject = { name: `${data.data.employee_information.identification} - ${data.data.employee_information.name}`, value: this.$route.params.id }
                this.update(data);
                setTimeout(() => {
                    this.isLoading = false;
                }, 3000)
            })
            .catch(error => {
                this.isLoading = false;
                Alerts.error('Error', 'Hubo un problema recolectando la información');
            });
        }
    },
    methods: {
        fetch()
        {
            this.isLoading = true;

            axios.post('/biologicalmonitoring/audiometry/informs/individual', {
                employee_id: this.employee_id
            })
            .then(data => {
                this.update(data);
                this.isLoading = false;
            })
            .catch(error => {
                this.isLoading = false;
                Alerts.error('Error', 'Hubo un problema recolectando la información');
            });
        },
        update(data) {
            _.forIn(data.data, (value, key) => {
                if (this[key]) {
                    this[key] = value;
                }
            });
        }
    }
}
</script>