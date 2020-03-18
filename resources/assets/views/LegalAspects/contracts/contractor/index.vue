<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="ADMINISTRAR CONTRATISTAS"
        url="legalaspects-contracts"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['contracts_c']"> 
            <b-btn :to="{name:'legalaspects-contractor-create'}" variant="primary">Crear contratista o arrendatario</b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['contracts_c']"> 
            <b-btn :to="{name:'legalaspects-contractor-list-check-validation'}" variant="primary">Configurar Lista de Chequeo</b-btn>
          </div>
          <div class="card-title-elements ml-md-auto" v-if="auth.can['contracts_export']">
            <b-dd variant="default" :right="isRTL">
              <template slot="button-content">
                <span class='fas fa-cogs'></span>
              </template>
              <b-dd-item @click="exportContracts()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
            </b-dd>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['contracts_r']"
                configName="legalaspects-contractor"
                @filtersUpdate="setFilters"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-contractor',
  metaInfo: {
    title: 'Lista de Contratistas'
  },
  data () {
    return {
      filters: []
    }
  },
  methods: {
    setFilters(value)
    {
      this.filters = value
    },
    exportContracts() {
      axios.post('/legalAspects/contracts/export', this.filters)
        .then(response => {
          Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
        }).catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    }
  }
}
</script>
