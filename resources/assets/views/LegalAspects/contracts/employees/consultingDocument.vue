<template>
    <div>
        <header-module
            title="CONTRATISTAS"
            subtitle="CONSULTAR DOCUMENTOS"
        />
        <loading :display="isLoading"/>
        <div v-show="!isLoading">
            <b-row>
                <b-col>
                    <b-card border-variant="primary" title="" class="mb-3 box-shadow-none">
                        <h5 style="padding-left: 5%">Contratista:  {{tableDocuments.contract}}.</h5>
                        <h5 style="padding-left: 5%">Empleado:  {{tableDocuments.employee}}.</h5>
                        <p style="color: red; padding-left: 5%"><b>IMPORTANTE:</b> Solo se muestran los archivos más recientes subidos para cada documento</p>
                        <br>
                        <template v-for="(activity, index) in tableDocuments.activities" >
                            <b-row :key="`row-${index}`">
                                <div class="col-md-12" style="padding-bottom: 15px;">
                                    <div class="table-responsive">
                                        <h5 style="padding-left: 5%">Actividad:  {{activity.name}}.</h5>
                                        <div v-for="(document, index2) in activity.documents" :key="`row-${index2}`">
                                            <b-card border-variant="dark">
                                                <h6 style="padding-left: 5%">Documento:  {{document.name}}.</h6>
                                                <br>
                                                <table v-if="document.files.id" class="table table-bordered table-striped mb-0">
                                                    <thead>
                                                        <tr style="border: solid 2px black">
                                                            <th class="text-center align-middle" style="border: solid 2px black">Nombre de Archivo</th>
                                                            <th class="text-center align-middle" style="border: solid 2px black">Estado</th>
                                                            <th class="text-center align-middle" style="border: solid 2px black">Fecha de vencimiento</th>
                                                            <th class="text-center align-middle" style="border: solid 2px black">Fecha de carga</th>
                                                            <th class="text-center align-middle" style="border: solid 2px black">Opciones</th> 
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr style="border: solid 2px black">
                                                            <td class="text-center align-middle" style="border: solid 2px black">{{ document.files.name }}</td>
                                                            <td class="text-center align-middle" style="border: solid 2px black">{{ document.files.state }}</td>
                                                            <td class="text-center align-middle" style="border: solid 2px black">{{ document.files.expirationDate }}</td>
                                                            <td class="text-center align-middle" style="border: solid 2px black">{{ document.files.created_at }}</td>
                                                            <td>
                                                                <center><b-btn 
                                                                v-if="document.files.id" variant="outline-success icon-btn borderless" size="xs" v-b-tooltip.top title="Consultar archivo"><a :href="`/legalaspects/upload-files/view/${document.files.id}`" target='blank'><span class="ion ion-md-eye"></span></a></b-btn></center>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <p v-else><b>Sin archivo cargado</b></p>
                                            </b-card>
                                            <br><br>
                                        </div>
                                    </div>
                                </div>
                            </b-row>
                        </template>
                        <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
                            <template>
                                <b-btn variant="default" @click="refresh()" >Atras</b-btn>
                            </template>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        
        </div>
    </div>
</template>

<script>

import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';
import ChartBarMultiple from '@/components/ECharts/ChartBarMultiple.vue';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import ChartPie from '@/components/ECharts/ChartPieDinamicColors.vue';
import TableReport from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/TableReport.vue';

export default {
    name: 'legalaspects-contracts-employees-consulting-documents',
    metaInfo: {
        title: 'Contratistas - Consulta de documentos'
    },
    components:{
        Loading,
        FilterGeneral,
        ChartBarMultiple,
        VueAdvancedSelect,
        ChartPie,
        TableReport
    },
    data () {
        return {
            isLoading: false,
            tableDocuments: []
        }
    },
    created(){
        this.fetch()
    },
    methods: {
        fetch()
        {
            if (!this.isLoading)
            {
                this.isLoading = true;

                let postData = Object.assign({}, {employee_id: this.$route.params.id});
                axios.post('/legalAspects/employeeContract/consultingDocumentResumen', postData)
                .then(response => {
                    this.tableDocuments = response.data.data
                    this.isLoading = false;
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
            }
        },
        refresh() {
            if (this.auth.hasRole['Arrendatario'] || this.auth.hasRole['Contratista'])
            {
                this.$router.push({name: 'legalaspects-contracts-employees'});
            }
            else
            {
                window.location =  "/legalaspects/employees/view/contract/"+this.tableDocuments.contract_id
            }
        },
    }
}

</script>