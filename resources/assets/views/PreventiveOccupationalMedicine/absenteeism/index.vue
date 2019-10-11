<template>
  	<div>
		<header-module
			title="AUSENTISMO"
			url="preventiveoccupationalmedicine"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['absen_reports_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'absenteeism-reports'}">
									<i class="lnr lnr-chart-bars display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Informes</span>
										<br>
										<small class="text-muted">Informes de mi compañia</small>
									</span>
								</router-link>
							</div>
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['absen_uploadFiles_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'absenteeism-upload-files'}">
									<i class="lnr lnr-upload display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Subida de archivos</span>
										<br>
										<small class="text-muted">Subida de archivos</small>
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
	name: "absenteeism",
	metaInfo: {
		title: "Ausentismo"
	},
  	methods: {
  	}
};
</script>
