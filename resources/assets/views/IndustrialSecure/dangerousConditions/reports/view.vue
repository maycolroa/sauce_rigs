<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="VER INSPECCIÓN NO PLANEADA"
      url="dangerousconditions-reports"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <b-row>
                <b-col>
                    <form-report 
                        :report="data"
                        :view-only="true"
                        :cancel-url="{ name: 'dangerousconditions-reports'}"
                        :rates="rates"
                        conditions-data-url="/selects/industrialSecurity/conditions"
                        :action-plan-states="actionPlanStates"
                        />
                </b-col>
            </b-row>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormReport from '@/components/IndustrialSecure/DangerousConditions/Reports/FormReportComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
    name: 'inspections-conditions-reports',
    metaInfo: {
        title: 'Inspecciones no planeadas - Ver'
    },
    components:{
        FormReport
    },     
    data () {
        return {
            data: [],
            rates: [],
            actionPlanStates: []
        }
    },
    created(){
        axios.get(`/industrialSecurity/dangerousConditions/report/${this.$route.params.id}`)
        .then(response => {
            this.data = response.data.data;
            this.fetchSelect('rates', '/selects/industrialSecurity/rates')
            this.fetchSelect('actionPlanStates', '/selects/actionPlanStates') 
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });
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