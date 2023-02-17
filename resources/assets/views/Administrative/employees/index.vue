<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`ADMINISTRAR ${keywordCheck('employees')}`"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['employees_c']">
            <b-btn :to="{name:'administrative-employees-create'}" variant="primary">Crear {{ keywordCheck('employee') }}</b-btn>
            <b-btn v-if="auth.can['employees_c']" variant="primary" @click="importMessage()" v-b-tooltip.top title="Importar"><i class="fas fa-upload"></i></b-btn>
            <input id="fileInputImport" type="file" style="display:none" v-on:input="importAudiometry"/>
            <b-btn v-if="auth.can['employees_c']" variant="primary" href="/templates/employeeimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>
            <b-btn v-if="auth.can['employees_c']" variant="primary" @click="importMessage2()" v-b-tooltip.top title="Importar Inactividad"><i class="fas fa-upload"></i></b-btn>
            <input id="fileInputImport2" type="file" style="display:none" v-on:input="importAudiometry2"/>
            <b-btn v-if="auth.can['employees_c']" variant="primary" href="/templates/employeeinactiveimport" target="blank" v-b-tooltip.top title="Generar Plantilla Inactivación"><i class="fas fa-file-alt"></i></b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-employees"
                :customColumnsName="true"
                v-if="auth.can['employees_r']"
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

     <!-- modal confirmation for import -->
    <b-modal ref="modalConfirmationImport2" class="modal-slide" hide-header hide-footer>
      <p class="text-justific mb-4">
        Estimado Usuario para realizar la importación el archivo debe cumplir lo siguiente:<br><br>

        <ol>
          <li>Formato excel (*.xlsx).</li>
          <li>Incluir las cabeceras de los campos en la primera fila del documento.</li>
          <li>Solo se leera la primera hoja del documento (En caso de tener mas de una).</li>
        </ol>

      </p>
      <b-btn block variant="primary" @click="importConfirmation2()">Aceptar</b-btn>
      <b-btn block variant="default" @click="toggleModalConfirmationImport2(false)">Cancelar</b-btn>
    </b-modal>

  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'employees',
  metaInfo() {
    return {
      title: `${this.keywordCheck('employees')}`
    }
  },
  methods: {
    importAudiometry(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post('/administration/employee/import', formData, {
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
    },
    importAudiometry2(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post('/administration/employee/importInactive', formData, {
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
    importMessage2() {
      this.toggleModalConfirmationImport2(true)
    },
    importConfirmation2() {
      this.toggleModalConfirmationImport2(false);
      document.getElementById('fileInputImport2').click()
    },
    toggleModalConfirmationImport2(toggle) {
      if (toggle)
        this.$refs.modalConfirmationImport2.show()
      else
        this.$refs.modalConfirmationImport2.hide();
    }
  },
}
</script>
