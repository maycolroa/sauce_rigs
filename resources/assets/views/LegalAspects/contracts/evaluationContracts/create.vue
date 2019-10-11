<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="EVALUAR"
        url="legalaspects-evaluations"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-contract-component
                :url="`/legalAspects/evaluationContract`"
                method="POST"
                :evaluation="data"
                :types-rating="typesRating"
                userDataUrl="/selects/users"
                :cancel-url="{ name: 'legalaspects-evaluations'}"/>
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
  name: 'legalaspects-evaluations-contratc-create',
  metaInfo: {
    title: 'Evaluaciones - Evaluar'
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

    axios.get(`/legalAspects/evaluationContract/getData/${this.$route.params.id}`)
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