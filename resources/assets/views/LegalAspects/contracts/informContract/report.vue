<template>
    <div>
        <header-module
            title="INFORMES MENSUALES"
            subtitle="REPORTE"
            url="legalaspects-informs"
        />
        <div class="col-md">
            <b-card no-body>
                <b-card-header class="with-elements">
                    <b-btn :to="{name:'legalaspects-informs-contracts', id: `${this.$route.params.id}`}" variant="secondary">Regresar</b-btn>
                </b-card-header>
            </b-card>
        </div>
        <loading :display="isLoading"/>
        <div style="width:100%" class="col-md" v-show="!isLoading">
            <b-card no-body>
                <br>
                <h4 style="padding-left: 5%">Informe:  {{name_inform}}.</h4>
                <b-tabs card pills class="nav-responsive-md md-pills-light">
                    <b-tab>
                        <template slot="title">
                            <strong>Reporte por contratista</strong> 
                        </template>
                        <b-row style="width:95%; padding-left: 5%">
                            <b-col cols="6">
                                <vue-ajax-advanced-select :disabled="isLoading" v-model="year" name="year" label="Año" placeholder="Año" :url="urlMultiselect" :parameters="{column: 'year'}" @updateEmpty="updateEmptyKey('year')" :emptyAll="empty.year">
                                </vue-ajax-advanced-select>
                            </b-col>
                            <b-col cols="6">
                                <vue-radio :disabled="isLoading || !year" v-model="consult_all" :options="siNo" name="consult_all" label="¿Desea consultar todos los contratistas?">
                                </vue-radio>
                            </b-col>
                            <b-col cols="6" v-if="consult_all == 'NO'">
                                <vue-ajax-advanced-select :disabled="isLoading" v-model="contract_id" name="contract_id" label="Contratista" placeholder="Seleccione la contratista" :url="contractDataUrl">
                                        </vue-ajax-advanced-select>
                            </b-col>
                            <b-col cols="6" v-if="auth.proyectContract == 'SI'">
                                <vue-ajax-advanced-select :disabled="isLoading" v-model="proyect_id" name="proyect_id" label="Proyectos" placeholder="Seleccione el proyecto" :url="proyectsUrl" :allowEmpty="true">
                                </vue-ajax-advanced-select>
                            </b-col>
                            <b-col cols="6">
                                <vue-ajax-advanced-select :disabled="isLoading || !year" v-model="theme" name="theme" label="Tema" placeholder="Tema" :url="urlMultiselectTheme" :parameters="{inform_id: inform_id}" @updateEmpty="updateEmptyKey('theme')" :emptyAll="empty.theme">
                                </vue-ajax-advanced-select>
                            </b-col>
                        </b-row>
                        <b-row>
                            <label class="col-md-6 offset-md-1"><b>Valor Real</b></label>
                        </b-row>
                        <b-row style="width:100%" v-if="report.length > 0">
                            <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                <table style="width:85%; font-size: 12px" class="table table-bordered mb-2">
                                    <tbody>
                                        <template v-for="(theme, index) in report">
                                            <tr :key="index+round()" style="width:100%;">
                                                <td :colspan="theme.headings[0].length" style="width:100%; background-color:#f0635f"><center><b>{{theme.name}}</b></center></td>
                                            </tr>
                                            <tr :key="index+round()" style="width:100%">
                                                <template v-for="(month, indexM) in theme.headings[0]">
                                                    <td v-if="indexM == 13" style="width:100%; background-color:#dcdcdc" :key="indexM+round()">{{month}}</td>
                                                    <td v-else :key="indexM+round()">{{month}}</td>
                                                </template>
                                            </tr>
                                            <template v-for="(executed, indexE) in theme.items[0]">
                                                <tr v-if="theme.items[0].length == (indexE + 1)" :key="indexE+round()" style="width:100%; background-color:#dcdcdc">
                                                    <template v-for="(value, indexV) in executed">
                                                        <td  v-if="indexV == 'total'" style="vertical-align: middle; background-color:#dcdcdc" :key="indexV+round()">
                                                            <center>{{value}}</center>
                                                        </td>
                                                        <td  v-else style="vertical-align: middle;" :key="indexV+round()">
                                                            <center>{{value}}</center>
                                                        </td>
                                                    </template>
                                                </tr>
                                                <tr v-else :key="indexE+round()" style="width:100%">
                                                    <template v-for="(value, indexV) in executed">
                                                        <td  v-if="indexV == 'total'" style="vertical-align: middle; background-color:#dcdcdc" :key="indexV+round()">
                                                            <center>{{value}}</center>
                                                        </td>
                                                        <td  v-else style="vertical-align: middle;" :key="indexV+round()">
                                                            <center>{{value}}</center>
                                                        </td>
                                                    </template>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </b-card>
                        </b-row>
                        <b-row>
                            <b-card style="width:95%" no-body>
                                <b-row style="width:95%; padding-left: 5%">
                                    <b-col cols="4">
                                        <vue-ajax-advanced-select :disabled="isLoading || !year" v-model="theme_id_grafic_values" name="theme" label="Tema" placeholder=Tema :url="urlMultiselectTheme" :parameters="{inform_id: inform_id}" @updateEmpty="updateEmptyKey('theme_id_grafic_values')" :emptyAll="empty.theme_id_grafic_values">
                                        </vue-ajax-advanced-select>
                                    </b-col>
                                    <b-col>
                                        <vue-ajax-advanced-select :disabled="isLoading || !year" class="col-md-12" v-model="item" name="item" label="Item para graficar" placeholder="Item" :url="urlMultiselectItem" :parameters="{inform_id: inform_id, theme_id: theme_id_grafic_values}" @updateEmpty="updateEmptyKey('item')" :emptyAll="empty.item">
                                    </vue-ajax-advanced-select>
                                    </b-col>
                                </b-row>
                                <b-row class="col-md-12">
                                    <b-col v-if="item">
                                        <line-component v-if="report_line.answers.length > 0" :key="test" :chartData="chartData" ref="line"></line-component>
                                        <b-container v-else>
                                            <b-row align-h="center">
                                                <b-col cols="6">No hay resultados</b-col>
                                            </b-row>
                                        </b-container>
                                    </b-col>
                                </b-row>
                            </b-card>
                        </b-row>
                        <b-row>
                            <label class="col-md-6 offset-md-1"><b>Cumplimiento</b></label>
                        </b-row>
                        <b-row style="width:95%">
                            <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                <table style="width:85%; font-size: 12px" class="table table-bordered mb-2">
                                    <tbody>
                                        <template v-for="(theme, index) in report_porcentage">
                                            <tr :key="index+round()" style="width:100%;">
                                                <td :colspan="theme.headings[0].length" style="width:100%; background-color:#f0635f"><center><b>{{theme.name}}</b></center></td>
                                            </tr>
                                            <tr :key="index+round()" style="width:100%">
                                                <template v-for="(month, indexM) in theme.headings[0]">
                                                    <td v-if="indexM == 13" style="width:100%; background-color:#dcdcdc" :key="indexM+round()">{{month}}</td>
                                                    <td v-else :key="indexM+round()">{{month}}</td>
                                                </template>
                                            </tr>
                                            <template v-for="(executed, indexE) in theme.items[0]">
                                                <tr v-if="theme.items[0].length == (indexE + 1)" :key="indexE+round()" style="width:100%; background-color:#dcdcdc">
                                                    <template v-for="(value, indexV) in executed">
                                                        <td style="vertical-align: middle;" :key="indexV+round()">
                                                            <center>{{indexV == 'item' ? value : redondearValor(value)}}{{indexV != 'item' ? '%' : ''}}</center>
                                                        </td>
                                                    </template>
                                                </tr>
                                                <tr v-else :key="indexE+round()" style="width:100%">
                                                    <template v-for="(value, indexV) in executed">
                                                        <td :style="indexV == 'total' ? 'vertical-align: middle; background-color:#dcdcdc' : 'vertical-align: middle;'" :key="indexV+round()">
                                                            <center>{{indexV == 'item' ? value : redondearValor(value)}}{{indexV != 'item' ? '%' : ''}}</center>
                                                        </td>   
                                                    </template>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </b-card>
                        </b-row>
                        <b-row>
                            <b-card style="width:95%" no-body>
                                <b-row style="width:95%; padding-left: 5%">
                                    <b-col cols="4">
                                        <vue-ajax-advanced-select :disabled="isLoading || !year" v-model="theme_id_grafic_compliance" name="theme" label="Tema" placeholder=Tema :url="urlMultiselectTheme" :parameters="{inform_id: inform_id}" @updateEmpty="updateEmptyKey('theme_id_grafic_compliance')" :emptyAll="empty.theme_id_grafic_compliance">
                                        </vue-ajax-advanced-select>
                                    </b-col>
                                    <b-col>
                                        <vue-ajax-advanced-select :disabled="isLoading || !year" class="col-md-12" v-model="item_2" name="item" label="Item para graficar Cumplimientos" placeholder="Item" :url="urlMultiselectItem" :parameters="{inform_id: inform_id, theme_id: theme_id_grafic_compliance, compliance:'si'}" @updateEmpty="updateEmptyKey('item2')" :emptyAll="empty.item_2">
                                    </vue-ajax-advanced-select>
                                    </b-col>
                                </b-row>
                                <b-row class="col-md-12">
                                    <b-col v-if="item_2">
                                        <line-component v-if="report_line_porcentage.answers.length > 0" :key="test" :chartData="chartData2" ref="line"></line-component>
                                        <b-container v-else>
                                            <b-row align-h="center">
                                                <b-col cols="6">No hay resultados</b-col>
                                            </b-row>
                                        </b-container>
                                    </b-col>
                                </b-row>
                            </b-card>
                        </b-row>
                    </b-tab>
                    <b-tab>
                        <template slot="title">
                            <strong>Reporte Global</strong> 
                        </template>
                            <b-row style="width:100%">
                                <b-col cols="4">
                                    <vue-ajax-advanced-select :disabled="isLoading" v-model="year_global" name="year" label="Año" placeholder="Año" :url="urlMultiselect" :parameters="{column: 'year'}" @updateEmpty="updateEmptyKey('year_global')" :emptyAll="empty.year">
                                    </vue-ajax-advanced-select>
                                </b-col>
                                <b-col cols="4" v-if="auth.proyectContract == 'SI'">
                                    <vue-ajax-advanced-select :disabled="isLoading || !year_global" v-model="proyect_id_global" name="proyects_id" label="Proyectos" placeholder="Seleccione el proyecto" :url="proyectsUrl" :allowEmpty="true">
                                    </vue-ajax-advanced-select>
                                </b-col>
                                <b-col cols="4">
                                    <vue-ajax-advanced-select :disabled="isLoading || !year_global" v-model="theme_global" name="theme_global" label="Tema" placeholder="Tema" :url="urlMultiselectTheme" :parameters="{inform_id: inform_id}" @updateEmpty="updateEmptyKey('theme')" :emptyAll="empty.theme">
                                    </vue-ajax-advanced-select>
                                </b-col>
                            </b-row>
                            <b-row style="width:100%">
                                <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                    <b-row>
                                        <b-col>
                                            <div v-if="report_porcentage_global.length < 1 && theme_global">
                                                <center>
                                                    <b><p style="text-align: center; font-size: 18px;">
                                                        El tema no tiene informacion debido a que no posee % de cumplimiento por la configuración de sus items.
                                                    </p></b>
                                                </center>
                                            </div>
                                            <table v-else style="width:85%; font-size: 12px" class="table table-bordered mb-2">
                                                <tbody>
                                                    <template v-for="(theme, index) in report_porcentage_global">
                                                        <tr :key="index+round()" style="width:100%;">
                                                            <td :colspan="theme.headings[0].length" style="width:100%; background-color:#f0635f"><center><b>{{theme.name}}</b></center></td>
                                                        </tr>
                                                        <tr :key="index+round()" style="width:100%">
                                                            <template v-for="(month, indexM) in theme.headings[0]">
                                                                <td v-if="indexM == 13" style="width:100%; background-color:#dcdcdc" :key="indexM+round()">{{month}}</td>
                                                                <td v-else :key="indexM+round()">{{month}}</td>
                                                            </template>
                                                        </tr>
                                                        <template v-for="(executed, indexE) in theme.items[0]">
                                                            <tr v-if="theme.items[0].length == (indexE + 1)" :key="indexE+round()" style="width:100%; background-color:#dcdcdc">
                                                                <template v-for="(value, indexV) in executed">
                                                                    <td v-if="indexV == 'item'" style="vertical-align: middle;" :key="indexV+round()">
                                                                        <center>{{value}}</center>
                                                                    </td>
                                                                    <td v-else style="vertical-align: middle;" :key="indexV+round()">
                                                                        <center>{{redondearValor(value)}}%</center>
                                                                    </td>
                                                                </template>
                                                            </tr>
                                                            <tr v-else :key="indexE+round()" style="width:100%">
                                                                <template v-for="(value, indexV) in executed">
                                                                    <td :style="indexV == 'total' ? 'vertical-align: middle; background-color:#dcdcdc' : 'vertical-align: middle;'" :key="indexV+round()">
                                                                        <center>{{indexV == 'item' ? value : redondearValor(value)}}{{indexV != 'item' ? '%' : ''}}</center>
                                                                    </td>        
                                                                </template>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-row>
                            <b-modal ref="modalpercentage" :hideFooter="true" id="modals-default-percentage" class="modal-top modal-item" size="xs">
                                <div slot="modal-title">
                                    Tema: {{item_modal}}<br>
                                    Item: {{theme_name}}<br>
                                     
                                </div>
                                <b-card bg-variant="transparent" title="" class="mb-3 box-shadow-none">
                                    <table class="table table-bordered mb-2">
                                        <tbody>
                                            <tr>
                                                <th style="text-align: center;">{{month_name}} de {{year_global}}</th>
                                                <th style="text-align: center;">{{percentage_global}}%</th>
                                            </tr>
                                            <tr>
                                                <td style="text-align: center;" ><b>Contratista</b></td>
                                                <td style="text-align: center;" ><b>Porcentage</b></td>
                                            </tr>
                                            <template v-for="(contract, indexG) in report_porcentage_for_contract">
                                            <tr :key="indexG+round()">
                                                <td style="text-align: center;" >
                                                    {{contract['name']}}
                                                </td>
                                                <td style="text-align: center;" >
                                                    {{contract[contract['month_name']]}}%
                                                </td>
                                            </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </b-card>
                                <br>
                                <div class="row float-right pt-12 pr-12y">
                                    <b-btn variant="primary" @click="hideModal('modalpercentage')">Cerrar</b-btn>
                                </div>
                            </b-modal>
                    </b-tab>
                </b-tabs>
            </b-card>
        </div>
    </diV>
