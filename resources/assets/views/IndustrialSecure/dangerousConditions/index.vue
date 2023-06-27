<template>
  	<div>
    	<header-module
			title="INSPECCIONES"
			url="industrialsecure"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['ph_reports_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'dangerousconditions-reports'}">
									<i class="lnr lnr-chart-bars display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Inspecciones no Planeadas</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="auth.can['ph_inspections_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'dangerousconditions-inspections'}"  >
									<i class="far fa-newspaper display-4 d-block text-primary" ></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Inspecciones Planeadas</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="auth.can['accidentsWork_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-accidentswork'}">
									<i class="far fa-newspaper display-4 d-block text-primary" ></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Incidentes y accidentes</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="auth.can['ph_inspections_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'dangerousconditions-incentives'}"  >
									<i class="far fa-file-pdf display-4 d-block text-primary" ></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Cargar Incentivos</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="auth.can['ph_inspections_c']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-configurations'}"  >
									<i class="ion ion-md-settings display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Configuración</span>
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
	name: "dangerousConditions",
	metaInfo: {
		title: "Inspecciones"
	},
  	methods: {
  	}
};
</script>
