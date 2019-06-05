<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Normas /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-law-component
                url="/legalAspects/legalMatrix/law"
                method="POST"
                :cancel-url="{ name: 'legalaspects-lm-law'}"
                :apply-systems="applySystems"
                :years="years"
                lawTypeDataUrl="/selects/legalMatrix/lawsTypes"
                riskAspectDataUrl="/selects/legalMatrix/riskAspects"
                entityDataUrl="/selects/legalMatrix/entities"
                sstRiskDataUrl="/selects/legalMatrix/sstRisks"
                :repealed="repealed"/>
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
      applySystems: [],
      years: [],
      repealed: []
    }
  },
  created(){
    this.fetchSelect('applySystems', '/selects/legalMatrix/applySystem')
    this.fetchSelect('years', '/selects/legalMatrix/years')
    this.fetchSelect('repealed', '/selects/legalMatrix/repealed')
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
