<template>
  	<div>
    	<h4 class="font-weight-bold mb-4">Contratistas</h4>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-create'}">
									<i class="lnr lnr-user display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Crear contratista o arrendatario</span>
										<br>
										<small class="text-muted">Creación de los usuarios para contratistas y/o arrendatarios.</small>
									</span>
								</router-link>
							</div>
							
							<!-- <div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-list-check-items'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administración de las listas de chequeo</span>
										<br>
										<small class="text-muted">Ver, editar y administrar las listas de chequeo de tus contratistas y/o arrendatarios.</small>
									</span>
								</router-link>
							</div> -->

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-list-check-items'}">
									<i class="lnr lnr-pencil display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Calificar lista de estándares mínimos</span>
										<br>
										<small class="text-muted">Calificación de tus lista de estándares mínimos.</small>
									</span>
								</router-link>
							</div>

							<!-- <div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-create'}">
									<i class="lnr lnr-users display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administración de usuarios</span>
										<br>
										<small class="text-muted">Ver, editar y administrar tus usuarios contratistas o arrendatarios.</small>
									</span>
								</router-link>
							</div> -->

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-complete-information', params: {id: 3}}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Completar formulario del contratista</span>
										<br>
										<small class="text-muted">Completar información por el contratista o arrendatario ingresado por primera vez a la plataforma</small>
									</span>
								</router-link>
							</div>
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-evaluations'}">
									<i class="lnr lnr-magnifier display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administración de las evaluaciones</span>
										<br>
										<small class="text-muted">Ver, editar y administrar las evaluaciones de tus contratistas y/o arrendatarios.</small>
									</span>
								</router-link>
							</div>
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-upload-files'}">
									<i class="lnr lnr-upload display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Subida de archivos</span>
										<br>
										<small class="text-muted">Subida de archivos</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-evaluations-lessee'}">
									<i class="lnr lnr-magnifier display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Ver mis evaluaciones</span>
										<br>
										<small class="text-muted">Ver las evaluaciones realizadas por mi contratante</small>
									</span>
								</router-link>
							</div>
						</div>
						<!-- <b-btn :to="{name:'legalaspects-contracts-create'}" variant="primary">Crear Usuario</b-btn>
						<b-btn :to="{name:'legalaspects-contracts-create'}" variant="primary">Lista de Chequeo</b-btn>-->
					</div>
				</b-card-header>
				<!--<b-card-body><vue-table configName="administrative-users"></vue-table></b-card-body>-->
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
		exportUsers() {
		axios
			.post("/administration/users/export")
			.then(response => {
				Alerts.warning(
					"Información",
					"Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso."
				);
			})
			.catch(error => {
				Alerts.error(
					"Error",
					"Se ha generado un error en el proceso, por favor contacte con el administrador"
				);
			});
		}
  	}
};
</script>
