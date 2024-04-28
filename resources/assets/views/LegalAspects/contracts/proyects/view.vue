<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="VER PROYECTO"
      url="legalaspects-contracts-proyects"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <contract-proyect-form
                :activity="data"
                :typeDocument='typeDocument' 
                :view-only="true"
                :cancel-url="{ name: 'legalaspects-contracts-proyects'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import ContractProyectForm from '@/components/LegalAspects/Contracts/Proyects/FormContractProyectComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-contracts-proyects-view',
  metaInfo: {
    title: 'Proyectos - Ver'
  },
  components:{
    ContractProyectForm
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/legalAspects/proyectContract/${this.$route.params.id}`)
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