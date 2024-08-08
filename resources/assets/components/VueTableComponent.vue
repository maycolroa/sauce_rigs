<template>
  <div>
      <b-row v-if="configNameFilter">
          <b-col>
              <filter-general 
                  v-model="filters" 
                  :configName="configNameFilter"
                  :modelId="modelId" />
          </b-col>
      </b-row>

    <v-server-table class="vuetable-master" :url="config.configuration.urlData" :columns="columns" :options="options" ref="vuetable" :key="keyVuetable" @row-click="onRowClick" v-if="tableReady">
      <template slot="controlls" slot-scope="props">
        <div class="align-middle text-center">
          
          <template v-for="(button, index) in controllsPush">
          <b-btn :key="index" 
          v-if="checkView(button, props.row)"
          :variant="button.config.color + ' ' + (button.config.borderless ? 'borderless' : '') + ' ' + (button.config.icon ? 'icon-btn' : '')" 
          class="btn-xs"
          v-b-tooltip.top :title="(button.config.title ? button.config.title : '')"
          @click.prevent="pushButton(button,props.row)"><i :class="button.config.icon"></i></b-btn>
          </template>

          <template v-for="(button, index) in controllsDownload">
            <b-btn :key="`${index}${button.name}`" 
            v-if="checkViewBtnBase(button, props.row)"
            :variant="button.config.color + ' ' + (button.config.borderless ? 'borderless' : '') + ' ' + (button.config.icon ? 'icon-btn' : '')" 
            class="btn-xs"
            v-b-tooltip.top :title="(button.config.title ? button.config.title : '')"
            @click.prevent="downloadButton(button, props.row)"><i :class="button.config.icon"></i></b-btn>
          </template>

          <template v-for="(button, index) in controllsSimpleDownload">
            <b-btn
            v-if="checkViewBtnBase(button, props.row)" 
            :key="`${index}${button.name}`" 
            :variant="button.config.color + ' ' + (button.config.borderless ? 'borderless' : '') + ' ' + (button.config.icon ? 'icon-btn' : '')" 
            class="btn-xs"
            v-b-tooltip.top :title="(button.config.title ? button.config.title : '')"
            :href="button.data.action+props.row[button.data.id]"
            target="blank"
            ><i :class="button.config.icon"></i></b-btn>
          </template>

          <template v-for="(button, index) in controllsBaseBtn">
            <span :key="`${index}${button.name}`">
              <b-btn 
                v-if="checkViewBtnBase(button, props.row)"
                :variant="button.config.color + ' ' + (button.config.borderless ? 'borderless' : '') + ' ' + (button.config.icon ? 'icon-btn' : '')" 
                class="btn-xs"
                v-b-tooltip.top :title="(button.config.title ? button.config.title : '')"
                @click.prevent="confirmBtnBase(props.row, button.name)"><i :class="button.config.icon"></i></b-btn>

              <!-- modal confirmation for delete -->
              <b-modal :ref="`modalBtnBase-${button.name}`" class="modal-slide" hide-header hide-footer>
                <p class="text-center text-big mb-4">
                  {{messageConfirmationBtnBase}}
                </p>
                <b-btn block variant="primary" @click="executeBtnBase(`modalBtnBase-${button.name}`)">Aceptar</b-btn>
                <b-btn block variant="default" @click="hideModal(`modalBtnBase-${button.name}`)">Cancelar</b-btn>
              </b-modal>
            </span>
          </template>

          <template v-for="(button, index) in controllsForms">
            <span :key="`${index}${button.name}`">
              <b-btn 
                v-if="checkViewBtnBase(button, props.row)"
                :variant="button.config.color + ' ' + (button.config.borderless ? 'borderless' : '') + ' ' + (button.config.icon ? 'icon-btn' : '')" 
                class="btn-xs"
                v-b-tooltip.top :title="(button.config.title ? button.config.title : '')"
                @click.prevent="openModalForm(props.row, button.name)"><i :class="button.config.icon"></i></b-btn>

              <!-- modal confirmation for delete -->
              <b-modal :ref="`modalBtnForm-${button.name}-${props.row.id}`" size="lg" class="modal-top" hide-header hide-footer>
                <b-card bg-variant="transparent" title="" class="mb-3 box-shadow-none">
                  <template v-if="button.name == 'switchStatus'">
                    <form-check-swith
                      :url="`/biologicalmonitoring/reinstatements/check/switchStatus/${props.row.id}`"
                      :check="props.row"
                      @closeModal="hideModal(`modalBtnForm-${button.name}-${props.row.id}`, true)"
                    />
                  </template>
                </b-card>
              </b-modal>
            </span>
          </template>
          
          <b-btn v-if="controllsBase.includes('delete') && checkViewDelete(props.row)" variant="outline-danger borderless icon-btn" class="btn-xs" v-b-tooltip.top title="Eliminar" @click.prevent="confirmRemove(props.row)"><i class="ion ion-md-close"></i></b-btn>
        </div>
      </template>
     }
      <template :slot="details.length > 0 || config.configuration.detailComponent ? 'child_row': null" slot-scope="props">
        <div v-if="!component">
          <b-row class="mx-0">
          <b-col cols="6" v-for="(field, index) in details" :key="index">
            <b>{{field.title}}:</b> {{props.row[field.data]}}
          </b-col>
          </b-row>
        </div>
        <component :is="component" v-if="component" :row="props.row"/>
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
  /*.table-with-detail thead th:not(:first-of-type) {
    min-width: 100px;
  }
  .table-without-detail thead th {
    min-width: 100px;
  }*/
  .table-with-detail tbody td {
    white-space: nowrap;
  }
  .table-without-detail tbody td {
    white-space: normal;
  }
  .VueTables__limit {
    float: left;
  }
  .vuetable-master {
    font-size: 12px;
  }
  .table thead th {
    white-space: nowrap;
  }
