<template>
    <div>
        <header-module
            title="SISTEMA"
            subtitle="REPORTE LICENCIAS"
            url="system-licenses"
        />
        
        <loading :display="isLoading"/>
        <div style="width:95%" class="col-md" v-show="!isLoading">
            <b-card no-body>
                    <div class="card-title-elements" v-if="auth.can['licenses_c'] && auth.company_id == 1">
                        <b-btn :to="{name:'system-licenses-configuration'}" variant="primary">Configurar Envio</b-btn>
                    </div>
                    <div>
                        <filter-general 
                            v-model="filters" 
                            configName="system-licenses-report" />
                    </div>
                    <b-row>
                        <vue-advanced-select class="col-md-6 offset-md-2" v-model="order" :multiple="false" :options="options" :hide-selected="false" @input="fetch" name="table" label="Ordenar por:" placeholder="Seleccione una opción">
                        </vue-advanced-select>
                    </b-row>
                    <b-tabs card pills class="nav-responsive-md md-pills-light">
                        <b-tab>
                            <template slot="title">
                                <strong>Reporte General</strong> 
                            </template>
                            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                                <b-row>
                                    <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
                                        <thead>
                                            <tr>
                                                <th v-for="(header, index) in headers.general" :key="`th-${index}`" class="text-center align-middle">
                                                    <b v-if="header.name == 'total' || header.name == 'total_old' || header.name == 'retention' || header.name == 'crecimiento'"> {{ header.label }} </b>
                                                    <p v-else>{{ header.label }}</p>                                            
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style='text-center' v-for="(parameter, key) in data2.general" :key="key">
                                                <td style='text-align:center;' v-for="(header, index) in headers.general" :key="index">
                                                    <b v-if="header.name == 'total' || header.name == 'total_old' || header.name == 'retention' || header.name == 'crecimiento'"> {{ (header.name == 'retention' || header.name == 'crecimiento') ? parameter[header.name]+'%' :  parameter[header.name]}} </b>
                                                    <p v-else>{{ parameter[header.name] }}</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>                        
                                </b-row>
                            </b-card>
                        </b-tab>
                        <b-tab>
                            <template slot="title">
                                <strong>Reporte por Módulo</strong> 
                            </template>
                            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                                <b-row>
                                    <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
                                        <thead>
                                            <tr>
                                                <th v-for="(header, index) in headers.module" :key="`th-${index}`" class="text-center align-middle">
                                                    <b v-if="header.name == 'total' || header.name == 'total_old' || header.name == 'retention' || header.name == 'crecimiento'"> {{ header.label }} </b>
                                                    <p v-else>{{ header.label }}</p> 
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(parameter, key) in data2.module" :key="key">
                                                <template v-if="parameter['module'] != 'Total'">
                                                    <td style='text-align:center;' v-for="(header, index) in headers.group_module" :key="index" v-show="parameter[header.name] != undefined">
                                                        <b v-if="header.name == 'total' || header.name == 'total_old' || header.name == 'retention' || header.name == 'crecimiento'"> {{ (header.name == 'retention' || header.name == 'crecimiento') ? parameter[header.name]+'%' :  parameter[header.name]}} </b>
                                                        <p v-else>{{ parameter[header.name] }}</p>
                                                    </td>
                                                </template>
                                                <template v-else>
                                                    <td style='text-align:center;' v-for="(header, index) in headers.group_module" :key="index" v-show="parameter[header.name] != undefined">
                                                        <b> {{ (header.name == 'retention' || header.name == 'crecimiento') ? parameter[header.name]+'%' :  parameter[header.name]}} </b>
                                                    </td>
                                                </template>
                                            </tr>
                                        </tbody>
                                    </table>                   
                                </b-row>
                            </b-card>
                        </b-tab>
                        <b-tab>
                            <template slot="title">
                                <strong>Reporte por Grupo de Compañia</strong> 
                            </template>
                            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                                <b-row>
                                    <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
                                        <thead>
                                            <tr>
                                                <th 
                                                    v-for="(header, index) in headers.group" 
                                                    :key="`th-${index}`" 
                                                    class="text-center align-middle"
                                                >
                                                    <b 
                                                    v-if="header.name == 'total' || header.name == 'total_old' || header.name == 'retention' || header.name == 'crecimiento'">
                                                        {{ header.label }} 
                                                    </b>
                                                    <p v-else>{{ header.label }}</p> 
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(parameter, key) in data2.group" :key="key">
                                                <template v-if="parameter['group'] != 'Total'">
                                                    <td 
                                                        style='text-align:center;' 
                                                        v-for="(header, index) in headers.group_module" 
                                                        :key="index" 
                                                        v-show="parameter[header.name] != undefined"
                                                    >
                                                        <b 
                                                        v-if="header.name == 'total' || header.name == 'total_old' || header.name == 'retention' || header.name == 'crecimiento'">
                                                            {{ (header.name == 'retention' ||  header.name == 'crecimiento') ? parameter[header.name]+'%' :  parameter[header.name]}} 
                                                        </b>
                                                        <p v-else>{{ parameter[header.name] }}</p>
                                                    </td>
                                                </template>
                                                <template v-else>
                                                    <td 
                                                        style='text-align:center;' 
                                                        v-for="(header, index) in headers.group_module" 
                                                        :key="index" 
                                                        v-show="parameter[header.name] != undefined"
                                                    >
                                                        <b> {{ (header.name == 'retention' || header.name == 'crecimiento') ? parameter[header.name]+'%' :  parameter[header.name]}} </b>
                                                    </td>
                                                </template>
                                            </tr>
                                        </tbody>
                                    </table>                  
                                </b-row>
                            </b-card>
                        </b-tab>
                        <b-tab>
                            <template slot="title">
                                <strong>Reporte por Grupo de Compañia - Módulo</strong> 
                            </template>
                            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                                <b-row>
                                    <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
                                        <thead>
                                            <tr>
                                                <th v-for="(header, index) in headers.group_module" :key="`th-${index}`" class="text-center align-middle">
                                                    <b v-if="header.name == 'total' || header.name == 'total_old' || header.name == 'retention' || header.name == 'crecimiento'"> {{ header.label }} </b>
                                                    <p v-else>{{ header.label }}</p> 
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(parameter, key) in data2.group_module" :key="key">
                                                <template v-if="parameter['group'] != 'Total'">
                                                    <td style='text-align:center;' v-for="(header, index) in headers.group_module" :key="index">
                                                        <b v-if="header.name == 'total' || header.name == 'total_old' || header.name == 'retention' || header.name == 'crecimiento'"> {{ (header.name == 'retention' || header.name == 'crecimiento') ? parameter[header.name]+'%' :  parameter[header.name]}} </b>
                                                        <p v-else>{{ parameter[header.name] }}</p>
                                                    </td>
                                                </template>
                                                <template v-else>
                                                    <td style='text-align:center;' v-for="(header, index) in headers.group_module" :key="index">
                                                        <b> {{ (header.name == 'retention' || header.name == 'crecimiento') ? parameter[header.name]+'%' :  parameter[header.name]}} </b>
                                                    </td>
                                                </template>
                                            </tr>
                                        </tbody>
                                    </table>                
                                </b-row>
                            </b-card>
                        </b-tab>
                        <b-tab>
                            <template slot="title">
                                <strong>Reporte por Grupo de Compañia- Compañia - Módulos no contratados</strong> 
                            </template>
                            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                                <b-row>
                                    <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 13px;">
                                        <thead>
                                            <tr>
                                                <th v-for="(header, index) in headers.group_module_not" :key="`th-${index}`" class="text-center align-middle; font-size: 13px;">
                                                    {{ header.label }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(parameter, key) in data2.group_module_not" :key="key">
                                                <template>
                                                    <td style='text-align:center; font-size: 13px;' v-for="(header, index) in headers.group_module_not" :key="index">
                                                        {{ parameter[header.name] }}
                                                    </td>
                                                </template>
                                            </tr>
                                        </tbody>
                                    </table>                
                                </b-row>
                            </b-card>
                        </b-tab>
                    </b-tabs>
            </b-card>
        </div>
    </diV>
</template>

<script>
import Loading from "@/components/Inputs/Loading.vue";
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Alerts from '@/utils/Alerts.js';

export default {
    name: 'system-licenses-report',
    metaInfo: {
        title: 'Licencias - Reporte'
    },
    components:{
        Loading,
        FilterGeneral,
        VueAdvancedSelect
    },
    data () {
        return {
            isLoading: false,
            filters: [],
            headers: [],
            data2: {},
            options: 
            [
                { name: 'Total Actual', value: 'total'},
                { name: 'Total Anterior', value: 'total_old'},
                { name: 'Retención', value: 'retention'}
			],
            order: 'total'
        }
    },
    created() {
        this.fetch()
    },
    watch: {
        filters: {
            handler(val) {
                this.fetch()
                
            },
            deep: true,
        },
    },
    methods: {
        setFilters(value)
        { 
            this.filters = value
            this.fetch()
        },
        fetch()
        {
            if (!this.isLoading)
            {
                this.isLoading = true;

                let postData = Object.assign({}, {filters: this.filters, order: this.order});

                axios.post('/system/license/report', postData)
                .then(data => {
                    this.headers = data.data.headers;
                    this.data2 = data.data.data
                    this.isLoading = false;
                })
                .catch(error => {
                    console.log(error);
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });
            }
        },
    }
}
</script>