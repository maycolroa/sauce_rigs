<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="EDITAR ACTIVIDAD"
      url="legalaspects-contracts-activities"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <contract-activity-form
                :url="`/legalAspects/activityContract/${this.$route.params.id}`"
                method="PUT"
                :activity="data"
                :typeDocument='typeDocument'
                :is-edit="true"
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
  name: 'legalaspects-contracts-activities-edit',
  metaInfo: {
    title: 'Actividades - Editar'
  },
  components:{
    ContractActivityForm
  },
  data () {
    return {
      data: [],
			typeDocument: []
    }
  },
  created(){
    axios.get(`/legalAspects/activityContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
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