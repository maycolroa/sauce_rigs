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
                <div>
                    <filter-general 
                        v-model="filters" 
                        configName="system-licenses-report" />
                </div>
                <b-row>
                    <b-card style="width:95%">
                        <div style= "margin-bottom: 20px;">
                            <h4><b>Reporte General</b></h4>
                            <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
                                <thead>
                                    <tr>
                                        <th v-for="(header, index) in headers.general" :key="`th-${index}`" class="text-center align-middle">
                                            {{ header.label }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style='text-center' v-for="(parameter, key) in data2.general" :key="key">
                                        <td style='text-align:center;' v-for="(header, index) in headers.general" :key="index">
                                            {{ parameter[header.name] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </b-card>
                </b-row>
                <b-row>
                    <b-card style="width:95%">
                        <div style= "margin-bottom: 20px;">
                            <h4><b>Reporte por Módulo</b></h4>
                            <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
                                <thead>
                                    <tr>
                                        <th v-for="(header, index) in headers.module" :key="`th-${index}`" class="text-center align-middle">
                                            {{ header.label }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(parameter, key) in data2.module" :key="key">
                                        <td style='text-align:center;' v-for="(header, index) in headers.module" :key="index">
                                            {{ parameter[header.name] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </b-card>
                </b-row>
                <b-row>
                    <b-card style="width:95%">
                        <div style= "margin-bottom: 20px;">
                            <h4><b>Reporte por Grupo de Compañia</b></h4>
                            <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
                                <thead>
                                    <tr>
                                        <th v-for="(header, index) in headers.group" :key="`th-${index}`" class="text-center align-middle">
                                            {{ header.label }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(parameter, key) in data2.group" :key="key">
                                        <td style='text-align:center;' v-for="(header, index) in headers.group" :key="index">
                                            {{ parameter[header.name] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </b-card>
                </b-row>
                <b-row>
                    <b-card style="width:95%">
                        <div style= "margin-bottom: 20px;">
                            <h4><b>Reporte por Grupo de Compañia - Módulo</b></h4>
                            <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
                                <thead>
                                    <tr>
                                        <th v-for="(header, index) in headers.group_module" :key="`th-${index}`" class="text-center align-middle">
                                            {{ header.label }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(parameter, key) in data2.group_module" :key="key">
                                        <td style='text-align:center;' v-for="(header, index) in headers.group_module" :key="index">
                                            {{ parameter[header.name] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </b-card>
                </b-row>
            </b-card>
        </div>
    </diV>
</template>

<script>
import Loading from "@/components/Inputs/Loading.vue";
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';
import Alerts from '@/utils/Alerts.js';

export default {
    name: 'system-licenses-report',
    metaInfo: {
        title: 'Licencias - Reporte'
    },
    components:{
        Loading,
        FilterGeneral
    },
    data () {
        return {
            isLoading: false,
            filters: [],
            headers: [],
            data2: {},
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

                let postData = Object.assign({}, {filters: this.filters});

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