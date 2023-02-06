<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="EDITAR GRUPO DE COMPAÑIA"
      url="system-companygroup"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-company
                :url="`/system/companyGroup/${this.$route.params.id}`"
                method="PUT"
                :companies-group="data"
                :si-no="siNo"
                :is-edit="true"
                :cancel-url="{ name: 'system-companygroup'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormCompany from '@/components/System/CompanyGroup/FormCompanyComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-companygroup-edit',
  metaInfo: {
    title: 'Grupo de Compañias - Editar'
  },
  components:{
    FormCompany
  },
  data () {
    return {
      data: [],
      siNo: []
    }
  },
  created(){
    this.fetchSelect('siNo', '/radios/siNo')
    axios.get(`/system/companyGroup/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
  methods: {
      fetchSelect(key, url)
      {
          GlobalMethods.getDataMultiselect(url, {id: this.$route.params.id})
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