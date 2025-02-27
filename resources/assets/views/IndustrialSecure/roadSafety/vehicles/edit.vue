<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="EDITAR VEHICULO"
      url="industrialsecure-roadsafety-vehicles"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <vehicle-form
              :url="`/industrialSecurity/roadsafety/vehicles/${this.$route.params.id}`"
              method="PUT"
              :years="years"
              :vehicles="data"
              :is-edit="true"
              :cancel-url="{ name: 'industrialsecure-roadsafety-vehicles'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import VehicleForm from '@/components/IndustrialSecure/RoadSafety/Vehicles/FormVehicleComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'industrialsecure-roadsafety-vehicles-edit',
  metaInfo: {
    title: 'Vehiculos - Editar'
  },
  components:{
    VehicleForm,
    Loading
  },
  data () {
    return {
      data: [],
      years: [],
      ready: false,
    }
  },
  created(){
    axios.get(`/industrialSecurity/roadsafety/vehicles/${this.$route.params.id}`)
    .then(response => {
      this.fetchSelect('years', '/selects/legalMatrix/years')
        this.data = response.data.data;
        this.ready = true
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
            //Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            //this.$router.go(-1);
        });
    },
  }
}
</script>