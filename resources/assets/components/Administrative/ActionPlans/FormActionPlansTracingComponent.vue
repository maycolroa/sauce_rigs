<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
            	<b-card border-variant="primary" title="Seguimientos de la actividad">
				    <template v-for="(tracing, index) in form.tracings">
				      <div :key="tracing.key">
				          <b-form-row>
				              <div class="col-md-12">
				                  <div v-if="auth.can['action_plan_activities_tracing_admin']" class="float-right">
				                      <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeDocument(index)"><span class="ion ion-md-close-circle"></span></b-btn>
				                  </div>
				              </div>
				              <vue-textarea v-if="auth.can['action_plan_activities_tracing_admin']" class="col-md-12" v-model="tracing.tracing" label="Seguimiento" name="tracings" type="text" placeholder="Seguimiento" :error="form.errorsFor(`tracings.${index}.tracing`)"></vue-textarea>
                              <vue-textarea v-if="!auth.can['action_plan_activities_tracing_admin']" :disabled="tracing.id" class="col-md-12" v-model="tracing.tracing" label="Seguimiento" name="tracings" type="text" placeholder="Seguimiento" :error="form.errorsFor(`tracings.${index}.tracing`)"></vue-textarea>
				          </b-form-row>
                        <template v-if="form.isEdit">
                          <b-form-row>
				              <vue-input :disabled="true" class="col-md-6" v-model="tracing.user" label="Usuario Creador" name="user" type="text" placeholder="Usuario Creador" :error="form.errorsFor(`tracings.${index}.user`)"></vue-input>
                              <vue-input :disabled="true" class="col-md-3" v-model="tracing.date" label="Fecha de Creación" name="date" type="text" placeholder="Fecha de Creación" :error="form.errorsFor(`tracings.${index}.date`)"></vue-input>
				          </b-form-row>
                        </template>
				      </div>
				    </template>

				    <b-form-row style="padding-bottom: 20px;">
				      <div class="col-md-12">
				          <center><b-btn variant="primary" @click.prevent="addDocument()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Seguimiento</b-btn></center>
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
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueInput from "@/components/Inputs/VueInput.vue";

export default {
    components: {
        VueTextarea,
        VueInput
    },
    props: {
        url: { type: String },
        method: { type: String },
        cancelUrl: { type: [String, Object], required: true },
        tracings: {
			default() {
				return {
					tracings: [],
                    activity_id: '',
                    isEdit: false,
                    delete: []
				};
			}
		}
    },
    watch: {
        tracings() {
            this.loading = false;
            this.form = Form.makeFrom(this.tracings, this.method);
        }
    },
    data() {
        return {
            loading: this.isEdit,
            form: Form.makeFrom(this.tracings, this.method)
        };
    },
    methods: {
        submit(e) {
            this.loading = true;
            this.form
                .submit(e.target.action)
                .then(response => {
                    this.loading = false;
                    this.$router.push({ name: "administrative-actionplans" });
                })
                .catch(error => {
                    this.loading = false;
                });
        },
        addDocument() {
	        this.form.tracings.push({
	            key: new Date().getTime(),
	            tracing: ''
	        })
	    },
	    removeDocument(index)
	    {
	        if (this.form.tracings[index].id != undefined)
        		this.form.delete.push(this.form.tracings[index].id)

      		this.form.tracings.splice(index, 1)
	    }
    }
};
</script>