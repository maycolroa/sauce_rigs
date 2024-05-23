<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="VER CONTRATISTA"
        url="legalaspects-contractor"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-contract 
                :contract="data"
                :view-only="true"
                :roles="roles"
                :contract-classifications="contractClassifications"
                :users-responsibles="usersResponsibles"
                :usersContract="usersContract"
				:kindsRisks="kindsRisks"
                :cancel-url="{ name: 'legalaspects-contractor'}"
                :si-no="siNo"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormContract from '@/components/LegalAspects/Contracts/ContractLessee/FormContractComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
    name: 'legalaspects-contractor-view',
    metaInfo: {
        title: 'Contratistas - Ver'
    },
    components:{
        FormContract
    },
    data () {
        return {
            data: [],
            roles: [],
            contractClassifications: [],
            siNo: [],
            usersResponsibles: [],
            usersContract: []
        }
    },
    created(){
        axios.get(`/legalAspects/contracts/${this.$route.params.id}`)
        .then(response => {
            this.data = response.data.data;
            this.usersContract = response.data.data.usersContract;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });

        this.fetchSelect('roles', '/selects/ctRoles')
        this.fetchSelect('contractClassifications', '/selects/ctContractClassifications')
        this.fetchSelect('siNo', '/radios/siNo')
		this.fetchSelect('usersResponsibles', '/selects/contracts/usersResponsibles')
		this.fetchSelect('kindsRisks', '/selects/ctkindsRisks') 
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