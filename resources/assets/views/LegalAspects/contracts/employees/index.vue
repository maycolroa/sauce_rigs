<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ADMINISTRAR EMPLEADOS"
      :url="(this.$route.params.id ? 'legalaspects-contractor' : 'legalaspects-contracts')"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['contracts_employee_c']">
            <b-btn :to="{name:'legalaspects-contracts-employees-create'}" variant="primary">Crear Empleado</b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['contracts_employee_c']">
            <b-btn variant="primary" href="/templates/contractemployeeimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['contracts_employee_c']">
            <b-btn variant="primary" @click="importMessage()" v-b-tooltip.top title="Importar"><i class="fas fa-upload"></i></b-btn>
            <input id="fileInputImport" type="file" style="display:none" v-on:input="importContractEmployee"/>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-contracts-employees"
                :customColumnsName="true" 
                v-if="auth.can['contracts_employee_r']"
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
  name: 'employees',
  metaInfo: {
    title: 'Empleados'
  },
  methods: {
    importContractEmployee(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post('/legalAspects/employeeContract/import', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
      })
      .then(response => {
        Alerts.warning('Información', 'Se inicio la importación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });    },
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
  }
}
</script>
