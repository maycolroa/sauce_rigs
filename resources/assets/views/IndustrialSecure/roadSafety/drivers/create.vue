<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="CREAR CONDUCTORES"
      url="industrialsecure-roadsafety-drivers"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <driver-form
                url="/industrialSecurity/roadsafety/drivers"
                method="POST"
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
  name: 'industrialsecure-roadsafety-drivers-create',
  metaInfo: {
    title: 'Conductores - Crear'
  },
  components:{
    DriverForm
  },
  props: {
  },
  data(){
		return {
		}
	},
	created(){
	},
  methods: {
	}
}
</script>
