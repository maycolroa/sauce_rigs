<template>
  <div>
    <header-module
      title="MI MATRIZ LEGAL"
      subtitle="EVALUAR NORMA"
      url="legalaspects-lm-law-qualify"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-law-qualify-component
                :url="`/legalAspects/legalMatrix/law/${this.$route.params.id}`"
                method="PUT"
                :law="data"
                :qualifications="qualifications"
                :action-plan-states="actionPlanStates"
                :si-no="siNo"
                :filter-interests-options="filterInterestsOptions"
                :is-edit="true"
                :cancel-url="{ name: 'legalaspects-lm-law-qualify'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLawQualifyComponent from '@/components/LegalAspects/LegalMatrix/Law/FormLawQualifyComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-lm-law-qualify-edit',
  metaInfo: {
    title: 'Evaluar Normas'
  },
  components:{
    FormLawQualifyComponent
  },
  data () {
    return {
      data: [],
      qualifications: [],
      actionPlanStates: [],
      siNo: [],
      filterInterestsOptions: []
    }
  },
  created(){
    this.fetchSelect('qualifications', '/selects/legalMatrix/articlesQualifications')
    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
    this.fetchSelect('siNo', '/selects/siNo')

    axios.post('/selects/legalMatrix/filterInterests', { id: this.$route.params.id })
    .then(response => {
        this.filterInterestsOptions = response.data;
    })
    .catch(error => {
        //Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        //this.$router.go(-1);
    });

    axios.get(`/legalAspects/legalMatrix/law/qualify/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        //Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        //this.$router.go(-1);
    });
    
  },
  methods: {
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
        .then(response => {
            this[key] = response;
        })
        .catch(error => {
            //Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            //this.$router.go(-1);
        });
    },
  }
}
</script>