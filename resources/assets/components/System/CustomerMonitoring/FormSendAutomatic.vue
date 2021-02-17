<template>
  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <div class="col-md-12">
          <b-form-row>
            <vue-advanced-select class="col-md-6" v-model="form.users[index].user_id" :error="form.errorsFor(`users.${index}.user_id`)" name="user" label="Usuario" placeholder="Seleccione un usuario" :options="usersOptions"  :searchable="true">
            </vue-advanced-select>

            <vue-advanced-select :disabled="isLoading" v-model="inspectionsSelectedType2" :options="daysOptions" :allowEmpty="false" :searchable="true" name="inspectionsSelectedType2"></vue-advanced-select>
              
          </b-form-row>
        </div>
      </template>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueInput,
    VueAjaxAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    usersOptions: {
			type: Array,
			default: function() {
				return [];
			}
    },
    daysOptions: {
			type: Array,
			default: function() {
				return [];
			}
		},
    send: {
      default() {
        return {
          users: [],
          day: ''
        };
      }
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.send, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "system-customermonitoring-automatics-send" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
