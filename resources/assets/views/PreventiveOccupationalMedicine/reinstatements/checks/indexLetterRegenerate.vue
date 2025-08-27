<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="ADMINISTRAR CARTAS"
      url="reinstatements-checks"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'reinstatements-checks'}" variant="default"> Regresar</b-btn>
            <b-btn v-if="auth.hasRole['Superadmin'] && auth.company_id == 669" variant="primary" @click="importMessage()" v-b-tooltip.top title="Importar"><i class="fas fa-upload"></i></b-btn>
            <input id="fileInputImport" type="file" style="display:none" v-on:input="importLetterHistory"/>
          </div>
        </b-card-header>
        <b-card-body>
          <div>
            <vue-table
              configName="reinstatements-letter-recommendations"
              v-if="auth.can['reinc_checks_r']"
              ></vue-table>
          </div>
        </b-card-body>
    </b-card>

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
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import FilterReincChecks from '@/components/Filters/FilterReincChecks.vue';

export default {
  name: 'reinstatements-checks-letter-regenerate',
  metaInfo: {
    title: 'Cartas'
  },
  components:{
      FilterReincChecks
  },
  data () {
    return {
      isLoading: false,
    }
  },
  watch: {},
  methods: {
    importLetterHistory(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post('/biologicalmonitoring/reinstatements/importLetter', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
      })
      .then(response => {        
        document.getElementById('fileInputImport').value = ''
        Alerts.warning('Información', 'Se inicio la importación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {        
        document.getElementById('fileInputImport').value = ''
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
