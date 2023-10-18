<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="CREAR CATEGORIAS"
      url="industrialsecure-accidentswork-causes-categories"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-category
                url="/industrialSecurity/causes/categories"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-accidentswork-causes-categories'}"
                />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormCategory from '@/components/IndustrialSecure/AccidentsWork/Causes/FormCategoryComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'accidentsWork-causes-categories-create',
  metaInfo: {
    title: 'Causas Categorias - Crear'
  },
  components:{
    FormCategory
  },
  data(){
    return {
    }
  }
}
</script>
