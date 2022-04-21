<template>
    <div>
        <template v-if="modules.length > 0">
            <b-row v-if="checkModule('contracts')">
                <b-col cols="6">
                    <router-link :to="{ name: 'legalaspects-contracts'}" v-on:click.native="activityUser('Contratistas')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="contracts" @mouseover="changeClassImage('contracts', 'contracts_hover')">
                                <img class="ui-w-80" src="/images/contracts.png" alt=""> CONTRATISTAS
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="contracts_hover" @mouseleave="changeClassImage('contracts_hover', 'contracts')">
                                <img class="ui-w-80" src="/images/contracts_hover.png" alt=""> CONTRATISTAS
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>

            <div class="row" v-if="checkModule('contracts') && checkModule('legalmatrix')">
                <div class="col-md-5 offset-md-1" style="padding-top: 40px; padding-bottom: 20px;">
                    <hr class="border-dark mt-0 mb-4">
                </div>
            </div>

            <b-row v-if="checkModule('legalmatrix')">
                <b-col cols="6">
                    <router-link :to="{ name: 'legalaspects-legalmatrix'}" v-on:click.native="activityUser('Matríz legal')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="legalmatrix" @mouseover="changeClassImage('legalmatrix', 'legalmatrix_hover')">
                                <img class="ui-w-80" src="/images/legalmatrix.png" alt=""> MATRIZ LEGAL
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="legalmatrix_hover" @mouseleave="changeClassImage('legalmatrix_hover', 'legalmatrix')">
                                <img class="ui-w-80" src="/images/legalmatrix_hover.png" alt=""> MATRIZ LEGAL
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>

            <div class="row" v-if="checkModule('legalmatrix')">
                <div class="col-md-5 offset-md-1" style="padding-top: 40px; padding-bottom: 20px;">
                    <hr class="border-dark mt-0 mb-4">
                </div>
            </div>

            <b-row>
                <b-col cols="8">
                    <router-link :to="{ name: 'legalaspects-documentslegals'}" v-on:click.native="activityUser('Documentos Legales')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="documents" @mouseover="changeClassImage('documents', 'documents_hover')">
                                <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis normas.png" alt="">
                                DOCUMENTOS &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="documents_hover" @mouseleave="changeClassImage('documents_hover', 'documents')">
                                <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis normas Over.png" alt="">
                                DOCUMENTOS &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
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
