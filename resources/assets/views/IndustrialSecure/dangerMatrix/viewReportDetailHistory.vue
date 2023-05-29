<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="VER DETALLE"
      url="industrialsecure-dangermatrix-report-history"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
        <loading :display="!ready"/>
          <div v-if="ready">
            <form-danger-component
                :is-edit="false"
                :view-only="true"
                :danger="danger"
                :danger-generated="dangerGenerated"
                :type-activities="typeActivities"
                :form="data"
                :activity="activity"
                :configuration="configuration"
              />
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormDangerComponent from '@/components/IndustrialSecure/DangerMatrix/FormDangerDetailHistoryComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'industrialsecure-dangermatrix-view',
  metaInfo: {
    title: 'Matriz de Peligros - Reporte Historico'
  },
  components:{
    FormDangerComponent,
    Loading
  },
  data () {
    return {
      typeActivities: [],
      dangerGenerated: [],
      siNo: [],
      qualifications: [],
      data: [],
      activity: [],
      danger: [],
      configuration: [],      
      ready: false
    }
  },
  created(){    
    axios.get(`/industrialSecurity/dangersMatrix/reportDetailHistory/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data.form;

        axios.get('/administration/configuration/view')
        .then(response2 => {
            this.configuration = response2.data.data;
            this.ready = true
        })
        .catch(error => {
        });
    })
    .catch(error => {
    });

    this.fetchSelect('typeActivities', '/radios/dmTypeActivities')
  },
  methods: {
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
        .then(response => {
            this[key] = response;
        })
        .catch(error => {
        });
    },
  }
}
</script>