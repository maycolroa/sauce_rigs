<template>
  	<div>
    	<header-module
			title="INSPECCIONES PLANEADAS REPORTES"
			url="dangerousconditions-inspections"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'dangerousconditions-inspection-report'}">
									<i class="lnr lnr-chart-bars display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Reportes Generales</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="auth.can['ph_inspections_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'dangerousconditions-inspection-report-gestion'}"  >
									<i class="far fa-newspaper display-4 d-block text-primary" ></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Reporte Gestión de Cumplimiento</span>
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
	name: "dangerousConditions-reports-inspections",
	metaInfo: {
		title: "Reportes Inspecciones Planeadas"
	},
  	methods: {
  	}
};
</script>
