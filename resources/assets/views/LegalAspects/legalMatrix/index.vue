<template>
  	<div>
    	<h4 class="font-weight-bold mb-4">Matriz Legal</h4>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-lm-interest'}">
									<i class="lnr lnr-list display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar intereses</span>
										<br>
										<small class="text-muted">Administrar intereses del sistema</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-lm-riskaspect'}">
									<i class="lnr lnr-cloud-sync display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar Riesgos/Aspectos Ambientales</span>
										<br>
										<small class="text-muted">Administrar Riesgos/Aspectos Ambientales del sistema</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-lm-sstrisk'}">
									<i class="lnr lnr-lighter display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar Riesgos SST</span>
										<br>
										<small class="text-muted">Administrar Riesgos SST del sistema</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-lm-entity'}">
									<i class="lnr lnr-apartment display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar Entidades</span>
										<br>
										<small class="text-muted">Administrar Entidades del sistema</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-lm-interest-myinterests'}">
									<i class="lnr lnr-checkmark-circle display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Establecer mis intereses</span>
										<br>
										<small class="text-muted">Establecer mis intereses</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'legalaspects-lm-law'}">
									<i class="lnr lnr-user display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Administrar Normas</span>
										<br>
										<small class="text-muted">Administrar Normas del sistema</small>
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
	name: "legal-aspects",
	metaInfo: {
		title: "Matriz Legal"
	},
  	methods: {
  	}
};
</script>
