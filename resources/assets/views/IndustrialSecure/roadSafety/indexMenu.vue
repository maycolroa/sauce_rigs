<template>
  	<div>
    	<header-module
			title="SEGURIDAD VIAL"
			url="industrialsecure"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['roadsafety_documents_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-roadsafety-documents'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Documentos</span>
										<br>
										<small class="text-muted">Administrar Documentos</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['roadsafety_vehicles_c']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-roadsafety-vehicles'}">
									<i class="lnr lnr-car display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Vehiculos</span>
										<br>
										<small class="text-muted">Administrar Vehiculos</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['roadsafety_drivers_c']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-roadsafety-drivers'}">
									<i class="lnr lnr-car display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Conductores</span>
										<br>
										<small class="text-muted">Administrar Conductores</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-roadsafety-customHelpers'}"  >
									<i class="ion ion-md-settings display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Ayudas</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['roadsafety_inspections_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'roadSafety-inspections-menu'}">
									<i class="lnr lnr-chart-bars display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Inspecciones</span>
										<br>
										<small class="text-muted">Administrar Inspecciones</small>
									</span>
								</router-link>
							</div>

							<!--<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['elements_c'] && auth.inventaryEpp == 'SI'">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-elements-import-balance-inicial'}">
									<i class="ion ion-md-cloud-upload display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Saldos Iniciales</span>
										<br>
										<small class="text-muted">Administrar Saldos Iniciales</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['transaction_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-transactions-menu'}">
									<i class="ion ion-md-git-compare display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Transacciones</span>
										<br>
										<small class="text-muted">Administrar Transacciones</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['transaction_r'] && auth.inventaryEpp == 'SI'">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-reports'}">
									<i class="lnr lnr-chart-bars display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Reportes</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['elements_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'epp-customHelpers'}">
									<i class="ion ion-md-settings display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Ayudas</span>
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
	name: "audiometry",
	metaInfo: {
		title: "Seguridad Vial"
	},
  	methods: {
  	}
};
</script>
