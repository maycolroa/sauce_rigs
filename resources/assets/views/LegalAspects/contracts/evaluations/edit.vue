<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="EDITAR EVALUACIÓN"
			url="legalaspects-evaluations"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-component
                :url="`/legalAspects/evaluation/${this.$route.params.id}`"
                method="PUT"
                :evaluation="data"
                :is-edit="true"
                :cancel-url="{ name: 'legalaspects-evaluations'}"
                :types-evaluation="typesEvaluation"
                :types-rating="typesRating"
                userDataUrl="/selects/users"/>
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
  name: 'legalaspects-evaluations-edit',
  metaInfo: {
    title: 'Evaluaciones - Editar'
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
        });
    },
  }
}
</script>