<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="CREAR NORMA"
      url="legalaspects-lm-law"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-law-component
                url="/legalAspects/legalMatrix/law"
                method="POST"
                :years="years"
                lawTypeDataUrl="/selects/legalMatrix/lawsTypes"
                riskAspectDataUrl="/selects/legalMatrix/riskAspects"
                entityDataUrl="/selects/legalMatrix/entities"
                sstRiskDataUrl="/selects/legalMatrix/sstRisks"
                urlDataInterests="/selects/legalMatrix/interestsSystem"
                systemApplyUrl="/selects/legalMatrix/systemApplySystem"
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
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });
    },
  }
}
</script>
