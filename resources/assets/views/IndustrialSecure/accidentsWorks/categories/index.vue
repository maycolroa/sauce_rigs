<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="ADMINISTRAR CATEGORIAS"
      url="industrialsecure-accidentswork-index-causes"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['accidentsWork_c']">
            <b-btn :to="{name:'industrialsecure-accidentswork-causes-categories-create'}" variant="primary">Crear Categoria</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-accidents-causes-categories"
                v-if="auth.can['accidentsWork_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'accidentsWork-causes-categories',
  metaInfo: {
    title: 'Causas - Categorias'
  },
  data () {
    return {
      //filters: []
    }
  },
  methods: {
    /*setFilters(value)
    {
      this.filters = value
    },
    exportData()
    {
      axios.post('/industrialSecurity/accidents/export', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        if (error.response.status == 500 && error.response.data.error != 'Internal Error')
        {
          Alerts.error('Error', error.response.data.error);
        }
        else 
        {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        }
      });
    },*/
  }
}
</script>
