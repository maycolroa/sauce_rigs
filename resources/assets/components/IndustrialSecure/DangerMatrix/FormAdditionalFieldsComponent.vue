<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
            	<b-card border-variant="primary" title="Campos adicionales">
				    <template v-for="(field, index) in form.fields">
				      <div :key="field.key">
				          <b-form-row>
				              <div class="col-md-12">
				                  <div class="float-right">
				                      <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeField(index)"><span class="ion ion-md-close-circle"></span></b-btn>
				                  </div>
				              </div>
				              <vue-input class="col-md-12" v-model="field.name" label="Nombre" name="fields" type="text" placeholder="Nombre" :error="form.errorsFor(`fields.${index}.name`)"></vue-input>
				          </b-form-row>
				      </div>
				    </template>

				    <b-form-row style="padding-bottom: 20px;">
				      <div class="col-md-12">
				          <center><b-btn variant="primary" @click.prevent="addField()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Documento</b-btn></center>
				      </div>
				    </b-form-row>
            	</b-card>

				<br>
				<div class="row float-right pt-10 pr-10">
                    <template>
                        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{"Cancelar"}}</b-btn>&nbsp;&nbsp;
                        <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>

<script>

import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";

export default {
	components: {
		VueInput
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		fields: {
			default() {
				return {
					fields: []
				};
			}
		},
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.fields, this.method),
		};
	},
	watch: {
		fields() {
			this.loading = false;
			this.form = Form.makeFrom(this.fields, this.method);
		}
	},
	methods: {
		submit(e) {
		this.loading = true;
		this.form
			.submit(e.target.action)
			.then(response => {
				this.loading = false;
				this.$router.push({ name: "industrialsecure-dangermatrix" });
			})
			.catch(error => {
				this.loading = false;
			});
		},
		addField() {
	        this.form.fields .push({
	            key: new Date().getTime(),
	            name: ''
	        })
	    },
	    removeField(index)
	    {
	        if (this.form.fields [index].id != undefined)
        		this.form.delete.push(this.form.fields [index].id)

      		this.form.fields .splice(index, 1)
	    }
	}
};
</script>
