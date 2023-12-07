<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
      subtitle="CONFIGURACIONES"
      url="industrialsecure-epp"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-configuration-component
                url="/industrialSecurity/epp/configuration"
                method="POST"
                :configuration="data"
                :si-no="siNo"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormConfigurationComponent from '@/components/IndustrialSecure/Epp/FormConfigurationComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'administrative-configurations',
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
    this.fetchSelect('siNo', '/radios/siNo')

    axios.get('/industrialSecurity/epp/configuration/view')
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
