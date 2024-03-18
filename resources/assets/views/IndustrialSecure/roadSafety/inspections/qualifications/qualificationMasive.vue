<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="CONFIGURACIÓN CALIFICACIÓN MASIVA"
      url="roadSafety-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-qualification-masive-component
            v-if="load"
                url="/industrialSecurity/roadsafety/inspection/saveConfigurationMasive"
                method="POST"
                :configuration="data"
                :optionQualification="optionQualification"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormQualificationMasiveComponent from '@/components/IndustrialSecure/RoadSafety/Inspections/Qualifications/FormQualificationMasiveComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'roadSafety-inspections-qualifiation-masive',
  metaInfo: {
    title: 'Configuración calificación masiva'
  },
  components:{
    FormQualificationMasiveComponent
  },
  data(){
    return {
      data: {
        options: []
      },
      optionQualification: [],
      load: false
    }
  },
  created(){
    this.fetchSelect('optionQualification', '/selects/qualificationMasiveInspection')

    axios.post('/industrialSecurity/roadsafety/inspection/getConfigurationMasive')
    .then(response => {
        this.data.options = response.data.data;
        this.load = true
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
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
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    },
  }
}
</script>
