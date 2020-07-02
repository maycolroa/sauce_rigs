<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="EDITAR REPORTE"
      url="dangerousconditions-reports"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <b-row>
                <b-col>
                    <form-report
                        :url="`/industrialSecurity/dangerousConditions/report/${this.$route.params.id}`"
                        method="PUT" 
                        :report="data"
                        :is-edit="true"
                        :cancel-url="{ name: 'dangerousconditions-reports'}"
                        :rates="rates"
                        :action-plan-states="actionPlanStates"
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
import FormReport from '@/components/IndustrialSecure/DangerousConditions/Reports/FormReportComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
    name: 'dangerousconditions-reports-edit',
    metaInfo: {
        title: 'Reportes - Editar'
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