<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" class="mb-3 box-shadow-none">
					<b-form-row>
						<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>

						<vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.type" :error="form.errorsFor('type')" :multiple="false" :options="typeOptions" :hide-selected="false" name="type" label="Tipo" placeholder="Seleccione el tipo">
						</vue-advanced-select>
					</b-form-row>

					<b-form-row>
        				<vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.observations" label="Observaciones" name="observations" placeholder="Observaciones" rows="3" :error="form.errorsFor('observations')"></vue-textarea>
					</b-form-row>

                    <b-form-row>
						<vue-file-simple :disabled="isEdit || viewOnly" class="col-md-12" v-model="form.file" label="Archivo" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
                    </b-form-row>

            	</b-card>
				<div class="row float-right pt-10 pr-10">
                    <template>
                        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
                        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>

<script>

import VueInput from "@/components/Inputs/VueInput.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/utils/Form.js";

export default {
	components: {
		VueInput,
		VueFileSimple,
		VueDatepicker,
		VueAjaxAdvancedSelect,
		VueAdvancedSelect,
		VueTextarea
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
		states: {
	      type: Array,
	      default: function() {
	        return [];
	      }
	    },
		document: {
			default() {
				return {
					name: '',
					type: '',
					observations: '',
					file: '',
				};
			}
		}
	},
	watch: {
		document() {
			this.loading = false;
			this.form = Form.makeFrom(this.document, this.method);
		}
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.document, this.method),
			typeOptions: [
				{name: 'Psicosocial', value: 'Psicosocial'},
				{name: 'Carga fisica', value: 'Carga fisica'},
				{name: 'Químico', value: 'Químico'}
			]
		};
	},
	methods: {
		submit(e) {
		this.loading = true;
		this.form
			.submit(e.target.action)
			.then(response => {
				this.loading = false;
				this.$router.push({ name: "industrialsecure-complementary-methodology" });
			})
			.catch(error => {
				this.loading = false;
			});
		}
	}
};
</script>
