<template>
  <div>
    <header-module
      v-if="!modal"
      title="SISTEMA"
      subtitle="CREAR GRUPO DE COMPAÑIA"
      url="system-companygroup"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-company
                url="/system/companyGroup"
                method="POST"
                :cancel-url="{ name: 'system-companygroup'}"
                :modal="modal"
                :si-no="siNo"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormCompany from '@/components/System/CompanyGroup/FormCompanyComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'system-companygroup-create',
  metaInfo: {
    title: 'Grupo de Compañias - Crear'
  },
  components:{
    FormCompany
  },
  props: {
    modal: { type: Boolean, default: false },
  },
  data(){
    return {
      siNo: []
    }
  },
  created(){
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
