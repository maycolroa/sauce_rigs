<template>
  	<div>
    	<header-module
			title="ELEMENTOS DE PROTECCIÓN PERSONAL"
			url="industrialsecure-epp"
		/>
    	<div class="col-md">
			<b-card no-body>
				<b-card-header class="with-elements">
					<div class="card-title-elements">
						<div class="row no-gutters row-bordered row-border-light h-100">
							
							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['transaction_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-transactions'}">
									<i class="ion ion-md-person-add display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Entregas</span>
										<br>
										<small class="text-muted">Administrar Entregas</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['transaction_r']">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-transactions-returns'}">
									<i class="fas fa-user display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Devoluciones</span>
										<br>
										<small class="text-muted">Administrar Devoluciones</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['transaction_r']  && auth.inventaryEpp == 'SI'">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-transactions-income'}">
									<i class="ion ion-md-add-circle display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Ingresos</span>
										<br>
										<small class="text-muted">Administrar Ingresos</small>
									</span>
								</router-link>
							</div>

							<div class="d-flex col-sm-6 col-md-6 col-lg-6 align-items-center" v-if="auth.can['transaction_r']  && auth.inventaryEpp == 'SI'">
								<router-link tag="a" href="javascript:void(0)" class="card-body media align-items-center text-dark" :to="{name:'industrialsecure-epps-transactions-exit'}">
									<i class="ion ion-md-remove-circle display-4 d-block text-primary"></i>
									<span class="media-body d-block ml-3">
										<span class="text-big font-weight-bolder">Salidas</span>
										<br>
										<small class="text-muted">Administrar Salidas</small>
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
	name: "industrialsecure-epps-transactions",
	metaInfo: {
		title: "Elementos de protección personal"
	},
  	methods: {
  	}
};
</script>
