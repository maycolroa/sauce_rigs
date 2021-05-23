<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="IMPORTAR INSPECCIONES PLANEADAS"
      url="dangerousconditions-inspections"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <div>
            <form-import
              url="/industrialSecurity/dangerousConditions/inspection/import"
              method="POST"
              regionals-data-url="/selects/regionals"
              headquarters-data-url="/selects/headquarters"
              areas-data-url="/selects/areas"
              processes-data-url="/selects/processes"
              :cancel-url="{ name: 'dangerousconditions-inspections'}"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormImport from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormImportComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'dangerousconditions-inspections-create',
  metaInfo: {
    title: 'Inspecciones Planeadas - Importar'
  },
  components:{
    FormImport,
    Loading
  },
  data(){
    return {
      form: ''
    }
  }
}
</script>
