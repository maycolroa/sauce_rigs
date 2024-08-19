<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="IMPORTAR SEGURIDAD SOCIAL"
      url="legalaspects-contracts-employees"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <div>
            <form-import-element-balance-inicial
              url="/legalAspects/employeeContract/importSocialSecure"
              method="POST"
              :cancel-url="{ name: 'legalaspects-contracts-employees'}"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormImportElementBalanceInicial from '@/components/LegalAspects/Contracts/Employees/FormImportMassiveSocialSecureComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'legalaspects-contracts-employee-social-secures',
  metaInfo: {
    title: 'Empleados - Seguridad social'
  },
  components:{
    FormImportElementBalanceInicial,
    Loading
  }
}
</script>
