<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="REPORTES"
      url="industrialsecure-epp"
    />

    <div class="col-md">
      <b-card no-body>
        <div>
          <filter-general 
            v-model="filters" 
            configName="industrialsecure-epp-report" />
        </div>

        <b-tabs card pills class="nav-responsive-md md-pills-light">
          <b-tab>
            <template slot="title">
                <strong>Reporte Saldos</strong> 
            </template>
            <b-card bg-variant="transparent" border-variant="dark" title="Totales" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                    <div><b># Elementos:</b></div>
                </b-col>
                <b-col>
                    <div><b># Disponibles:</b></div>
                </b-col>
                <b-col>
                    <div><b># Asignados:</b></div>
                </b-col>
                <b-col>
                    <div><b># En transito:</b></div>
                </b-col>
                <b-col>
                    <div><b># Desechados:</b></div>
                </b-col>
              </b-row>
              <b-row>
                <b-col>
                    <div>{{information.total}}</div>
                </b-col>
                <b-col>
                    <div>{{information.disponibles}}</div>
                </b-col>
                <b-col>
                    <div>{{information.asignados}}</div>
                </b-col>
                <b-col>
                    <div>{{information.transito}}</div>
                </b-col>
                <b-col>
                    <div>{{information.desechados}}</div>
                </b-col>
              </b-row>
            </b-card>
            <b-card>
              <b-card-body>
                <vue-table
                  configName="industrialsecure-epp-reports"
                  v-if="auth.can['elements_r']"
                  :params="{filters}"
                  ref="saldosElements"
                ></vue-table>
              </b-card-body>
            </b-card>
          </b-tab>
          <b-tab>
            <template slot="title">
                <strong>Reporte Elementos Asignados</strong> 
            </template>
            <b-card bg-variant="transparent" border-variant="dark" title="Totales" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                    <div><b>Empleado:</b></div>
                </b-col>
                <b-col>
                    <div><b># Elementos asignados:</b></div>
                </b-col>
              </b-row>
              <template v-for="(item, index) in information2">
                <b-row  :key="index+round()">
                  <b-col>
                      <div><b>{{item.employee}}</b></div>
                  </b-col>
                  <b-col>
                      <div><b>{{item.asignados}}</b></div>
                  </b-col>
                </b-row>
              </template>
            </b-card>
            <b-card>
              <b-card-body>
                <vue-table
                  configName="industrialsecure-epp-reports-employees"
                  v-if="auth.can['elements_r']"
                  :params="{filters}"
                  ref="elementsAsigned"
                ></vue-table>
              </b-card-body>
            </b-card>
          </b-tab>
        </b-tabs>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';

export default {
  name: 'industrialsecure-epp-reports',
  metaInfo: {
    title: 'EPP - Reportes'
  },
  components:{
      FilterGeneral
  },
  data () {
        return {
            filters: [],
            information: {
                total: 0,
                dsponibles: 0,
                asignados: 0,
                transito: 0,
                desechados: 0
            },
            information2: {
                empleado: '',
                total: 0
            },
        }
    },
    watch: {
        filters: {
            handler(val){
                this.$refs.saldosElements.refresh()
                this.$refs.elementsAsigned.refresh()
                this.updateTotales()
            },
            deep: true
        }
    },
    created(){
        this.updateTotales()
    },
    methods: {
        updateTotales()
        {
            let postData = Object.assign({}, {filters: this.filters});
            axios.post('/industrialSecurity/epp/element/reportTotal', postData)
                .then(response => {
                    this.information = response.data.data;
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
            axios.post('/industrialSecurity/epp/element/reportEmployeeTotals', postData)
                .then(response => {
                    this.information2 = response.data.data;
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        round()
        {
            return Math.random();
        }
    }
}
</script>
