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
          <loading-block v-if="!ready"/>
          <form-law-qualify-component
            v-if="ready"
            :url="`/legalAspects/legalMatrix/law/${this.$route.params.id}`"
            method="PUT"
            :law="data"
            :qualifications="qualifications"
            :action-plan-states="actionPlanStates"
            :si-no="siNo"
            :filter-interests-options="filterInterestsOptions"
            :is-edit="isEdit"
            :view-only="viewOnly"
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
      articles: [],
      qualifications: [],
      actionPlanStates: [],
      siNo: [],
      filterInterestsOptions: [],
      isEdit: false,
      viewOnly: true,
      ready: false
    }
  },
  created(){
    this.isEdit = this.auth.can['laws_qualify']
    this.viewOnly = this.auth.can['laws_qualify'] ? false : true;

    axios.get(`/legalAspects/legalMatrix/law/qualify/${this.$route.params.id}`)
    .then(response => {
      this.data = response.data.data;

      axios.post('/selects/legalMatrix/filterInterests', { id: this.$route.params.id })
      .then(response => {
        this.filterInterestsOptions = response.data;

        GlobalMethods.getDataMultiselect('/selects/legalMatrix/articlesQualifications')
        .then(response => {
          this.qualifications = response;

          GlobalMethods.getDataMultiselect('/selects/actionPlanStates')
          .then(response => {
            this.actionPlanStates = response;

            GlobalMethods.getDataMultiselect('/selects/siNo')
            .then(response => {
              this.siNo = response;
              this.ready = true;
            })
            .catch(error => {
            });
          })
          .catch(error => {
          });
        })
        .catch(error => {
        });
      })
      .catch(error => {
      });
    })
    .catch(error => {
    });
  }
}
</script>