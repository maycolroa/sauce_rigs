<template>
  	<div>
    	<h4 class="font-weight-bold mb-4">Maestro</h4>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['risksAspects_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-dangermatrix-tags-administrative-controls'}">
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Controles administrativos</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['sstRisks_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-dangermatrix-tags-danger-description'}">
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Descripción de peligros</span>
                                    </span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['systemApply_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-dangermatrix-tags-engineering-controls' }">
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Controles de ingenieria</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['entities_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-dangermatrix-tags-epp'}">
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Epp</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['risksAspects_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-dangermatrix-tags-participants'}">
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Participantes</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['interests_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-dangermatrix-tags-possible-consequences-danger'}">
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Posibles consecuencias del peligro</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['systemApplyCustom_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-dangermatrix-tags-substitutions' }">
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Sustituciones</span>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center" v-if="auth.can['interestsCustom_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-dangermatrix-tags-warning-signage'}">
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Señalizaciones, Advertencias</span>
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
	name: "danger-matrix-tags",
	metaInfo: {
		title: "Tags"
	},
  	methods: {
  	}
};
</script>
