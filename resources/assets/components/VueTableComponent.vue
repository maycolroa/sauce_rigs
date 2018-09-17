<template>
  <div>
    <v-server-table :url="config.configuration.urlData" :columns="columns" :options="options" ref="vuetable">
      <template slot="controlls" slot-scope="props">
        <div>
          
          <b-btn v-for="(button, index) in controllsPush" :key="index" 
          :variant="button.config.color + ' ' + (button.config.borderless ? 'borderless' : '') + ' ' + (button.config.icon ? 'icon-btn' : '')" 
          class="btn-xs"
          @click.prevent="pushButton(button,props.row)"><i :class="button.config.icon"></i></b-btn>
          
          <b-btn v-if="controllsBase.includes('delete')" variant="outline-danger borderless icon-btn" class="btn-xs" @click.prevent="confirmRemove(props.row)"><i class="ion ion-md-close"></i></b-btn>
        </div>
      </template>
      <template :slot="details.length > 0 ? 'child_row': null" slot-scope="props">
        <b-row class="mx-0">
        <b-col cols="6" v-for="(field, index) in details" :key="index">
          <b>{{field.title}}:</b> {{props.row[field.data]}}
        </b-col>
        </b-row>
      </template>
    </v-server-table>

    <!-- modal confirmation for delete -->
    <b-modal ref="modalConfirmationRemove" class="modal-slide" hide-header hide-footer>
      <p class="text-center text-big mb-4">
        {{messageConfirmationRemove}}.
      </p>
      <b-btn block variant="primary" @click="remove()">Aceptar</b-btn>
      <b-btn block variant="default" @click="hideModalConfirmationRemove()">Cancelar</b-btn>
        <small class="text-muted">Esta accion no se puede deshacer.</small>
    </b-modal>
  </div>
</template>
<style scope>
  .table-with-detail thead th:not(:first-of-type) {
    min-width: 100px;
  }
  .table-without-detail thead th {
    min-width: 100px;
  }
</style>

<style src="@/vendor/libs/vue-data-tables/vue-data-tables.scss" lang="scss"></style>

<script>
import Vue from 'vue'
import {ServerTable, ClientTable, Event} from 'vue-tables-2';
import VueTableConfig from '@/vuetableconfig/';
import Alerts from '@/utils/Alerts.js';

Vue.use(ServerTable)

export default {
  name: 'vue-table',
  props:{
    configName: {type: String, required: true},
    config: {type: Object, default: function(){
      return VueTableConfig.get(this.configName);
    }},
  },
  data(){
    return{
      messageConfirmationRemove:'',
      actionRemove:''
    }
  },
  computed: {
    columns(){
      let columns = this.config.fields.filter((f) => {
        return !f.detail && !f.key;
      });
      return columns.map((f) => {
        return f.data;
      });
    },
    options(){
      let options = {
        pagination: { chunk: 5 },
        perPage: 10,
        perPageValues: [],
        serverMultiSorting: false,
        sortIcon: {
          is: 'fa-sort',
          base: 'fas',
          up: 'fa-sort-up',
          down: 'fa-sort-down'
        },
        skin: 'table table-striped table-bordered table-hover table-sm',
        requestFunction: function (data) {
          return axios.post(this.url, data)
          .catch(function (e) {
              this.dispatch('error', e);
          }.bind(this));
        },
        texts:{
          count:"Mostrando del {from} al {to} de {count} registros|{count} registros|Un registro",
          first:'Primero',
          last:'Último',
          filter:"Filtro:",
          filterPlaceholder:"Consulta buscada",
          page:"Pagina:",
          noResults:"Sin registros coincidentes",
          filterBy:"Filtrar por {column}",
          loading:'Cargando...',
          defaultOption:'Select {column}',
          columns:'Columnas'
        },
      };

      var fields = this.config.fields;

      var fieldsFilter = fields.filter((f) => {
          return f.searchable;
      });

      //Define columns searchable in vuetable requestAdapter
      options.requestAdapter = function(data){
        data.fields = fieldsFilter.map((f) => {
          return f.name;
        });
        return data;
      };

      //define columns  filterable for input by column when filterbycolumn is true
      options.filterable = fieldsFilter.map((f) => {
        return f.data;
      });
      
      //define header columns
      options.headings= {};
      fields.map((f) => {
          options.headings[f.data] = f.title;
      });

      //set prop filter conlumns in opions vuetable
      options.filterByColumn = this.config.configuration.filterColumns;

      //define unique key column
      options.uniqueKey = fields.filter((f) => {
        return f.key;
      })[0].data;

      options.sortable = fields.filter((f) => {
        return f.sortable;
      }).map((f) => {
        return f.data;
      });

      let countdetail = fields.filter((f) => {
        return f.detail;
      }).length;
       options.skin += countdetail > 0 ?  " table-with-detail": " table-without-detail";

      return options;
    },

    details(){
      let fieldsDetail =  this.config.fields.filter((f) => {
        return f.detail;
      });
      
      return fieldsDetail;
    },

    controllsBase(){
      let controlls = this.config.controlls
      .filter(c => {
        return c.type == 'base'
      })
      .map(c => {
        return c.buttons.map(b => {
          return b.name;
        })[0];
      });
      return controlls;
    },
    controllsPush(){
      let controlls = this.config.controlls.filter(c => {
        return c.type == 'push'
      })[0];
      return controlls.buttons;
    }
  },
  methods: {
    pushButton (button, row) {
      console.log(button);

      if(button.data.routePush.name != undefined){
         let id = row[button.data.id];
         this.$router.push({name: button.data.routePush.name, params : { id }});
      }
      else if(button.data.routePush.route != undefined){
        let id = row[button.data.id];
         this.$router.push({path: `/${button.data.routePush.route}/${ id }`});
      }
      else if(button.data.route != undefined){
         let id = row[button.data.id];
         this.$router.push({path: `/${button.data.route}/${ id }`});
      }
      else{
        throw "not define data valid for route redirect";
      }
    },
    confirmRemove (row) {
      let controll = VueTableConfig.getControllBase(this.config.controlls,'delete');

      let id = row[controll.data.id];

      this.actionRemove = controll.data.action+id;

      this.messageConfirmationRemove = (controll.data.messageConfirmation.split("__")).map((e,i) => {
        if((i % 2) == 1){
          return row[e];
        }
        else{
          return e;
        }
      }).join('');

      this.$refs.modalConfirmationRemove.show()
    },
    
    remove(){
      axios.delete(this.actionRemove)
      .then(response => {
        this.$refs.vuetable.refresh(); 
        
        Alerts.success('Exito',response.data.messsage);
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
      this.$refs.modalConfirmationRemove.hide();
    },
    hideModalConfirmationRemove(){
      this.$refs.modalConfirmationRemove.hide();
    }
  }
}
</script>
