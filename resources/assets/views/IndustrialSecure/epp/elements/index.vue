<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="ADMINISTRAR ELEMENTOS"
      url="industrialsecure-epp"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['elements_c']"> 
            <b-btn :to="{name:'industrialsecure-epps-elements-create'}" variant="primary">Crear Elemento</b-btn>&nbsp;&nbsp;
          </div>
          <b-btn v-if="auth.can['elements_c']" variant="primary" href="/templates/elementimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>&nbsp;&nbsp;
          <b-btn v-if="auth.can['elements_c']" variant="primary" @click="importMessage()" v-b-tooltip.top title="Importar"><i class="fas fa-upload"></i></b-btn>&nbsp;&nbsp;
          <input id="fileInputImport" type="file" style="display:none" v-on:input="importElement"/>
          <!--<b-btn :to="{name:'industrialsecure-epps-elements-import-balance-inicial'}" v-if="auth.can['elements_c']" variant="primary" v-b-tooltip.top title="Importar Saldos Iniciales"><i class="fas fa-cloud-upload-alt"></i></b-btn>
          <input id="fileInputImport2" type="file" style="display:none" v-on:input="    importElementBalance"/>-->
          <!--<div class="card-title-elements" v-if="auth.can['transaction_r']"> 
            <b-btn :to="{name:'industrialsecure-epps-transactions-menu'}" variant="primary">Transacciones</b-btn>&nbsp;&nbsp;
          </div>
          <div class="card-title-elements ml-md-auto" v-if="auth.can['elements_r']">
            <b-dd variant="default" :right="isRTL">
              <template slot="button-content">
                <span class='fas fa-cogs'></span>
              </template>
              <b-dd-item href="/templates/elementnotidentimport" target="blank"><i class="fas fa-download"></i> &nbsp;Plantilla Elementos sin identificar</b-dd-item>
              <b-dd-item href="/templates/elementidentimport" target="blank"><i class="fas fa-download"></i> &nbsp;Plantilla Elementos identicados</b-dd-item>
            </b-dd>
          </div>-->
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-epps-elements"
                v-if="auth.can['elements_r']"
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
  name: 'epps-elements',
  metaInfo: {
    title: 'Elementos'
  },
  methods: {
    importElement(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post('/industrialSecurity/epp/element/import', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
      })
      .then(response => {
        Alerts.warning('Información', 'Se inicio la importación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });    
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
