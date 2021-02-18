<template>
  	<div>
    	<header-module
			title="SISTEMA"
      		subtitle="MONITOREO DE CLIENTES"
      		url="system"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['customerMonitoring_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'system-customermonitoring-dangerousconditions'}">
									<i class="far fa-newspaper display-4 d-block text-primary" ></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Monitoreo de clientes- Condiciones Peligrosas</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="auth.can['customerMonitoring_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'system-customermonitoring-reinstatements'}"  >
									<i class="far fa-newspaper display-4 d-block text-primary" ></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Monitoreo de clientes- Reincorporaciones</span>
									</span>
								</router-link>
							</div>

							<!--<div class="d-flex col-sm-6 col-md-4 c ol-lg-6 align-items-center" v-if="auth.can['customerMonitoring_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'system-customermonitoring-automatics-send'}"  >
									<i class="far fa-newspaper display-4 d-block text-primary" ></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Monitoreo de clientes- Envíos Automáticos</span>
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
	name: "customerMonitoring",
	metaInfo: {
		title: "Monitoreo de clientes"
	},
  	methods: {
  	}
};
</script>
