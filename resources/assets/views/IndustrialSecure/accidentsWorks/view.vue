<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="VER FORMULARIO"
      url="industrialsecure-accidentswork"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
        <loading :display="!ready"/>
          <div v-if="ready">
            <accident-form
                :accident="data"
                :view-only="true"
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
  name: 'industrialsecure-accidents-work-view',
  metaInfo: {
    title: 'Accidentes e incidentes - Ver'
  },
  components:{
    AccidentForm,
    Loading
  },
  data () {
    return {
      data: [],
      ready: false,
    }
  },
  created(){
    axios.get(`/industrialSecurity/accidents/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.ready = true
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
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