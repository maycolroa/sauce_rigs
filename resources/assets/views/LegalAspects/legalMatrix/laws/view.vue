<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="VER NORMA"
      url="legalaspects-lm-law"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-law-component
                :law="data"
                :view-only="true"
                :years="years"
                lawTypeDataUrl="/selects/legalMatrix/lawsTypes"
                riskAspectDataUrl="/selects/legalMatrix/riskAspects"
                entityDataUrl="/selects/legalMatrix/entities"
                sstRiskDataUrl="/selects/legalMatrix/sstRisks"
                urlDataInterests="/selects/legalMatrix/interests"
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
  name: 'legalaspects-lm-law-view',
  metaInfo: {
    title: 'Normas - Ver'
  },
  components:{
    FormLawComponent
  },
  data () {
    return {
      data: [],
      years: [],
      repealed: [],
      siNo: []
    }
  },
  created(){
    axios.get(`/legalAspects/legalMatrix/law/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        //Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        //this.$router.go(-1);
    });

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