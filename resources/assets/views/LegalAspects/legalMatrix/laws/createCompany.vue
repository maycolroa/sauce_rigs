<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="CREAR NORMA"
      url="legalaspects-lm-law-company"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-law-component
                url="/legalAspects/legalMatrix/law"
                method="POST"
                :custom="true"
                :years="years"
                riskAspectDataUrl="/selects/legalMatrix/riskAspects"
                entityDataUrl="/selects/legalMatrix/entitiesCompany"
                lawTypeDataUrl="/selects/legalMatrix/lawsTypes"
                sstRiskDataUrl="/selects/legalMatrix/sstRisks"
                urlDataInterests="/selects/legalMatrix/interests"
                systemApplyUrl="/selects/legalMatrix/systemApplyCompany"
                :repealed="repealed"
                :si-no="siNo"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLawComponent from '@/components/LegalAspects/LegalMatrix/Law/FormLawComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-lm-law-create',
  metaInfo: {
    title: 'Normas - Crear'
  },
  components:{
    FormLawComponent
  },
  data(){
    return {
      years: [],
      repealed: [],
      siNo: []
    }
  },
  created(){
    this.fetchSelect('years', '/selects/legalMatrix/years')
    this.fetchSelect('repealed', '/selects/legalMatrix/repealed')
    this.fetchSelect('siNo', '/radios/siNo')
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
