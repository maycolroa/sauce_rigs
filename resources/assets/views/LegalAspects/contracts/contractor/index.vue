<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="ADMINISTRAR CONTRATISTAS"
        url="legalaspects-contracts"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['contracts_c']"> 
            <b-btn :to="{name:'legalaspects-contractor-create'}" variant="primary">Crear</b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['contracts_c']"> 
            <b-btn variant="primary" :to="{name:'legalaspects-contractor-list-check-validation'}" v-b-tooltip.top title="Configurar Lista de Chequeo"><i class="ion ion-ios-list-box"></i></b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['contracts_c']"> 
            <b-btn variant="primary" :to="{name:'legalaspects-contracts-documents'}" v-b-tooltip.top title="Configurar Documentos a solicitar"><i class="ion ion-md-document"></i></b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['contracts_c']">
            <b-btn variant="primary" href="/templates/contractimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['contracts_c']">
            <b-btn variant="primary" @click="importMessage()" v-b-tooltip.top title="Importar"><i class="fas fa-upload"></i></b-btn>
            <input id="fileInputImport" type="file" style="display:none" v-on:input="importContract"/>
          </div>
          <div class="card-title-elements ml-md-auto" v-if="auth.can['contracts_export']">
            <b-dd variant="default" :right="isRTL">
              <template slot="button-content">
                <span class='fas fa-cogs'></span>
              </template>
              <b-dd-item @click="exportContracts()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
            </b-dd>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['contracts_r']"
                configName="legalaspects-contractor"
                @filtersUpdate="setFilters"
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
  name: 'legalaspects-contractor',
  metaInfo: {
    title: 'Lista de Contratistas'
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
    exportContracts() {
      axios.post('/legalAspects/contracts/export', this.filters)
        .then(response => {
          Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
        }).catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    },
    importContract(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post('/legalAspects/contracts/import', formData, {
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