</template>

<script>

import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import LineComponent from '@/components/Chartjs/ChartLine.vue';
import VueRadio from "@/components/Inputs/VueRadio.vue";

export default {
    name: 'legalaspects-informs-report',
    metaInfo: {
        title: 'Informes Mensuales - Reporte'
    },
    components:{
        Loading,
        VueAjaxAdvancedSelect,
        LineComponent,
        VueRadio
    },
    data () {
        return {
            isLoading: false,
            theme_id_grafic_compliance: '',
            theme_id_grafic_values: '',
            report: [],
            year: '',
            theme: '',
            proyect_id: '',
            year_global: '',
            theme_global: '',
            proyect_id_global: '',
            contract_id: '',
            item: '',
            item_2: '',
            empty: {
                year: false,
                theme: false
            },
            urlMultiselect: '/selects/ctInformReportMultiselect',
            urlMultiselectTheme: '/selects/ctInformReportMultiselectThemes',
            urlMultiselectItem: '/selects/ctInformReportMultiselectItems',
            inform_id: this.$route.params.id,
            contractDataUrl: '/selects/contractors',
			proyectsUrl: '/selects/contracts/ctProyects',
            test: true,
            chartData: {},
            chartData2: {},
            report_line: {
                item: '',
                headings: [],
                answers: []
            },
            report_porcentage: [],
            report_porcentage_global: [],
            report_porcentage_for_contract: [],
            report_line_porcentage: {
                item: '',
                headings: [],
                answers: []
            },
            item_modal: '',
            month_name: '',
            percentage_global: '',
            theme_name: '',
            name_inform: '',
            consult_all: '',
            siNo: [
                {text: 'SI', value: 'SI'},
                {text: 'NO', value: 'NO'}
            ],
        }
    },
    created(){
        axios.get(`/legalAspects/inform/${this.inform_id}`)
            .then(response => {
                this.name_inform = response.data.data.name;
            })
            .catch(error => {
                console.log(error)
            });
    },
    watch: {
        'consult_all'() {
            if (this.consult_all == 'SI')
            {
                this.fetch()
                this.emptySelect('item', 'item')
                this.emptySelect('theme_id_grafic_values', 'theme_id_grafic_values')
                this.emptySelect('item_2', 'item_2')
                this.emptySelect('theme_id_grafic_compliance', 'theme_id_grafic_compliance')
            }
        },
        'contract_id'() {
            if (this.contract_id && this.year)
            {
                this.fetch()
                this.emptySelect('item', 'item')
                this.emptySelect('theme_id_grafic_values', 'theme_id_grafic_values')
                this.emptySelect('item_2', 'item_2')
                this.emptySelect('theme_id_grafic_compliance', 'theme_id_grafic_compliance')
            }
        },
        /*'year'() {
            this.fetch()
        },*/
        'theme'() {
            this.fetch()
        },
        'proyect_id' () {
            this.fetch()
        },
        'item'() {
            this.fetch2()
        },
        'item_2'() {
            this.fetch4()
        },
        'year_global'() {
            this.fetch5()
        },
        'theme_global'() {
            this.fetch5()
        },
        'proyect_id_global' () {
            this.fetch5()
        }
    },
    methods: {
        redondearValor: function(valor) {
            return Math.round(valor * 100) / 100;
        },
        fetch()
        {
            if (!this.isLoading)
            {
                this.isLoading = true;
                this.postData = Object.assign({}, {contract_id: this.contract_id}, {year: this.year}, {theme: this.theme}, {inform_id: this.inform_id}, {proyect_id: this.proyect_id}, {consult_all: this.consult_all});

                axios.post('/legalAspects/informContract/reportTableTotales', this.postData)
                    .then(response => {
                    this.report = response.data
                    axios.post('/legalAspects/informContract/reportTablePorcentage', this.postData)
                        .then(response2 => {
                        this.report_porcentage = response2.data
                        this.isLoading = false;
                        }).catch(error => {
                            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                        });
                    }).catch(error => {
                        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                    });

            }
        },
        fetch2()
        {
            this.postData2 = Object.assign({}, {contract_id: this.contract_id}, {year: this.year}, {inform_id: this.inform_id}, {item_id: this.item}, {proyect_id: this.proyect_id}, {consult_all: this.consult_all});

            axios.post('/legalAspects/informContract/reportLineItemQualification', this.postData2)
                .then(response => {
                this.report_line = response.data

                this.chartData = {
                    labels: this.report_line.headings,
                    datasets: [{
                        label: this.report_line.item,
                        data: this.report_line.answers,
                        borderWidth: 1,
                        backgroundColor: '#36A2EB',
                        borderColor: '#36A2EB',
                        fill: false
                    }]
                }
                this.test = !this.test;
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        fetch4()
        {
            this.postData2 = Object.assign({}, {contract_id: this.contract_id}, {year: this.year}, {inform_id: this.inform_id}, {item_id: this.item_2}, {proyect_id: this.proyect_id}, {consult_all: this.consult_all});

            axios.post('/legalAspects/informContract/reportLineItemPorcentege', this.postData2)
                .then(response => {
                this.report_line_porcentage = response.data

                this.chartData2 = {
                    labels: this.report_line_porcentage.headings,
                    datasets: [{
                        label: this.report_line_porcentage.item,
                        data: this.report_line_porcentage.answers,
                        borderWidth: 1,
                        backgroundColor: '#36A2EB',
                        borderColor: '#36A2EB',
                        fill: false
                    }]
                }
                this.test = !this.test;
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        fetch5()
        {
            this.postData2 = Object.assign({}, {year: this.year_global}, {inform_id: this.inform_id}, {theme: this.theme_global}, {proyect_id: this.proyect_id_global});

            axios.post('/legalAspects/informContract/reportTablePorcentageGlobal', this.postData2)
                .then(response => {
                this.report_porcentage_global = response.data
                this.isLoading = false;
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        emptySelect(keySelect, keyEmpty)
        {
            if (this[keySelect] !== '')
            {
                this.empty[keyEmpty] = true
                this[keySelect] = ''
            }
        },
        updateEmptyKey(keyEmpty)
        {
            this.empty[keyEmpty]  = false
        },
        round()
        {
            return new Date().getTime() + Math.round(Math.random() * 10000);
        },
        modalContract(item, mes, theme_id, value, theme_name)
        {
             this.postData2 = Object.assign({}, {year: this.year_global}, {inform_id: this.inform_id}, {theme: theme_id}, {month: mes}, {item: item}, {proyect_id: this.proyect_id});

            axios.post('/legalAspects/informContract/detailContractGlobal', this.postData2)
                .then(response => {
                this.report_porcentage_for_contract = response.data

                if (this.report_porcentage_for_contract.length > 0)
                {
                    this.item_modal = item;
                    this.month_name = this.report_porcentage_for_contract[0].month_name;
                    this.percentage_global = value
                    this.theme_name = theme_name
                    this.$refs.modalpercentage.show();
                }
                this.isLoading = false;
                }).catch(error => {
                    console.log(error)
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        hideModal(ref) {
            this.$refs.modalpercentage.hide()
        },
    }
}

</script>