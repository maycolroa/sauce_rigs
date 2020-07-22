<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="CREAR INSPECCIÓN"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection
                url="/industrialSecurity/dangerousConditions/inspection"
                method="POST"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                areas-data-url="/selects/areas"
                processes-data-url="/selects/processes"
                :inspection="data"
                :cancel-url="{ name: 'dangerousconditions-inspections' }"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormInspection from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormInspectionComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangerousconditions-inspections-create',
  metaInfo: {
    title: 'Inspecciones - Clonar'
  },
  components:{
    FormInspection
  },
  data(){
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/dangerousConditions/inspection/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        delete this.data.id
        this.data.themes.map((theme) => {
          delete theme.id
          delete theme.inspection_id

          theme.items.map((item) => {
            delete item.id
            delete item.inspection_section_id
            return item;
          });

          return theme;
        });
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
}
</script>
