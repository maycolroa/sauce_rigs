<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
            	<b-card border-variant="primary" title="Documentos a solicitar">
				    <template v-for="(document, index) in form.documents">
				      <div :key="document.key">
				          <b-form-row>
				              <div class="col-md-12">
				                  <div class="float-right">
				                      <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeDocument(index)"><span class="ion ion-md-close-circle"></span></b-btn>
				                  </div>
				              </div>
				              <vue-input class="col-md-12" v-model="document.name" label="Nombre" name="documents" type="text" placeholder="Nombre" :error="form.errorsFor(`documents.${index}.name`)"></vue-input>
				          </b-form-row>
				      </div>
				    </template>

				    <b-form-row style="padding-bottom: 20px;">
				      <div class="col-md-12">
				          <center><b-btn variant="primary" @click.prevent="addDocument()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Documento</b-btn></center>
				      </div>
				    </b-form-row>
            	</b-card>

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
		documents: {
			default() {
				return {
					documents: []
				};
			}
		},
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.documents, this.method),
		};
	},
	watch: {
		documents() {
			this.loading = false;
			this.form = Form.makeFrom(this.documents, this.method);
		}
	},
	methods: {
		submit(e) {
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
		},
		addDocument() {
	        this.form.documents.push({
	            key: new Date().getTime(),
	            name: ''
	        })
	    },
	    removeDocument(index)
	    {
	      if (this.form.documents[index].id != undefined)
	        this.form.delete.documents.push(this.form.documents[index].id)

	      this.form.documents.splice(index, 1)
	    }
	}
};
</script>
