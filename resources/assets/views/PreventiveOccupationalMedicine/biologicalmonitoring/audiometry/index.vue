<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Audiometrias
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'biologicalmonitoring-audiometry-create'}" variant="primary">Crear Audiometria</b-btn>
            <b-btn :to="{name:'biologicalmonitoring-audiometry-report-pta'}" variant="primary">Ver Informes</b-btn>
          </div>
          <div class="card-title-elements ml-md-auto">
            <b-dd variant="default" :right="isRTL">
            <template slot="button-content">
              <span class='fas fa-cogs'></span>
            </template>
            <input id="fileInputImportAudiometry" type="file" style="display:none" v-on:input="importAudiometry"/>
            <b-dd-item onclick="document.getElementById('fileInputImportAudiometry').click()">
              <i class="fas fa-upload"></i> &nbsp;Importar
            </b-dd-item>
            <b-dd-item href="/templates/audiometryimport" target="blank"><i class="fas fa-file-alt"></i> &nbsp;Generar Plantilla</b-dd-item>
            <b-dd-divider></b-dd-divider>
            <b-dd-item @click="exportAudiometry()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
          </b-dd>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="biologicalmonitoring-audiometry"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'audiometry',
  metaInfo: {
    title: 'Audiometria'
  },

  methods: {
    exportAudiometry(){
      axios.post('/biologicalmonitoring/audiometry/export')
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    importAudiometry(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post('/biologicalmonitoring/audiometry/import', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
      })
      .then(response => {
        Alerts.warning('Información', 'Se inicio la importación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });;
    }
  },
}
</script>
