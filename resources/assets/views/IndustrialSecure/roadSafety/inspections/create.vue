<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="CREAR INSPECCIÓN PLANEADA"
      url="roadSafety-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection
                url="/industrialSecurity/roadSafety/inspection"
                method="POST"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                areas-data-url="/selects/areas"
                processes-data-url="/selects/processes"
                :typesInspection="typesInspection"
                :cancel-url="{ name: 'roadSafety-inspections' }"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormInspection from '@/components/IndustrialSecure/RoadSafety/Inspections/FormInspectionComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'roadSafety-inspections-create',
  metaInfo: {
    title: 'Inspecciones Planeadas - Crear'
  },
  components:{
    FormInspection
  },
  data(){
    return {
      typesInspection: []
    }
  },
  created(){
    this.fetchSelect('typesInspection', '/selects/industrialSecurity/inspectionType')
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
