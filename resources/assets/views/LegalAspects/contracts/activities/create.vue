<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CREAR ACTIVIDAD"
      url="legalaspects-contracts-activities"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <contract-activity-form
                url="/legalAspects/activityContract"
                method="POST"
                :typeDocument='typeDocument' 
                :cancel-url="{ name: 'legalaspects-contracts-activities'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import ContractActivityForm from '@/components/LegalAspects/Contracts/Activities/FormContractActivityComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'industrialsecure-activities-create',
  metaInfo: {
    title: 'Actividades - Crear'
  },
  components:{
    ContractActivityForm
  },
  props: {
  },
  data(){
		return {
			typeDocument: []
		}
	},
	created(){
		this.fetchSelect('typeDocument', '/selects/typesDocument')
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
