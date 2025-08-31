<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="ADMINISTRAR CASOS"
      url="preventiveoccupationalmedicine-reinstatements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['reinc_checks_c']" :to="{name:'reinstatements-checks-create'}" variant="primary">Crear Caso</b-btn>
            <b-btn v-if="auth.can['reinc_checks_export']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
            <b-btn v-if="!auth.hasRole['Rol Visor Reincorporaciones']" :to="{name:'reinstatements-checks-letter-regenerate'}" variant="primary">Consulta de Cartas</b-btn>
            <!--<b-btn v-if="auth.hasRole['Superadmin']" variant="primary" @click="importMessage()" v-b-tooltip.top title="Importar"><i class="fas fa-upload"></i></b-btn>-->
            <input id="fileInputImport" type="file" style="display:none" v-on:input="importDangerMatrix"/>
          </div>
        </b-card-header>
        <b-card-body>
          <div>
              <filter-reinc-checks 
                  v-model="filters" 
                  configName="reinstatements-checks" 
                  :isDisabled="isLoading"/>
          </div>
          <div>
            <vue-table
              ref="tableCheck"
              configName="reinstatements-checks"
              :customColumnsName="true"
              v-if="auth.can['reinc_checks_r']"
              :params="{filters: filters}"
              ></vue-table>
          </div>
        </b-card-body>
    </b-card>
    </div>

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
import FilterReincChecks from '@/components/Filters/FilterReincChecks.vue';

export default {
  name: 'reinstatements-checks',
  metaInfo: {
    title: 'Casos'
  },
  components:{
      FilterReincChecks
  },
  data () {
    return {
      filters: [],
      isLoading: false,
    }
  },
  watch: {
    filters: {
      handler(val){
        this.fetch()
      },
      deep: true
    }
  },
  methods: {
    fetch()
    {
      this.$refs.tableCheck.refresh()
    },
    exportData(){
      axios.post('/biologicalmonitoring/reinstatements/check/export', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    importDangerMatrix(e){
      var formData = new FormData();
      var imagefile = e.target.files;

      formData.append("file", imagefile[0]);
      axios.post('/biologicalmonitoring/reinstatements/check/importCie11', formData, {
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
  }
}
</script>
