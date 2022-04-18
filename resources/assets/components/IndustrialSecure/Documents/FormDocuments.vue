<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" class="mb-3 box-shadow-none">
					<b-form-row>
						<vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
					</b-form-row>

                    <b-form-row>
						<vue-file-simple v-if="isEdit || viewOnly" :help-text="`Para descargar el archivo actual, haga click <a href='/legalAspects/fileUpload/download/${this.$route.params.id}' target='blank'>aqui</a> `" :disabled="viewOnly" class="col-md-12" v-model="form.file" label="Archivo" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
						<vue-file-simple v-else :disabled="viewOnly" class="col-md-12" v-model="form.file" label="Archivo" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
                    </b-form-row>

					<b-form-row>
						<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.users_id" :selected-object="form.multiselect_user_id" name="users_id" label="Usuarios" placeholder="Seleccione los usuarios autorizados para ver o editar este documento" :url="userDataUrl" :error="form.errorsFor('users_id')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>    
						<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.roles_id" :error="form.errorsFor('roles_id')" :selected-object="form.multiselect_role" name="roles_id" label="Roles" placeholder="Seleccione los roles autorizados para ver o editar este documento" :url="rolesDataUrl" :multiple="true" :allowEmpty="true"></vue-ajax-advanced-select>  
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
					id:'',
					users_id: '',
					name: '',
					roles_id: '',
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
      		userDataUrl: '/selects/users',
			rolesDataUrl: "/selects/roles",
			disabledDates: {
				to: new Date()
			}
		};
	},
	methods: {
		submit(e) {
		this.loading = true;
		this.form
			.submit(e.target.action)
			.then(response => {
				this.loading = false;
				this.$router.push({ name: "industrialsecure-documentssecurity" });
			})
			.catch(error => {
				this.loading = false;
			});
		}
	}
};
</script>
