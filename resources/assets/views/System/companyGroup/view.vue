<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="VER GRUPO DE COMPAÑIA"
      url="system-companygroup"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-company
                :companies-group="data"
                :view-only="true"
                :si-no="siNo"
                :cancel-url="{ name: 'system-companygroup'}"/>
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
  name: 'system-companygroup-view',
  metaInfo: {
    title: 'Compañias - Ver'
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