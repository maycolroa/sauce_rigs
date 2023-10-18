<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="EDITAR REPORTE"
      url="industrialsecure-accidentswork"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <accident-form
                :url="`/industrialSecurity/accidents/${this.$route.params.id}`"
                method="PUT"
                :accident="data"
                :is-edit="true"
                :sexs="sexs"
                :cancel-url="{ name: 'industrialsecure-accidentswork'}"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AccidentForm from '@/components/IndustrialSecure/AccidentsWork/FormAccidentsComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'industrialsecure-accidents-work-edit',
  metaInfo: {
    title: 'Accidentes e incidentes - Editar'
  },
  components:{
    AccidentForm,
    Loading
  },
  data () {
    return {
      data: [],
      sexs: [],
      ready: false,
    }
  },
  created(){
    axios.get(`/industrialSecurity/accidents/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;

        if (!response.data.data.employee_regional_id)
        {
          Alerts.error('Error', 'Debe completar la informacion del empleado');
          this.$router.go(-1);
        }

        this.ready = true
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

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