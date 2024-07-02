<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
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
          <b-tab>
            <template slot="title">
                <strong>Reporte Existencias Mínimas</strong> 
            </template>
            <b-card>
              <b-card-body>
                <vue-table
                  configName="industrialsecure-epp-reports-stock-minimun"
                  v-if="auth.can['elements_r']"
                  :params="{filters}"
                  ref="stockMinimun"
                ></vue-table>
              </b-card-body>
            </b-card>
          </b-tab>
          <b-tab>
            <template slot="title">
                <strong>Reporte Elementos Entregados</strong> 
            </template>
            <b-card bg-variant="transparent" border-variant="dark" title="Totales" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                    <div><b>Empleado:</b></div>
                </b-col>
                <b-col>
                    <div><b># Elementos entregados:</b></div>
                </b-col>
              </b-row>
              <template v-for="(item, index) in information3">
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
                  configName="industrialsecure-epp-reports-employees-history"
                  v-if="auth.can['elements_r']"
                  :params="{filters}"
                  ref="elementsDeliveryHistory"
                ></vue-table>
              </b-card-body>
            </b-card>
          </b-tab>
          <b-tab>
            <template slot="title">
                <strong>Elementos mas entregados</strong> 
            </template>
            <b-row>
              <b-col>
                <b-card border-variant="primary" title="Top de elementos" class="mb-3 box-shadow-none">
                  <chart-bar 
                      :chart-data="eppTopData"
                      title="Top de elementos"
                      ref="topElement"/>
                </b-card>
              </b-col>
            </b-row>
          </b-tab>
          <b-tab>
            <template slot="title">
                <strong>Reporte Ubicación - Costos</strong> 
            </template>
            <b-row>
              <b-col>
                <b-card border-variant="primary" title="Ubicación - Costos" class="mb-3 box-shadow-none">
                  <br>
                  <template v-for="(location, index4) in eppCostData">
                    <b-row  style="margin-top: 20px; margin-bottom: 20px" :key="index4">
                      <b-col>
                        <h4>{{index4}}</h4>
                        <br>
                        <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; margin-bottom: 0px">
                            <thead>
                              <tr>
                                  <th scope="col" class="text-center align-middle">Elemento</th>
                                  <th scope="col" class="text-center align-middle">Cantidad</th>
                                  <th scope="col" class="text-center align-middle">Costo</th>
                                  <th scope="col" class="text-center align-middle">Subtotal</th>
                              </tr>
                            </thead>
                            <tbody>
                              <template v-for="(loc, index5) in location">
                                <tr :key="index5">
                                    <td class="align-middle">{{loc.element}}</td>
                                    <td class="align-middle">{{loc.cantidad}}</td>
                                    <td class="align-middle">{{loc.cost}}</td>
                                    <td class="align-middle">{{loc.subtotal}}</td>
                                </tr>
                                <tr :key="index5" v-if="index5 == (location.length-1)">
                                    <td class="align-middle"><b>Total</b></td>
                                    <td class="align-middle">{{loc.totals.total_cantidad}}</td>
                                    <td class="align-middle">{{loc.totals.total_cost}}</td>
                                    <td class="align-middle"><b>{{loc.totals.total}}</b></td>
                                </tr>
                                </template>
                            </tbody>
                        </table>
                         <br>
                      </b-col>
                    </b-row>
                  </template>
                </b-card>
              </b-col>
            </b-row>
          </b-tab>
        </b-tabs>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';
import ChartBar from '@/components/ECharts/ChartBar.vue';

export default {
  name: 'industrialsecure-epp-reports',
  metaInfo: {
    title: 'EPP - Reportes'
  },
  components:{
      FilterGeneral,
      ChartBar
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
        information3: {
            empleado: '',
            total: 0
        },
        eppTopData: {
            labels: [],
            datasets: []
        },
        eppCostData: {}
      }
    },
    watch: {
        filters: {
            handler(val){
                this.$refs.saldosElements.refresh()
                this.$refs.elementsAsigned.refresh()
                this.$refs.stockMinimun.refresh()
                this.$refs.elementsDeliveryHistory.refresh()
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
            axios.post('/industrialSecurity/epp/element/reportEmployeeTotalsHistory', postData)
                .then(response => {
                    this.information3 = response.data.data;
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });

            axios.post('/industrialSecurity/epp/element/reportElementTop', postData)
                .then(response => {
                    this.eppTopData = response.data;
                    console.log(this.eppTopData);
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });

            axios.post('/industrialSecurity/epp/element/reportElementCost', postData)
                .then(response => {
                    this.eppCostData = response.data;
                    console.log(this.eppTopData);
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
