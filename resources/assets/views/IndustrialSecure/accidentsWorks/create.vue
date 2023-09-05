<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="CREAR REPORTE"
      url="industrialsecure-accidentswork"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <accident-form
                url="/industrialSecurity/accidents"
                method="POST"
                :sexs="sexs"
                :cancel-url="{ name: 'industrialsecure-accidentswork'}"
                />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AccidentForm from '@/components/IndustrialSecure/AccidentsWork/FormAccidentsComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'industrialsecure-accidents-work-create',
  metaInfo: {
    title: 'Accidentes e incidentes - Crear'
  },
  components:{
    AccidentForm
  },
  data(){
    return {
      sexs: []
    }
  },
  created(){ 
    this.fetchSelect('sexs', '/selects/sexs')
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
			});
		},
	}
}
</script>
