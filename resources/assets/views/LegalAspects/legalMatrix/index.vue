<template>
  <div>
		<div class="row">
			<div class="col-md-8">
				<span class="title-module">MATRIZ</span><br>
				<span class="title-module">LEGAL</span><br>
				<img class="filete-module" src="/images/Sauce-ML Filete.png">
			</div>
			<div class="col-md-4 text-right cursor-pointer">
				<router-link :to="{ name: 'legalaspects' }" class="text-dark cursor-pointer item-app-navbar">
					Volver atrás <img class="ui-w-50" src="~@/images/Sauce-ML Boton Volver Atras.png">
				</router-link>
			</div>
		</div>

		<b-row>
      <b-col cols="3" class="col-item" v-if="auth.can['laws_qualify']">
                <router-link :to="{name:'legalaspects-lm-law-configuration'}" class="text-dark cursor-pointer item-app-navbar">
                        <br>
                    <div class="my-2 mx-2" ref="custom_law_configuration" @mouseover="changeClassImage('custom_law_configuration', 'custom_law_configuration_hover')">
                        <i class="ion ion-md-settings display-3 d-block text-dark"></i>
                        <div class="font-weight-bold pt-1">
                          Configuración
                        </div>
            <small class="text-muted">Configuración</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_law_configuration_hover" @mouseleave="changeClassImage('custom_law_configuration_hover', 'custom_law_configuration')">
                        <i class="ion ion-md-settings display-3 d-block text-primary"></i>

                        <div class="font-weight-bold pt-1">
                          Configuración 
                        </div>
            <small class="text-muted">Configuración</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

      <b-col cols="3" class="col-item" v-if="auth.can['laws_qualify']">
                <router-link :to="{name:'legalMatrix-customHelpers'}" class="text-dark cursor-pointer item-app-navbar">
                        <br>
                    <div class="my-2 mx-2" ref="custom_law_configuration_help" @mouseover="changeClassImage('custom_law_configuration_help', 'custom_law_configuration_help_hover')">
                        <i class="ion ion-md-settings display-3 d-block text-dark"></i>
                        <div class="font-weight-bold pt-1">
                          Ayudas
                        </div>
            <small class="text-muted">Ayudas</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_law_configuration_help_hover" @mouseleave="changeClassImage('custom_law_configuration_help_hover', 'custom_law_configuration_help')">
                        <i class="ion ion-md-settings display-3 d-block text-primary"></i>

                        <div class="font-weight-bold pt-1">
                          Ayudas 
                        </div>
            <small class="text-muted">Ayudas</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>
      
      <b-col cols="3" class="col-item" v-if="auth.can['interests_config']">
                <router-link :to="{name:'legalaspects-lm-interest-myinterests'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="config_interest" @mouseover="changeClassImage('config_interest', 'config_interest_hover')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Establecer mis intereses.png" alt="" >

                        <div class="font-weight-bold pt-1">
                          Establecer mis intereses  
                        </div>
            <small class="text-muted">Establecer mis intereses</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="config_interest_hover" @mouseleave="changeClassImage('config_interest_hover', 'config_interest')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Establecer mis intereses Over.png" alt="">

                        <div class="font-weight-bold pt-1">
                          Establecer mis intereses  
                        </div>
            <small class="text-muted">Establecer mis intereses</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

      <b-col cols="3" class="col-item" v-if="auth.can['laws_qualify'] || auth.can['laws_qualify_view']">
                <router-link :to="{name:'legalaspects-lm-law-qualify'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="custom_law_qualify" @mouseover="changeClassImage('custom_law_qualify', 'custom_law_qualify_hover')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Calificar Normas.png" alt="" >

                        <div class="font-weight-bold pt-1">
                          Mi Matríz Legal 
                        </div>
            <small class="text-muted">Mi matríz legal</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_law_qualify_hover" @mouseleave="changeClassImage('custom_law_qualify_hover', 'custom_law_qualify')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Calificar Normas Over.png" alt="">

                        <div class="font-weight-bold pt-1">
                          Mi Matríz Legal 
                        </div>
            <small class="text-muted">Mi matríz legal</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

      <b-col cols="3" class="col-item" v-if="auth.can['laws_report_r']">
                <router-link :to="{name:'legalaspects-lm-law-report'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="custom_law_report" @mouseover="changeClassImage('custom_law_report', 'custom_law_report_hover')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Reporte.png" alt="" >

                        <div class="font-weight-bold pt-1">
                          Reporte 
                        </div>
            <small class="text-muted">Reporte de cumplimiento</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_law_report_hover" @mouseleave="changeClassImage('custom_law_report_hover', 'custom_law_report')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Reporte Over.png" alt="">

                        <div class="font-weight-bold pt-1">
                          Reporte 
                        </div>
            <small class="text-muted">Reporte de cumplimiento</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

      <b-col cols="3" class="col-item" v-if="auth.can['interestsCustom_r']">
                <router-link :to="{name:'legalaspects-lm-interest-company'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="custom_interest" @mouseover="changeClassImage('custom_interest', 'custom_interest_hover')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis intereses B.png" alt="" >

                        <div class="font-weight-bold pt-1">
                          Crear intereses 
                        </div>
            <small class="text-muted">Administrar mis intereses</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_interest_hover" @mouseleave="changeClassImage('custom_interest_hover', 'custom_interest')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis intereses B Over.png" alt="">

                        <div class="font-weight-bold pt-1">
                          Crear intereses 
                        </div>
            <small class="text-muted">Administrar mis intereses</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

      <b-col cols="3" class="col-item" v-if="auth.can['systemApplyCustom_r']">
                <router-link :to="{name:'legalaspects-lm-system-apply-company'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="custom_system" @mouseover="changeClassImage('custom_system', 'custom_system_hover')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Mis sistemas.png" alt="" >

                        <div class="font-weight-bold pt-1">
                          Crear mis sistemas  
                        </div>
            <small class="text-muted">Administrar mis sistemas</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_system_hover" @mouseleave="changeClassImage('custom_system_hover', 'custom_system')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Mis sistemas over.png" alt="">

                        <div class="font-weight-bold pt-1">
                          Crear mis sistemas  
                        </div>
            <small class="text-muted">Administrar mis sistemas</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

      <b-col cols="3" class="col-item" v-if="auth.can['lawsCustom_r']">
                <router-link :to="{name:'legalaspects-lm-law-company'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="custom_law" @mouseover="changeClassImage('custom_law', 'custom_law_hover')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis normas.png" alt="" >

                        <div class="font-weight-bold pt-1">
                          Crear mis normas  
                        </div>
            <small class="text-muted">Administrar mis normas</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_law_hover" @mouseleave="changeClassImage('custom_law_hover', 'custom_law')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin mis normas Over.png" alt="">

                        <div class="font-weight-bold pt-1">
                          Crear mis normas  
                        </div>
            <small class="text-muted">Administrar mis normas</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

      <b-col cols="3" class="col-item" v-if="auth.can['entitiesCustom_r']">
                <router-link :to="{name:'legalaspects-lm-entity-company'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="custom_entity" @mouseover="changeClassImage('custom_entity', 'custom_entity_hover')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin entidades.png" alt="" >

                        <div class="font-weight-bold pt-1">
                          Administrar mis entidades 
                        </div>
            <small class="text-muted">Administrar mis entidades</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_entity_hover" @mouseleave="changeClassImage('custom_entity_hover', 'custom_entity')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin entidades over.png" alt="">

                        <div class="font-weight-bold pt-1">
                          Administrar mis entidades 
                        </div>
            <small class="text-muted">Administrar mis entidades</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

      <b-col cols="3" class="col-item" v-if="auth.can['typesCustom_r']">
                <router-link :to="{name:'legalaspects-lm-type-company'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="custom_type" @mouseover="changeClassImage('custom_type', 'custom_type_hover')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin entidades.png" alt="" >

                        <div class="font-weight-bold pt-1">
                          Administrar mis tipos de normas 
                        </div>
            <small class="text-muted">Administrar mis tipos de normas</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_type_hover" @mouseleave="changeClassImage('custom_type_hover', 'custom_type')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin entidades over.png" alt="">

                        <div class="font-weight-bold pt-1">
                          Administrar mis tipos de normas 
                        </div>
            <small class="text-muted">Administrar mis tipos de normas</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

			<b-col cols="3" class="col-item" v-if="auth.can['risksAspects_r']">
                <router-link :to="{name:'legalaspects-lm-riskaspect'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="riskaspect" @mouseover="changeClassImage('riskaspect', 'riskaspect_hover')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin Temas ambientales.png" alt="" >

                      	<div class="font-weight-bold pt-1">
	                        Administrar temas ambientales	
                      	</div>
						<small class="text-muted">Administrar riesgos y/o aspectos ambientales del sistema</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="riskaspect_hover" @mouseleave="changeClassImage('riskaspect_hover', 'riskaspect')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin Temas ambientales over.png" alt="">

                      	<div class="font-weight-bold pt-1">
	                        Administrar temas ambientales	
                      	</div>
						<small class="text-muted">Administrar riesgos y/o aspectos ambientales del sistema</small>
						<div style="padding-top: 10px;">
							<hr class="border-divider mt-0 mb-4">
						</div>
                    </div>
                </router-link>
      </b-col>

			<b-col cols="3" class="col-item" v-if="auth.can['sstRisks_r']">
                <router-link :to="{name:'legalaspects-lm-sstrisk'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="sstrisk" @mouseover="changeClassImage('sstrisk', 'sstrisk_hover')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin Temas SST.png" alt="" >

                      	<div class="font-weight-bold pt-1">
	                        Administrar temas SST	
                      	</div>
						<small class="text-muted">Administrar temas SST del sistema</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="sstrisk_hover" @mouseleave="changeClassImage('sstrisk_hover', 'sstrisk')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin Temas SST Over.png" alt="">

                      	<div class="font-weight-bold pt-1">
	                        Administrar temas SST	
                      	</div>
						<small class="text-muted">Administrar temas SST del sistema</small>
						<div style="padding-top: 10px;">
							<hr class="border-divider mt-0 mb-4">
						</div>
                    </div>
                </router-link>
      </b-col>

      <b-col cols="3" class="col-item" v-if="auth.can['risksAspects_r']">
                <router-link :to="{name:'legalaspects-lm-type'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="custom_type" @mouseover="changeClassImage('custom_type', 'custom_type_hover')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin entidades.png" alt="" >

                        <div class="font-weight-bold pt-1">
                          Administrar Tipos de normas 
                        </div>
            <small class="text-muted">Administrar Tipos de normas</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="custom_type_hover" @mouseleave="changeClassImage('custom_type_hover', 'custom_type')">
                        <img class="ui-w-80" src="/images/Sauce-ML Ico Admin entidades over.png" alt="">

                        <div class="font-weight-bold pt-1">
                          Administrar Tipos de normas 
                        </div>
            <small class="text-muted">Administrar Tipos de normas</small>
            <div style="padding-top: 10px;">
              <hr class="border-divider mt-0 mb-4">
            </div>
                    </div>
                </router-link>
      </b-col>

			<b-col cols="3" class="col-item" v-if="auth.can['systemApply_r']">
                <router-link :to="{name:'legalaspects-lm-system-apply'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="system" @mouseover="changeClassImage('system', 'system_hover')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin sistemas.png" alt="" >

                      	<div class="font-weight-bold pt-1">
	                        Administrar sistemas	
                      	</div>
						<small class="text-muted">Administrar sistemas</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="system_hover" @mouseleave="changeClassImage('system_hover', 'system')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin sistemas OVer.png" alt="">

                      	<div class="font-weight-bold pt-1">
	                        Administrar sistemas	
                      	</div>
						<small class="text-muted">Administrar sistemas</small>
						<div style="padding-top: 10px;">
							<hr class="border-divider mt-0 mb-4">
						</div>
                    </div>
                </router-link>
      </b-col>

			<b-col cols="3" class="col-item" v-if="auth.can['entities_r']">
                <router-link :to="{name:'legalaspects-lm-entity'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="entity" @mouseover="changeClassImage('entity', 'entity_hover')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin entidades.png" alt="" >

                      	<div class="font-weight-bold pt-1">
	                        Administrar entidades	
                      	</div>
						<small class="text-muted">Administrar entidades</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="entity_hover" @mouseleave="changeClassImage('entity_hover', 'entity')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin entidades over.png" alt="">

                      	<div class="font-weight-bold pt-1">
	                        Administrar entidades	
                      	</div>
						<small class="text-muted">Administrar entidades</small>
						<div style="padding-top: 10px;">
							<hr class="border-divider mt-0 mb-4">
						</div>
                    </div>
                </router-link>
      </b-col>

			<b-col cols="3" class="col-item" v-if="auth.can['interests_r']">
                <router-link :to="{name:'legalaspects-lm-interest'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="interest" @mouseover="changeClassImage('interest', 'interest_hover')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin intereses.png" alt="" >

                      	<div class="font-weight-bold pt-1">
	                        Administrar intereses	
                      	</div>
						<small class="text-muted">Administrar intereses del sistema</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="interest_hover" @mouseleave="changeClassImage('interest_hover', 'interest')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin intereses Over.png" alt="">

                      	<div class="font-weight-bold pt-1">
	                        Administrar intereses	
                      	</div>
						<small class="text-muted">Administrar intereses del sistema</small>
						<div style="padding-top: 10px;">
							<hr class="border-divider mt-0 mb-4">
						</div>
                    </div>
                </router-link>
      </b-col>

			<b-col cols="3" class="col-item" v-if="auth.can['laws_r']">
                <router-link :to="{name:'legalaspects-lm-law'}" class="text-dark cursor-pointer item-app-navbar">
                    <div class="my-2 mx-2" ref="law" @mouseover="changeClassImage('law', 'law_hover')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin normas.png" alt="" >

                      	<div class="font-weight-bold pt-1">
	                        Administrar normas	
                      	</div>
						<small class="text-muted">Administrar normas del sistema</small>
                    </div>
                    <div class="my-2 mx-2 imgHidden" ref="law_hover" @mouseleave="changeClassImage('law_hover', 'law')">
                      	<img class="ui-w-80" src="/images/Sauce-ML Ico Admin normas Over.png" alt="">

                      	<div class="font-weight-bold pt-1">
	                        Administrar normas	
                      	</div>
						<small class="text-muted">Administrar normas del sistema</small>
						<div style="padding-top: 10px;">
							<hr class="border-divider mt-0 mb-4">
						</div>
                    </div>
                </router-link>
      </b-col>
	
		</b-row>
  </div>
</template>

<style scoped>
.title-module {
	font-size: 1.813rem;
	font-family: sans-serif;
    letter-spacing: 5px;
    color: #f44b52;
	padding-left: 10px;
}
.filete-module {
	width: 300px; 
	padding-right: 0px;
}
.border-divider {
	border-color: #f44b52;
	border-top: 5px solid;
}
.imgHidden {
    display: none;
}
.text-dark[href]:hover {
	color: #f44b52 !important;
}
.col-item {
	min-height: 200px;
}
</style>

<script>
import Alerts from "@/utils/Alerts.js";

export default {
	name: "legal-aspects",
	metaInfo: {
		title: "Matriz Legal"
	},
  	methods: {
		changeClassImage(image, imageHover) {
            this.$refs[image].classList.add("imgHidden");
            this.$refs[imageHover].classList.remove("imgHidden");
        }
  	}
};
</script>
