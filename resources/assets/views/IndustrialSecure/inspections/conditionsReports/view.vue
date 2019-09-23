<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Condiciones Peligrosas /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <b-row>
                <b-col>
                    <conditions-reports 
                        :report="data"
                        :view-only="true"
                        :cancel-url="{ name: 'inspections-conditionsReports'}"
                        :action-plan-states="actionPlanStates"
                        :rates="rates"
                        :disable-wacth-select-in-created="true"
                        regionals-data-url="/selects/regionals"
                        headquarters-data-url="/selects/headquarters"
                        areas-data-url="/selects/areas"
                        processes-data-url="/selects/processes"
                        conditions-data-url="/selects/industrialSecurity/conditions"
                        />
                </b-col>
            </b-row>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import ConditionsReports from '@/components/IndustrialSecure/Inspections/FormConditionsReports.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
    name: 'inspections-conditions-reports',
    metaInfo: {
        title: 'Condiciones Peligrosas - Ver'
    },
    components:{
        ConditionsReports
    },     
    data () {
        return {
            data: [],
            actionPlanStates: [],
            rates: []
        }
    },
    created(){
        axios.get(`/industrialSecurity/inspections/conditionsReports/${this.$route.params.id}`)
        .then(response => {
            this.data = response.data.data;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });

        this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
        this.fetchSelect('rates', '/selects/industrialSecurity/rates')
    },
    methods: {
        fetchSelect(key, url)
        {
            GlobalMethods.getDataMultiselect(url)
            .then(response => {
                this[key] = response;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                this.$router.go(-1);
            });
        },
    }
}
</script>