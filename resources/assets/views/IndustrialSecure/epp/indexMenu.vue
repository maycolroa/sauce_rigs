<template>
  	<div>
    	<header-module
			title="ELEMENTOS DE PROTECCIÓN PERSONAL"
			url="industrialsecure"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['elements_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-elements'}">
									<i class="ion ion-md-glasses display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Elementos</span>
										<br>
										<small class="text-muted">Administrar Elementos</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['configuration_epp_c']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-settings'}">
									<i class="ion ion-md-settings display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Configuraciones</span>
										<br>
										<small class="text-muted">Administrar Configuraciones</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['location_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-locations'}">
									<i class="ion ion-md-locate display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Ubicaciones</span>
										<br>
										<small class="text-muted">Administrar Ubicaciones</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['elements_c'] && auth.inventaryEpp == 'SI'">
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
		title: "Elementos de protección personal"
	},
  	methods: {
  	}
};
</script>
