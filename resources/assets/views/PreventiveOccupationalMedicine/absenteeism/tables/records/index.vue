<template>
  <div>
    <header-module
      title="AUSENTISMO"
      :subtitle="`ADMINISTRAR DATOS - ${table.name ? table.name : ''}`"
      url="absenteeism-tables"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['absen_tables_c']">
            <b-btn :to="{name:'absenteeism-tables-records-create'}" variant="primary">Crear Dato</b-btn>
          </div>

          <div class="card-title-elements" v-if="auth.can['absen_tables_c']">
            <b-btn variant="primary" :href="`/templates/absenteeismTableRecordsImport/${table.id}`" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>
          </div>

          <div class="card-title-elements" v-if="auth.can['absen_tables_c']">
            <b-btn variant="primary" @click="importMessage()" v-b-tooltip.top title="Importar"><i class="fas fa-upload"></i></b-btn>
            <input id="fileInputImport" type="file" style="display:none" v-on:input="importContract"/>
          </div>

          <div class="card-title-elements ml-md-auto" v-if="auth.can['absen_tables_r']">
            <b-dd variant="default" :right="isRTL">
              <template slot="button-content">
                <span class='fas fa-cogs'></span>
              </template>
              <b-dd-item @click="exportData()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
            </b-dd>
          </div>

        </b-card-header>
        <b-card-body>
          <vue-table
            configName="absenteeism-tables-records"
            :customColumnsName="true" 
            :params="{tableId: `${this.$route.params.table}`}"
            v-if="auth.can['absen_tables_r']"
          ></vue-table>
        </b-card-body>

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

    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-tables-records',
  metaInfo: {
    title: 'Datos'
  },
  data(){
    return {
      table: {}
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/absenteeism/tables/${this.$route.params.table}`)
    .then(response => {
      this.table = response.data.data
    })
    .catch(error => {
      Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
  methods: {
    exportData(){
      axios.post(`/biologicalmonitoring/absenteeism/tableRecords/export/${this.$route.params.table}`)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    importMessage() {
      this.toggleModalConfirmationImport(true)
    },
    toggleModalConfirmationImport(toggle) {
      if (toggle)
        this.$refs.modalConfirmationImport.show()
      else
        this.$refs.modalConfirmationImport.hide();
    },
    importConfirmation() {
      this.toggleModalConfirmationImport(false);
      document.getElementById('fileInputImport').click()
    },
    importContract(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post(`/biologicalmonitoring/absenteeism/tableRecords/import/${this.$route.params.table}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
      })
      .then(response => {
        Alerts.warning('Información', 'Se inicio la importación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    }
  }
}
</script>
