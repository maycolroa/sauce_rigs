<template>
    <div>
        <template v-if="modules.length > 0">
            <b-row v-if="checkModule('biologicalmonitoring')">
                <b-col cols="6">
                    <router-link :to="{ name: 'biologicalmonitoring-menu'}" v-on:click.native="activityUser('Monitoreo biologico')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="riskmatrix" @mouseover="changeClassImage('riskmatrix', 'riskmatrix_hover')">
                                <img class="ui-w-80" src="/images/icono-negro-biomecanico.png" alt=""> MONITOREO BIOLÓGICO
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="riskmatrix_hover" @mouseleave="changeClassImage('riskmatrix_hover', 'riskmatrix')">
                                <img class="ui-w-80" src="/images/icono-rojo-biomecanico.png" alt=""> MONITOREO BIOLÓGICO
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>

            <div class="row" v-if="checkModule('biologicalmonitoring') && checkModule('reinstatements')">
                <div class="col-md-5 offset-md-1" style="padding-top: 40px; padding-bottom: 20px;">
                    <hr class="border-dark mt-0 mb-4">
                </div>
            </div>

            <b-row v-if="checkModule('reinstatements')">
                <b-col cols="8">
                    <router-link :to="{ name: 'preventiveoccupationalmedicine-reinstatements'}" v-on:click.native="activityUser('Reincorporaciones')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="reinstatements" @mouseover="changeClassImage('reinstatements', 'reinstatements_hover')">
                                <img class="ui-w-80" src="/images/reinstatements.png" alt=""> REINCORPORACIONES &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="reinstatements_hover" @mouseleave="changeClassImage('reinstatements_hover', 'reinstatements')">
                                <img class="ui-w-80" src="/images/reinstatements_hover.png" alt=""> REINCORPORACIONES &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>

            <div class="row" v-if="checkModule('reinstatements') && checkModule('absenteeism')">
                <div class="col-md-5 offset-md-1" style="padding-top: 40px; padding-bottom: 20px;">
                    <hr class="border-dark mt-0 mb-4">
                </div>
            </div>

            <b-row v-if="checkModule('absenteeism')">
                <b-col cols="8">
                    <router-link :to="{ name: 'preventiveoccupationalmedicine-absenteeism'}" v-on:click.native="activityUser('Ausentismo')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="absenteeism" @mouseover="changeClassImage('absenteeism', 'absenteeism_hover')">
                                <img class="ui-w-80" src="/images/absenteeism.png" alt=""> AUSENTISMO &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; 
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="absenteeism_hover" @mouseleave="changeClassImage('absenteeism_hover', 'absenteeism')">
                                <img class="ui-w-80" src="/images/absenteeism_hover.png" alt=""> AUSENTISMO &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                            </div>
                        </center>
                    </router-link>
                </b-col>
            </b-row>

            <div class="row" v-if="checkModule('absenteeism') || checkModule('reinstatements') || checkModule('biologicalmonitoring')">
                <div class="col-md-5 offset-md-1" style="padding-top: 40px; padding-bottom: 20px;">
                    <hr class="border-dark mt-0 mb-4">
                </div>
            </div>

            <b-row>
                <b-col cols="8">
                    <router-link :to="{ name: 'preventiveoccupationalmedicine-documentspreventive'}" v-on:click.native="activityUser('Documentos Medicina Preventiva')" class="text-dark cursor-pointer item-app-navbar">
                        <center>
                            <div style="font-size: 20px;" class="my-2 mx-2 text-center font-weight-bold" ref="documents" @mouseover="changeClassImage('documents', 'documents_hover')">
                                <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis normas.png" alt="">
                                DOCUMENTOS &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                            </div>
                            <div style="font-size: 20px; text-decoration: underline rgb(244, 75, 82); text-underline-position: under;" class="my-2 mx-2 text-center font-weight-bold imgHidden" ref="documents_hover" @mouseleave="changeClassImage('documents_hover', 'documents')">
                                <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis normas Over.png" alt="">
                                DOCUMENTOS &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
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
