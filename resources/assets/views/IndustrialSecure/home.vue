<template>
    <div>
        <template v-if="modules.length > 0">
            <b-row v-if="checkModule('dangerousconditions')">
                <b-col cols="6">
                    <router-link :to="{ name: 'industrialsecure-dangerousconditions'}" v-on:click.native="activityUser('Inspecciones')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="inspections" @mouseover="changeClassImage('inspections', 'inspections_hover')">
                                <img class="ui-w-80" src="/images/inspections.png" alt=""> INSPECCIONES &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="inspections_hover" @mouseleave="changeClassImage('inspections_hover', 'inspections')">
                                <img class="ui-w-80" src="/images/inspections_hover.png" alt=""> INSPECCIONES &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>

            <div class="row" v-if="checkModule('dangerousconditions') && checkModule('dangermatrix')">
                <div class="col-md-5 offset-md-1" style="padding-top: 40px; padding-bottom: 20px;">
                    <hr class="border-dark mt-0 mb-4">
                </div>
            </div>

            <b-row v-if="checkModule('dangermatrix')">
                <b-col cols="6">
                    <router-link :to="{ name: 'industrialsecure-dangermatrix-menu'}" v-on:click.native="activityUser('Matríz de peligros')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="dangermatrix" @mouseover="changeClassImage('dangermatrix', 'dangermatrix_hover')">
                                <img class="ui-w-80" src="/images/dangermatrix.png" alt=""> MATRIZ DE PELIGROS
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="dangermatrix_hover" @mouseleave="changeClassImage('dangermatrix_hover', 'dangermatrix')">
                                <img class="ui-w-80" src="/images/dangermatrix_hover.png" alt=""> MATRIZ DE PELIGROS
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>


            <div class="row" v-if="checkModule('dangermatrix') && checkModule('riskmatrix')">
                <div class="col-md-5 offset-md-1" style="padding-top: 40px; padding-bottom: 20px;">
                    <hr class="border-dark mt-0 mb-4">
                </div>
            </div>

            <b-row v-if="checkModule('riskmatrix')">
                <b-col cols="6">
                    <router-link :to="{ name: 'industrialsecure-riskmatrix-menu'}" v-on:click.native="activityUser('Matríz de peligros')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="riskmatrix" @mouseover="changeClassImage('riskmatrix', 'riskmatrix_hover')">
                                <img class="ui-w-80" src="/images/riskmatrix.png" alt=""> MATRIZ DE RIESGOS
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="riskmatrix_hover" @mouseleave="changeClassImage('riskmatrix_hover', 'riskmatrix')">
                                <img class="ui-w-80" src="/images/riskmatrix_hover.png" alt=""> MATRIZ DE RIESGOS
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>

            <div class="row" v-if="checkModule('riskmatrix') && checkModule('epp')">
                <div class="col-md-5 offset-md-1" style="padding-top: 40px; padding-bottom: 20px;">
                    <hr class="border-dark mt-0 mb-4">
                </div>
            </div>

            <b-row v-if="checkModule('epp')">
                <b-col cols="8">
                    <router-link :to="{ name: 'industrialsecure-epp'}" v-on:click.native="activityUser('Elementos de proteccion personal')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="epp" @mouseover="changeClassImage('epp', 'epp_hover')">
                                <img class="ui-w-80" src="/images/contracts.png" alt=""> ELEMENTOS DE PROTECCIÓN PERSONAL &nbsp;&nbsp; &nbsp;&nbsp; 
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="epp_hover" @mouseleave="changeClassImage('epp_hover', 'epp')">
                                <img class="ui-w-80" src="/images/contracts_hover.png" alt=""> ELEMENTOS DE PROTECCIÓN PERSONAL &nbsp;&nbsp; &nbsp;&nbsp; 
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>

            <div class="row" v-if="checkModule('epp') || checkModule('riskmatrix') || checkModule('dangermatrix') || checkModule('dangerousconditions')">
                <div class="col-md-5 offset-md-1" style="padding-top: 40px; padding-bottom: 20px;">
                    <hr class="border-dark mt-0 mb-4">
                </div>
            </div>

            <b-row>
                <b-col cols="8">
                    <router-link :to="{ name: 'industrialsecure-documentssecurity'}" v-on:click.native="activityUser('Documentos Seguridad')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="documents" @mouseover="changeClassImage('documents', 'documents_hover')">
                                <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis normas.png" alt="">
                                DOCUMENTOS &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; 
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="documents_hover" @mouseleave="changeClassImage('documents_hover', 'documents')">
                                <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis normas Over.png" alt="">
                                DOCUMENTOS &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>
        </template>
    </div>
</template>

<style>
.imgHidden {
    display: none;
}
</style>

<script>
  export default {
    props: {
      apps: {
        type: [Object, Array],
        required: true,
        default: function() {
          return [];
        }
      }
    },
    mounted() {
        console.log('entro')
    },
    computed: {
      modules() {
        if (Object.keys(this.apps).length > 0)
        {
            return this.apps[this.routeAppName] != undefined ? this.apps[this.routeAppName].modules : []
        }

        return [];
      }
    },
    methods: {
        checkModule(search) {
            let result = this.modules.filter(data => (data.name == search));
            
            if (result.length > 0)
                return true;
            else 
                return false;
        },
        changeClassImage(image, imageHover) {
            this.$refs[image].classList.add("imgHidden");
            this.$refs[imageHover].classList.remove("imgHidden");
        },
        activityUser(description)
        {
          this.userActivity(description)
        }
    }
  }
</script>
