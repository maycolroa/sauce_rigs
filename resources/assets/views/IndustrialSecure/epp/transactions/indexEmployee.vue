<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="TRANSACCIONES DE ELEMENTOS ENTREGAS"
      url="industrialsecure-epps-transactions-menu"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['transaction_c']"> 
            <b-btn :to="{name:'industrialsecure-epps-transactions-create'}" variant="primary">Crear Entrega</b-btn>&nbsp;&nbsp;
            <b-btn variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-epps-transactions"
                v-if="auth.can['transaction_r']"
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
  name: 'epps-transactions',
  metaInfo: {
    title: 'Transacciones'
  },
  methods: {
    setFilters(value)
    {
      this.filters = value
    },
    data () {
      return {
        filters: []
      }
    },
    exportData()
    {
      axios.post('/industrialSecurity/epp/transaction/delivery/export', this.filters)
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
    },
  }
}
</script>

