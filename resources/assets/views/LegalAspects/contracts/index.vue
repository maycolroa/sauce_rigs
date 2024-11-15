<template>
  	<div>
		<header-module
			title="CONTRATISTAS"
			url="legalaspects"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']) && auth.can['contracts_activities_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-activities'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Actividades</span>
										<br>
										<small class="text-muted">Actividades</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']) && auth.can['contracts_proyects_r'] && auth.proyectContract == 'SI'">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-proyects'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Proyectos</span>
										<br>
										<small class="text-muted">Proyectos</small>
									</span>
								</router-link>
							</div>
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']) && auth.can['contracts_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contractor'}">
									<i class="lnr lnr-user display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar contratistas</span>
										<br>
										<small class="text-muted">Administrar tus contratistas y/o arrendatarios</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']) && auth.can['contracts_myInformation']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-information'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Mi información</span>
										<br>
										<small class="text-muted">Registrar la información de mi contratista</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.hasRole['Contratista'] && auth.can['contracts_view_list_standards']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-list-check-qualification'}">
									<i class="lnr lnr-pencil display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Calificar lista de estándares mínimos</span>
										<br>
										<small class="text-muted">Calificación de tus lista de estándares mínimos.</small>
									</span>
								</router-link>
							</div>
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']) && auth.can['contracts_evaluations_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-evaluations'}">
									<i class="lnr lnr-magnifier display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administración de las evaluaciones</span>
										<br>
										<small class="text-muted">Administrar las evaluaciones de tus contratistas y/o arrendatarios.</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['contracts_informs_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-informs'}">
									<i class="lnr lnr-magnifier display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administración de los informes mensuales</span>
										<br>
										<small class="text-muted">Administrar los informes mensuales</small>
									</span>
								</router-link>
							</div>
							
							<!--<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['contracts_uploadFiles_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-upload-files'}">
									<i class="lnr lnr-upload display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Subida de archivos</span>
										<br>
										<small class="text-muted">Subida de archivos</small>
									</span>
								</router-link>
							</div>-->

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['contracts_uploadFiles_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-menu-upload-files'}">
									<i class="lnr lnr-upload display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Subida de Archivos y Comunicaciones</span>
										<br>
										<small class="text-muted">Subida de Archivos y Comunicaciones</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="/*(!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']) && */auth.can['contracts_training_r'] || auth.can['contracts_training_view_made']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-trainings'}">
									<i class="lnr lnr-book display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Capacitaciones</span>
										<br>
										<small class="text-muted">Capacitaciones</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']) && auth.can['contracts_employee_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-employees'}">
									<i class="lnr lnr-user display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar empleados</span>
										<br>
										<small class="text-muted">Administrar empleados</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']) && auth.can['contracts_evaluations_view_evaluations_made']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-evaluations-lessee'}">
									<i class="lnr lnr-magnifier display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Ver mis evaluaciones</span>
										<br>
										<small class="text-muted">Ver las evaluaciones realizadas por mi contratante</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['contracts_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-report'}">
									<i class="lnr lnr-chart-bars display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Reportes</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="(!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']) && auth.can['contracts_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-configurations'}"  >
									<i class="ion ion-md-settings display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Configuración</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="(!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']) && auth.can['contracts_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'contracts-customHelpers'}"  >
									<i class="ion ion-md-settings display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Ayudas</span>
									</span>
								</router-link>
							</div>
							<!--<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="auth.hasRole['Superadmin']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'contract-send-notification'}"  >
									<i class="ion ion-md-settings display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Envio de notificaciones</span>
									</span>
								</router-link>
							</div>-->
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
	name: "legal-aspects",
	metaInfo: {
		title: "Contratistas"
	},
  	methods: {
  	}
};
</script>
