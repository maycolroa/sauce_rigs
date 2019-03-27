<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Evaluaciones /</span> Evaluar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-component
                :url="`/legalAspects/evaluation/evaluate/${this.$route.params.id}`"
                method="PUT"
                :evaluation="data"
                :types-evaluation="typesEvaluation"
                :types-rating="typesRating"
                :is-evaluation="true"
                :view-only="true"
                :cancel-url="{ name: 'legalaspects-evaluations'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormEvaluationComponent from '@/components/LegalAspects/Evaluations/FormEvaluationComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-evaluations-evaluate',
  metaInfo: {
    title: 'Evaluaciones - Evaluar'
  },
  components:{
    FormEvaluationComponent
  },
  data () {
    return {
      data: [],
      typesEvaluation: [],
      typesRating: []
    }
  },
  created(){

    axios.get(`/legalAspects/evaluation/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

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