</style>

<style src="@/vendor/libs/vue-data-tables/vue-data-tables.scss" lang="scss"></style>

<script>
import Vue from 'vue'
import {ServerTable, ClientTable, Event} from 'vue-tables-2';
import VueTableConfig from '@/vuetableconfig/';
import Alerts from '@/utils/Alerts.js';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';
import FormCheckSwith from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/FormCheckSwithComponent.vue';

Vue.use(ServerTable)

export default {
  name: 'vue-table',
  props:{
    customColumnsName: {type: Boolean, default: false},
    configName: {type: String, required: true},
    config: {type: Object, default: function(){
      return VueTableConfig.get(this.configName);
    }},
    viewIndex: { type: Boolean, default: true },
    modelId: {type: [Number, String], default: null},
    params: {type: Object, default: function()
      {
        return {}
      }
    },
    configNameFilterDefault: {type: String, default: ''},
  },
  components:{
    FilterGeneral,
    FormCheckSwith
  },
  data(){
    return{
      messageConfirmationRemove:'',
      actionRemove:'',
      messageConfirmationBtnBase: '',
      actionBtnBase:'',
      actionBtnForm:'',
      component: null,
      filters: [],
      tableReady: false,
      keyVuetable: 'Vuetable',
      pagina: 4
    }
  },
  watch: {
    filters: {
        handler(val){
            if (this.tableReady)
            {
              Vue.nextTick( () => this.$refs.vuetable.refresh() )
              this.$emit("filtersUpdate", this.filters);
            }
        },
        deep: true
    },
    modelId() {
      Vue.nextTick( () => this.$refs.vuetable.refresh() )
    }
  },
  computed: {
    configNameFilter() {
      if (this.config.configuration.configNameFilter != undefined)
        return this.config.configuration.configNameFilter
      else 
        return this.configNameFilterDefault
    },
    loader(){
      if(this.config.configuration.detailComponent){
        return () => import(`@/components${this.config.configuration.detailComponent}`);
      }
      return () => null;
    },
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
        columnsDropdown: true,
        pagination: { chunk: 5 },
        perPage: 10,
        perPageValues: [10, 25, 50, 100],
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
          }
          .bind(this))
          .finally(function(e) {         
            let data2 = {
                vuetable: data.nameTable,
                page: data.page
            }

            axios.post(`/setStatePageVuetable`, data2)
              .then(response => {
              })
              .catch(error => {
                  //Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                  //this.$router.go(-1);
              });
           });
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
          columns:'Columnas',
          limit: 'Registros'
        },
        params: {
          filters: this.filters,
          modelId: this.modelId,
          nameTable: this.configName,
          pagina: this.pagina,
          tables: {}
        },
        initialPage: this.pagina
      };

      var fields = this.config.fields;
      
      _.forIn(this.params, (value, key) => {
          this.$set(options.params, key, value)
      });

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
          options.headings[f.data] = this.keywordCheck(f.title, f.title);
      });

      options.params.tables= {};
      fields.map((f) => {
          if (f.searchable)
            options.params.tables[f.data] = f.name;
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
       options.skin += (countdetail > 0 || this.config.configuration.detailComponent) ?  " table-with-detail": " table-without-detail";
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
      })[0]
      .buttons.filter(c => {
        if (c.permission)
          return auth.can[c.permission]
        else 
          return true
      })
      .map(c => {
        return c.name;
      });
      return controlls;
    },
    controllsPush(){
      let controlls = this.config.controlls.filter(c => {
        return c.type == 'push'
      })[0]
      .buttons.filter(c => {
        if (c.permission)
          return auth.can[c.permission]
        else 
          return true
      });
      return controlls;
    },
    controllsBaseBtn(){
      let controlls = this.config.controlls
      .filter(c => {
        return c.type == 'base'
      })[0]
      .buttons.filter(c => {
        if (c.permission)
          return auth.can[c.permission] && c.name != 'delete'
        else 
          return c.name != 'delete'
      });

      return controlls;
    },
    controllsDownload(){
      let controlls = this.config.controlls.filter(c => {
        return c.type == 'download'
      })[0]
      
      if (controlls)
      {
        controlls = controlls.buttons.filter(c => {
                      if (c.permission)
                        return auth.can[c.permission]
                      else 
                        return true
                    });
      }

      return controlls;
    },
    controllsSimpleDownload(){
      let controlls = this.config.controlls.filter(c => {
        return c.type == 'simpleDownload'
      })[0]
      
      if (controlls)
      {
        controlls = controlls.buttons.filter(c => {
                      if (c.permission)
                        return auth.can[c.permission]
                      else 
                        return true
                    });
      }
          return controlls;
    },
    controllsForms(){
      let controlls = this.config.controlls.filter(c => {
        return c.type == 'form'
      })[0]
      
      if (controlls)
      {
        controlls = controlls.buttons.filter(c => {
                      if (c.permission)
                        return auth.can[c.permission]
                      else 
                        return true
                    });
      }

      return controlls;
    },
  },
  mounted() {
    if(this.loader()){
      this.loader()
              .then(() => {
                  this.component = () => this.loader()
              })
    }

    this.getPageVuetable()
  },
  methods: {
    loadCustomColumns() {
      axios.post('/vuetableCustomColumns', {'customColumnsName': this.config.name})
      .then(response => {
        this.config.fields = response.data.fields;
        //this.keyVuetable = 'changeVuetable'
        this.tableReady = true
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
      });
    },
    getPageVuetable()
    {
        axios.post(`/getPageVuetable`, { vuetable: this.configName })
          .then(response => {
              if (response.data)
              {
                this.pagina = response.data;

                if (!this.customColumnsName)
                  this.tableReady = true
                else
                  this.loadCustomColumns()
              }
          })
          .catch(error => {});
    },
    onRowClick: function(row) {
      this.$emit("rowClick", row.row);
    },
    pushButton (button, row) {

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
        if (error.response.status == 500 && error.response.data.error != 'Internal Error')
        {
          Alerts.error('Error', error.response.data.error);
        }
        else if (error.response.status == 403)
        {
          Alerts.error('Permiso Denegado', 'No tiene permitido realizar esta acción');
        }
        else
        {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        }
      });
      this.$refs.modalConfirmationRemove.hide();
    },
    hideModalConfirmationRemove(){
      this.$refs.modalConfirmationRemove.hide();
    },
    checkView(button, row) {
      if(row[button.data.routePush.name] != undefined) {
         return row[button.data.routePush.name]
      }

      return true
    },
    checkViewDelete(row) {
      if(row['control_delete'] != undefined) {
         return row['control_delete']
      }

      return true
    },
    checkViewBtnBase(button, row) {
      if(row[button.name] != undefined) {
         return row[button.name]
      }

      return true
    },
		hideModal(ref, refresh = false) {
      this.$refs[ref][0].hide()
      
      if (refresh)
        this.refresh()
    },
    confirmBtnBase (row, name) {
      let controll = VueTableConfig.getControllBase(this.config.controlls, name);

      let id = row[controll.data.id];

      this.actionBtnBase = controll.data.action+id;

      this.messageConfirmationBtnBase = (controll.data.messageConfirmation.split("__")).map((e,i) => {
        if((i % 2) == 1){
          return row[e];
        }
        else{
          return e;
        }
      }).join('');

      this.$refs['modalBtnBase-'+name][0].show()
    },
    openModalForm (row, name) {
      let controll = VueTableConfig.getControllForm(this.config.controlls, name);

      let id = row[controll.data.id];

      this.actionBtnForm = controll.data.action+id;
      this.$refs[`modalBtnForm-${name}-${row.id}`][0].show()
    },
    executeBtnBase(ref){
      axios.post(this.actionBtnBase)
      .then(response => {
        this.$refs.vuetable.refresh(); 
        
        Alerts.success('Exito',response.data.messsage);
      })
      .catch(error => {
        if (error.response.status == 500 && error.response.data.error != 'Internal Error')
        {
          Alerts.error('Error', error.response.data.error);
        }
        else if (error.response.status == 403)
        {
          Alerts.error('Permiso Denegado', 'No tiene permitido realizar esta acción');
        }
        else
        {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        }
      });

      this.$refs[ref][0].hide()
    },
    downloadButton (button, row) {
      
      if (button.data.action != undefined){
          let id = row[button.data.id];
          let actionDownload = button.data.action+id;

          axios.get(actionDownload)
            .then(response => {
              Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
            }).catch(error => {
              Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
      }
      else{
        throw "not define data valid for action download";
      }
    },
    refresh() {
      Vue.nextTick( () => this.$refs.vuetable.refresh() )
    }
  }
}
</script>
