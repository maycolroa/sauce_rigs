<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="CONFIGURACIONES"
      url="industrialsecure-dangerousconditions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-configuration-component
                url="/industrialSecurity/configuration"
                method="POST"
                :configuration="data"
                :locationLevels="locationLevels"
                :si-no="siNo"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormConfigurationComponent from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormConfigurationComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'dangerousconditions-inspections-configurations',
  metaInfo: {
    title: 'Configuraciones'
  },
  components:{
    FormConfigurationComponent
  },
  data(){
    return {
      data: [],
      locationLevels: [],
      siNo: []
    }
  },
  created(){
    this.fetchSelect('locationLevels', '/radios/conf/locationLevelForm')

    axios.get('/industrialSecurity/configuration/view')
    .then(response => {
        this.data = response.data.data;
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
