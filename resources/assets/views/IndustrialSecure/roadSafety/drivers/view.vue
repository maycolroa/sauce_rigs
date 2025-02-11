<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="VER CONDUCTORES"
      url="industrialsecure-roadsafety-drivers"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1000px">
            <driver-form
                :driver="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-roadsafety-drivers'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import DriverForm from '@/components/IndustrialSecure/RoadSafety/Drivers/FormDriverComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'industrialsecure-roadsafety-drivers-view',
  metaInfo: {
    title: 'Conductores - Ver'
  },
  components:{
    DriverForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/roadsafety/drivers/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
  methods: {}
}
</script>