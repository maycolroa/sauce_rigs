<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">		
		<b-card border-variant="primary" no-body class="mb-4">
			<b-card-body>
				<div class="rounded ui-bordered p-3 mb-3"  v-for="(item, index) in form.items" :key="item.id">
					<b-form-row>
						<p class="my-1">{{ (index) + 1 }} - {{ item.item_name }}</p>
						<span class="text-muted">{{ item.criterion_description}}</span>
						<div class="media align-items-center mt-3">
							<div class="text-muted small text-nowrap">	
									<vue-radio v-model="item.required" label="Archivo requerido" :name="`items${item.id}`" :options="options" :checked="item.required"></vue-radio>
							</div>
							
						</div>
					</b-form-row>
				</div>
			</b-card-body>
		</b-card>
		<br><br>
		<div class="row float-right pt-10 pr-10" style="padding-bottom: 40px;">
			<template>
				<b-btn variant="default" :to="cancelUrl" :disabled="loading">Atras</b-btn>&nbsp;&nbsp;
				<b-btn type="submit" :disabled="loading" variant="primary">Guardar</b-btn>
			</template>
		</div>
	</b-form>
</template>

<script>

import VueCheckbox from "@/components/Inputs/VueCheckbox.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';

export default {
	components: {
		VueCheckbox,
		VueRadio
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		items: {
			default() {
				return {
					items: []
				};
			}
		},
	},
	data() {
		return {
			loading: false,
			form: Form.makeFrom(this.items, this.method),
			options: {
				'SI': 'SI',
				'NO': 'NO'
			}
		};
	},
	methods: {
		submit(e) {
			console.log("enviar")
	      this.loading = true;
	      this.form
	        .submit(e.target.action)
	        .then(response => {
	          this.loading = false;
	          this.$router.push({ name: "legalaspects-contractor" });
	        })
	        .catch(error => {
	          this.loading = false;
	        });
	    }
	}
};
</script>
