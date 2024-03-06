<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="VER VEHICULO"
      url="industrialsecure-roadsafety-vehicles"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <vehicle-form
                :vehicles="data"
                :view-only="true"
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

export default {
  name: 'industrialsecure-roadsafety-vehicles-view',
  metaInfo: {
    title: 'Vehiculos - Ver'
  },
  components:{
    VehicleForm
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/industrialSecurity/roadsafety/vehicles/${this.$route.params.id}`)
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