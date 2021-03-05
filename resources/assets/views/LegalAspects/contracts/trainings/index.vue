<template>
  	<div>
		<header-module
			title="CAPACITACIONES"
			url="legalaspects-contracts"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']) && auth.can['contracts_training_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-trainings-virtual'}">
									<i class="lnr lnr-laptop display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Capacitaciones Virtuales</span>
										<br>
										<small class="text-muted">Capacitaciones Virtuales</small>
									</span>
								</router-link>
							</div>

							<!--<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="(!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']) && auth.can['contracts_training_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-contracts-trainings-virtual'}">
									<i class="lnr lnr-users display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Capacitaciones Presenciales</span>
										<br>
										<small class="text-muted">Capacitaciones Presenciales</small>
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
