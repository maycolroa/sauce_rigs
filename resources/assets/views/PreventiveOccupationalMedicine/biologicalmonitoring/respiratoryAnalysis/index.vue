<template>
  <div>
    <header-module
      title="ANÁLISIS RESPIRATORIO"
      subtitle="ADMINISTRAR ANÁLISIS"
      url="biologicalmonitoring-menu"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['biologicalMonitoring_respiratoryAnalysis_c']" variant="primary" href="/templates/respiratoryAnalysisimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>
            <b-btn v-if="auth.can['biologicalMonitoring_respiratoryAnalysis_c']" variant="primary" @click="importMessage()" v-b-tooltip.top title="Importar"><i class="fas fa-upload"></i></b-btn>
            <b-btn v-if="auth.can['biologicalMonitoring_respiratoryAnalysis_r']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
            <input id="fileInputImport" type="file" style="display:none" v-on:input="importFile"/>
            <b-btn v-if="auth.can['biologicalMonitoring_respiratoryAnalysis_c']" :to="{name:'biologicalmonitoring-respiratoryanalysis-informs'}" variant="primary">Ver Informes</b-btn>
            <b-btn v-if="auth.can['biologicalMonitoring_respiratoryAnalysis_c']" :to="{name:'biologicalmonitoring-respiratoryanalysis-reportindividual'}" variant="primary">Reporte Individual</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="biologicalmonitoring-respiratoryAnalysis"
                v-if="auth.can['biologicalMonitoring_respiratoryAnalysis_r']"
                @filtersUpdate="setFilters"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>

    <!-- modal confirmation for import -->
    <b-modal ref="modalConfirmationImport" class="modal-slide" hide-header hide-footer>
      <p class="text-justific mb-4">
        Estimado Usuario para realizar la importación el archivo debe cumplir lo siguiente:<br><br>

        <ol>
          <li>Formato excel (*.xlsx).</li>
          <li>Incluir las cabeceras de los campos en la primera fila del documento.</li>
          <li>Solo se leera la primera hoja del documento (En caso de tener mas de una).</li>
        </ol>

      </p>
      <b-btn block variant="primary" @click="importConfirmation()">Aceptar</b-btn>
      <b-btn block variant="default" @click="toggleModalConfirmationImport(false)">Cancelar</b-btn>
    </b-modal>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'biologicalmonitoring-respiratoryAnalysis',
  metaInfo: {
    title: 'Análisis Respiratorio'
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
    exportData(){
      axios.post('/biologicalmonitoring/respiratoryAnalysis/export', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    importFile(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post('/biologicalmonitoring/respiratoryAnalysis/import', formData, {
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
    importMessage() {
      this.toggleModalConfirmationImport(true)
    },
    importConfirmation() {
      this.toggleModalConfirmationImport(false);
      document.getElementById('fileInputImport').click()
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
