<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Evaluaciones Realizadas/</span> Editar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-contract-component
                :url="`/legalAspects/evaluationContract/${this.$route.params.id}`"
                method="PUT"
                :evaluation="data"
                :is-edit="true"
                :types-rating="typesRating"
                userDataUrl="/selects/users"
                :cancel-url="{ name: 'legalaspects-evaluations-contracts'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormEvaluationContractComponent from '@/components/LegalAspects/Contracts/EvaluationContracts/FormEvaluationContractComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-evaluations-contratcs-edit',
  metaInfo: {
    title: 'Evaluaciones Realizadas - Editar'
  },
  components:{
    FormEvaluationContractComponent
  },
  data () {
    return {
      data: [],
      typesRating: []
    }
  },
  created(){

    axios.get(`/legalAspects/evaluationContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    this.fetchSelect('typesRating', '/legalAspects/typeRating/AllTypesRating')
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