<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="CREAR EVALUACIÓN"
			url="legalaspects-evaluations"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-component
                url="/legalAspects/evaluation"
                method="POST"
                :cancel-url="{ name: 'legalaspects-evaluations'}"
                :types-evaluation="typesEvaluation"
                :types-rating="typesRating"
                :evaluation="data"/>
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
      data: [],
      typesEvaluation: [],
      typesRating: []
    }
  },
  created(){
    this.fetchSelect('typesEvaluation', '/radios/ctTypesEvaluation')
    this.fetchSelect('typesRating', '/legalAspects/typeRating/AllTypesRating')

    axios.get(`/legalAspects/evaluation/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        delete this.data.id
        this.data.objectives.map((objective) => {
          delete objective.id
          delete objective.evaluation_id

          objective.subobjectives.map((subobjective) => {
            delete subobjective.id
            delete subobjective.objective_id

            subobjective.items.map((item) => {
              delete item.id
              delete item.subobjective_id

              for (var key in item.ratings)
              {
                delete item.ratings[key].item_id
              }

              return item;
            });

            return subobjective;
          });

          return objective;
        });
    })
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
