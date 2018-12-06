<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Audiometrias
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="viewIndex">
          <div class="card-title-elements">
            <b-btn :to="{name:'biologicalmonitoring-audiometry-create'}" variant="primary">Crear Audiometria</b-btn>
            <b-btn :to="{name:'biologicalmonitoring-audiometry-informs'}" variant="primary">Ver Informes</b-btn>
          </div>
          <div class="card-title-elements ml-md-auto">
            <b-dd variant="default" :right="isRTL">
            <template slot="button-content">
              <span class='fas fa-cogs'></span>
            </template>
            <input id="fileInputImportAudiometry" type="file" style="display:none" v-on:input="importAudiometry"/>
            <b-dd-item @click="importAudiometryMessage()">
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
                :configName="configNameTable"
                :viewIndex="viewIndex"
                :modelId="employeeId"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>

    <!-- modal confirmation for import -->
    <b-modal ref="modalConfirmationImport" class="modal-slide" hide-header hide-footer>
      <p class="text-justific mb-4">
        Estimado Usuario para realizar la importarción el archivo debe cumplir lo siguiente:<br><br>

        <ol>
          <li>Formato excel (*.xlsx).</li>
          <li>Incluir las cabeceras de los campos en la primera fila del documento.</li>
          <li>Solo se leera la primera hoja del documento (En caso de tener mas de una).</li>
        </ol>

      </p>
      <b-btn block variant="primary" @click="importAudiometryConfirmation()">Aceptar</b-btn>
      <b-btn block variant="default" @click="toggleModalConfirmationImport(false)">Cancelar</b-btn>
    </b-modal>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'audiometry',
  metaInfo: {
    title: 'Audiometria'
  },
  props: {
    viewIndex: { type: Boolean, default: true },
    employeeId: {type: [Number, String], default: null},
    configNameTable: {type: String, default: 'biologicalmonitoring-audiometry'},
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
    },
    importAudiometryMessage() {
      this.toggleModalConfirmationImport(true)
    },
    importAudiometryConfirmation() {
      this.toggleModalConfirmationImport(false);
      document.getElementById('fileInputImportAudiometry').click()
    },
    toggleModalConfirmationImport(toggle) {
      if (toggle)
        this.$refs.modalConfirmationImport.show()
      else
        this.$refs.modalConfirmationImport.hide();
    }
  },
}
</script>
