<template>
  	<div>
    	<header-module
			title="AUDIOMETRIAS"
			url="preventiveoccupationalmedicine"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['biologicalMonitoring_audiometry_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'biologicalmonitoring-audiometry'}">
									<i class="ion ion-md-megaphone display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Audiometrías</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['biologicalMonitoring_musculoskeletalAnalysis_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'biologicalmonitoring-musculoskeletalanalysis'}"  >
									<i class="lnr lnr-magnifier display-4 d-block text-primary" ></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Análisis osteomuscular</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['biologicalMonitoring_respiratoryAnalysis_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'biologicalmonitoring-respiratoryanalysis'}"  >
									<i class="lnr lnr-magnifier display-4 d-block text-primary" ></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Análisis respiratorio</span>
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
		title: "Audiometrias"
	},
  	methods: {
  	}
};
</script>
