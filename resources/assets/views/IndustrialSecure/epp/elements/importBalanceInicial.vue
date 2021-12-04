<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="IMPORTA SALDO INICIAL ELEMENTO"
      url="industrialsecure-epps-elements"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <div>
            <form-import-element-balance-inicial
              url="/industrialSecurity/epp/element/import/balanceInicial"
              method="POST"
              :id="id"
              :cancel-url="{ name: 'industrialsecure-epps-elements'}"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormImportElementBalanceInicial from '@/components/IndustrialSecure/Epp/FormImportElementBalanceInicialComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'administrative-users-create',
  metaInfo: {
    title: 'Usuarios - Importar'
  },
  components:{
    FormImportElementBalanceInicial,
    Loading
  },
  data(){
    return {
      id: this.$route.params.id,
      form: ''
    }
  }
}
</script>
