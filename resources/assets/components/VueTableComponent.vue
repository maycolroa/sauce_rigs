<template>
  <div>
    <v-server-table :url="urlData" :columns="columns" :options="options">
      <template slot="controlls" slot-scope="props">
        <div>
          <b-btn variant="outline-success borderless icon-btn" class="btn-xs" @click.prevent="edit(props.row)"><i class="ion ion-md-create"></i></b-btn>
          <b-btn variant="outline-danger borderless icon-btn" class="btn-xs" @click.prevent="remove(props.row)"><i class="ion ion-md-close"></i></b-btn>
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

Vue.use(ServerTable)

export default {
  name: 'vue-table',
  metaInfo: {
    title: 'VueTable'
  },
  props:{
    filterColumns: {type:Boolean, default:false},
    urlData: {type:String, required: true},
    fields: {type: Object, required: true},
  },
  created() {
    
  },
  computed: {
    columns(){
      let columns = this.fields.fields.filter((f) => {
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
        serverMultiSorting: true,
        perPageValues: [10,25,50,100],
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
          limit:"Registros:",
          page:"Pagina:",
          noResults:"Sin registros coincidentes",
          filterBy:"Filtrar por {column}",
          loading:'Cargando...',
          defaultOption:'Select {column}',
          columns:'Columnas'
        },
      };

      var fields = this.fields.fields;

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
      options.filterByColumn = this.filterColumns;

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
      let fieldsDetail =  this.fields.fields.filter((f) => {
        return f.detail;
      });
      
      return fieldsDetail;
    }
  },
  methods: {
    edit (row) {
      alert(`Edit: ${row.first_name} ${row.last_name}`)
    },
    remove (row) {
      alert(`Remove: ${row.first_name} ${row.last_name}`)
    }
  }
}
</script>
