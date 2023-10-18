<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="VER CATEGORIA"
      url="industrialsecure-accidentswork-causes-categories"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
        <loading :display="!ready"/>
          <div v-if="ready">
            <form-category
                :category="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-accidentswork-causes-categories'}"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
    
  </div>
</template>
 
<script>
import FormCategory from '@/components/IndustrialSecure/AccidentsWork/Causes/FormCategoryComponent.vue';
import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'accidentsWork-causes-categories-view',
  metaInfo: {
    title: 'Causas Categorias - Ver'
  },
  components:{
    FormCategory,
    Loading
  },
  data () {
    return {
      data: [],
      ready: false,
    }
  },
  created(){
    axios.get(`/industrialSecurity/causes/categories/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.ready = true
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  }
}
</script>