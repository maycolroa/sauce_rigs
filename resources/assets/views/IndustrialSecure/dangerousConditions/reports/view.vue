<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="VER REPORTE"
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
import FormReport from '@/components/IndustrialSecure/DangerousConditions/Reports/FormReportComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
    name: 'inspections-conditions-reports',
    metaInfo: {
        title: 'Condiciones Peligrosas - Ver'
    },
    components:{
        FormReport
    },     
    data () {
        return {
            data: [],
            rates: []
        }
    },
    created(){
        axios.get(`/industrialSecurity/dangerousConditions/report/${this.$route.params.id}`)
        .then(response => {
            this.data = response.data.data;
            this.fetchSelect('rates', '/selects/industrialSecurity/rates')
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