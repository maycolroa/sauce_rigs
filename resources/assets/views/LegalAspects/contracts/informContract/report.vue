<template>
    <div>
        <header-module
            title="INFORMES MENSUALES"
            subtitle="REPORTE"
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
            <b-card style="width:100%" no-body>
                <b-row>
                    <vue-ajax-advanced-select :disabled="isLoading" class="col-md-4" v-model="contract_id" name="contract_id" label="Contratista" placeholder="Seleccione la contratista" :url="contractDataUrl">
                                </vue-ajax-advanced-select>
                    <vue-ajax-advanced-select :disabled="isLoading || !contract_id" class="col-md-4" v-model="year" name="year" label="Año" placeholder=Año :url="urlMultiselect" :parameters="{column: 'year'}" @updateEmpty="updateEmptyKey('year')" :emptyAll="empty.year">
                    </vue-ajax-advanced-select>
                    <vue-ajax-advanced-select :disabled="isLoading || !year" class="col-md-4" v-model="theme" name="theme" label="Tema" placeholder=Tema :url="urlMultiselectTheme" :parameters="{inform_id: inform_id}" @updateEmpty="updateEmptyKey('theme')" :emptyAll="empty.theme">
                    </vue-ajax-advanced-select>
                </b-row>
                <b-row style="width:100%" v-if="report.length > 0">
                    <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                        <table style="width:80%; font-size: 14px" class="table table-bordered mb-2">
                            <tbody>
                                <template v-for="(theme, index) in report">
                                    <tr :key="index+2" style="width:100%;">
                                        <td :colspan="theme.headings[0].length" :key="index" style="width:100%; background-color:#f0635f"><center><b>{{theme.name}}</b></center></td>
                                    </tr>
                                    <tr :key="index+200" style="width:100%">
                                        <template v-for="(month, indexM) in theme.headings[0]">
                                            <td v-if="indexM == 13" style="width:100%; background-color:#dcdcdc" :key="indexM">{{month}}</td>
                                            <td v-else :key="indexM">{{month}}</td>
                                        </template>
                                    </tr>
                                    <template v-for="(executed, indexE) in theme.items[0]">
                                        <tr v-if="theme.items[0].length == (indexE + 1)" :key="indexE+454+indexE" style="width:100%; background-color:#dcdcdc">
                                            <template v-for="(value, indexV) in executed">
                                                <td  v-if="indexV == 'total'" style="vertical-align: middle; background-color:#dcdcdc" :key="indexV">
                                                    <center>{{value}}</center>
                                                </td>
                                                <td  v-else style="vertical-align: middle;" :key="indexV">
                                                    <center>{{value}}</center>
                                                </td>
                                            </template>
                                        </tr>
                                        <tr v-else :key="indexE+454+indexE" style="width:100%">
                                            <template v-for="(value, indexV) in executed">
                                                <td  v-if="indexV == 'total'" style="vertical-align: middle; background-color:#dcdcdc" :key="indexV">
                                                    <center>{{value}}</center>
                                                </td>
                                                <td  v-else style="vertical-align: middle;" :key="indexV">
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
            </b-card>
        </div>
    </diV>
</template>

<script>

import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
    name: 'legalaspects-informs-report',
    metaInfo: {
        title: 'Informes Mensuales - Reporte'
    },
    components:{
        Loading,
        VueAjaxAdvancedSelect
    },
    data () {
        return {
            isLoading: false,
            report: [],
            year: '',
            theme: '',
            contract_id: '',
            empty: {
                year: false,
                theme: false
            },
            urlMultiselect: '/selects/ctInformReportMultiselect',
            urlMultiselectTheme: '/selects/ctInformReportMultiselectThemes',
            inform_id: this.$route.params.id,
            contractDataUrl: '/selects/contractors',
        }
    },
    created(){
        //this.fetch()
    },
    watch: {
        'contract_id'() {
            this.emptySelect('year', 'year')
            this.emptySelect('theme', 'theme')
        },
        'year'() {
            if (this.contract_id && this.year)
            {
                this.emptySelect('theme', 'theme')
                this.fetch()
            }
        },
        'theme'()
        {
            this.fetch()
        }
    },
    methods: {
        fetch()
        {
            if (!this.isLoading)
            {
                this.isLoading = true;
                this.postData = Object.assign({}, {contract_id: this.contract_id}, {year: this.year}, {theme: this.theme}, {inform_id: this.inform_id});

                axios.post('/legalAspects/informContract/reportTableTotales', this.postData)
                    .then(response => {
                    this.report = response.data
                    console.log(this.report[0].items[0])
                    this.isLoading = false
                    }).catch(error => {
                        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                    });
            }
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
    }
}

</script>