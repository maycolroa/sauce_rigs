<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Planes de Acción
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements ml-md-auto" v-if="auth.can['actionPlans_export']">
            <b-dd variant="default" :right="isRTL">
              <template slot="button-content">
                <span class='fas fa-cogs'></span>
              </template>
              <b-dd-item @click="exportActivities()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
            </b-dd>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-actionplans"
                @filtersUpdate="setFilters"
                v-if="auth.can['actionPlans_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'actionplans',
  metaInfo: {
    title: 'Planes de Acción'
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
    exportActivities() {
      axios.post('/administration/actionplan/export', this.filters)
        .then(response => {
          Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
        }).catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    }
  }
}
</script>
