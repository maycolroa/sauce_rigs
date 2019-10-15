<template>
  <div>
    <header-module
      v-if="viewIndex"
      title="AUDIOMETRIAS"
      subtitle="ADMINISTRAR AUDIOMETRIAS"
      url="preventiveoccupationalmedicine"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="viewIndex">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['biologicalMonitoring_audiometry_c']" :to="{name:'biologicalmonitoring-audiometry-create'}" variant="primary">Crear Audiometria</b-btn>
            <b-btn v-if="auth.can['biologicalMonitoring_audiometry_informs_r']" :to="{name:'biologicalmonitoring-audiometry-informs'}" variant="primary">Ver Informes</b-btn>
            <b-btn v-if="auth.can['biologicalMonitoring_audiometry_inform_individual_r']" :to="{name:'biologicalmonitoring-audiometry-informs-individual'}" variant="primary">Ver Informe Individual</b-btn>
            <b-btn v-if="auth.can['biologicalMonitoring_audiometry_c']" variant="primary" @click="importAudiometryMessage()" v-b-tooltip.top title="Importar"><i class="fas fa-upload"></i></b-btn>
            <input id="fileInputImportAudiometry" type="file" style="display:none" v-on:input="importAudiometry"/>
            <b-btn v-if="auth.can['biologicalMonitoring_audiometry_c']" variant="primary" href="/templates/audiometryimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>
            <b-btn v-if="auth.can['biologicalMonitoring_audiometry_r']" variant="primary" @click="exportAudiometry()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                :configName="configNameTable"
                :viewIndex="viewIndex"
                :modelId="employeeId"
                @filtersUpdate="setFilters"
                v-if="auth.can['biologicalMonitoring_audiometry_r']"
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
    exportAudiometry(){
      axios.post('/biologicalmonitoring/audiometry/export', this.filters)
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
