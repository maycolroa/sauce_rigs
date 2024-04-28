<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CREAR PROYECTO"
      url="legalaspects-contracts-proyects"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <contract-proyect-form
                url="/legalAspects/proyectContract"
                method="POST"
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
  name: 'industrialsecure-proyects-create',
  metaInfo: {
    title: 'Proyectos - Crear'
  },
  components:{
    ContractProyectForm
  },
  props: {
  },
  data(){
		return {}
	},
	created(){},
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
