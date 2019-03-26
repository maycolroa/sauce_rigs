<template>
  	<div>
    	<h4 class="font-weight-bold mb-4">Gestión de contratistas</h4>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-create'}">
									<i class="lnr lnr-user display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Crear contratista - arrendatario</span>
										<br>
										<small class="text-muted">Creación de los usuarios para contratistas y/o arrendatarios.</small>
									</span>
								</router-link>
							</div>
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-create'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administración de las listas de chequeo</span>
										<br>
										<small class="text-muted">Ver, editar y administrar las listas de chequeo de tus contratistas y/o arrendatarios.</small>
									</span>
								</router-link>
							</div>
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-create'}">
									<i class="lnr lnr-pencil display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Realizar listas de chequeo</span>
										<br>
										<small class="text-muted">Creación de tus listas de chequeo para tus arrendatarios.</small>
									</span>
								</router-link>
							</div>
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-create'}">
									<i class="lnr lnr-users display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administración de usuarios</span>
										<br>
										<small class="text-muted">Ver, editar y administrar tus usuarios contratistas o arrendatarios.</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-typesrating'}">
									<i class="lnr lnr-layers display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administración de los tipos de calificación</span>
										<br>
										<small class="text-muted">Ver, editar y administrar los tipos de calificaciones de las evaluaciones.</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-evaluations'}">
									<i class="lnr lnr-magnifier display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administración de las evaluaciones</span>
										<br>
										<small class="text-muted">Ver, editar y administrar las evaluaciones de tus contratistas y/o arrendatarios.</small>
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
	name: "users",
	metaInfo: {
		title: "Usuarios"
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
