<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Evaluaciones /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-component
                url="/legalAspects/evaluation"
                method="POST"
                :cancel-url="{ name: 'legalaspects-evaluations'}"
                :types-evaluation="typesEvaluation"
                :types-rating="typesRating"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormEvaluationComponent from '@/components/LegalAspects/Contracts/Evaluations/FormEvaluationComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-evaluations-create',
  metaInfo: {
    title: 'Evaluaciones - Crear'
  },
  components:{
    FormEvaluationComponent
  },
  data(){
    return {
      typesEvaluation: [],
      typesRating: []
    }
  },
  created(){
    this.fetchSelect('typesEvaluation', '/radios/ctTypesEvaluation')
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
