<template>
  	<div>
		<header-module
			title="REINCORPORACIONES"
			url="preventiveoccupationalmedicine"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['reinc_restrictions_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'reinstatements-restrictions'}">
									<i class="lnr lnr-cross-circle display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar Restricciones</span>
										<br>
										<small class="text-muted">Administrar Restricciones de mi compañia</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['reinc_checks_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'reinstatements-checks'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar Casos</span>
										<br>
										<small class="text-muted">Administrar Casos</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['reinc_disease_origin_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'reinstatements-disease-origin'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar {{ keywordCheck('disease_origin') }}</span>
										<br>
										<small class="text-muted">Administrar {{ keywordCheck('disease_origin') }}</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['reinc_origin_advisor_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'reinstatements-origin-advisor'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar Procedencias de recomendaciones</span>
										<br>
										<small class="text-muted">Administrar Procedencias de recomendaciones</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['reinc_labor_conclusion_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'reinstatements-labor-conclusions'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar Conclusiones laborales</span>
										<br>
										<small class="text-muted">Administrar Conclusiones laborales</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['reinc_medical_conclusion_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'reinstatements-medical-conclusions'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar Conclusiones médicas</span>
										<br>
										<small class="text-muted">Administrar Conclusiones médicas</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['reinc_checks_informs']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'reinstatements-informs'}">
									<i class="lnr lnr-chart-bars display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Ver Reportes</span>
										<br>
										<small class="text-muted">Ver Reportes</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(auth.can['reinc_checks_r'] && !auth.hasRole['Rol Visor Reincorporaciones'] && !auth.hasRole['Rol Visor Reincorporaciones Recomendaciones']) || (auth.can['reinc_checks_configuration'] && !auth.hasRole['Rol Visor Reincorporaciones'] && !auth.hasRole['Rol Visor Reincorporaciones Recomendaciones'])">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'reinstatements-configuration'}">
									<i class="ion ion-md-settings display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Configuración</span>
										<br>
										<small class="text-muted">Administrar Configuraciones</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['reinc_checks_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'reinstatements-customHelpers'}">
									<i class="ion ion-md-settings display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Ayudas</span>
										<br>
										<small class="text-muted">Visualizar ayudas</small>
									</span>
								</router-link>
							</div>
						</div>
					</div>
				</b-card-header>
			</b-card>
    	</div>
  	</div>
</template>

<script>
import Alerts from "@/utils/Alerts.js";

export default {
	name: "biologicalmonitoring-reinstatements",
	metaInfo: {
		title: "Reincorporaciones"
	},
  	methods: {
  	}
};
</script>
