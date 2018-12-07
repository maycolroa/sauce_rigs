<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Audiometrias /</span> Informe Individual
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <b-row>
                <b-col>
                    <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                        <vue-ajax-advanced-select class="col-md-12" v-model="employee_id"  name="employee_id" label="Empleado" placeholder="Seleccione el empleado" :url="employeesDataUrl">
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
            this.fetch()
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
                console.log(error);
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