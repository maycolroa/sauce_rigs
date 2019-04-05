<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Evaluaciones
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-evaluations-create'}" variant="primary">Crear Evaluación</b-btn>
          </div>
          <div class="card-title-elements ml-md-auto">
            <b-dd variant="default" :right="isRTL">
              <template slot="button-content">
                <span class='fas fa-cogs'></span>
              </template>
              <b-dd-item @click="exportEvaluations()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
            </b-dd>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-evaluations"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'evaluations',
  metaInfo: {
    title: 'Evaluaciones'
  },
  methods: {
    exportEvaluations() {
      axios.post('/legalAspects/evaluation/export')
        .then(response => {
          Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
        }).catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    }
  }
}
</script>
