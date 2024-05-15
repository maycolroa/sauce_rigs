<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="EXPORTACIÓN MASIVA"
      url="industrialsecure-dangermatrix"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 500px">
          <div>
            <form-export-masive
              url="/industrialSecurity/dangersMatrix/downloadMasive"
              method="POST"
              :cancel-url="{ name: 'industrialsecure-dangermatrix'}"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormExportMasive from '@/components/IndustrialSecure/DangerMatrix/FormExportMasiveComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'danger-matrix-export-masive',
  metaInfo: {
    title: 'Matriz de peligros - Exportacion Masiva'
  },
  components:{
    FormExportMasive,
    Loading
  }
}
</script>
