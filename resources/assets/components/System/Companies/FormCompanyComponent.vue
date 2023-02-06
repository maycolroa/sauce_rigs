<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-radio :disabled="viewOnly" :checked="form.group_company" class="col-md-12" v-model="form.group_company" :options="siNo" name="group_company" :error="form.errorsFor('group_company')" label="¿Pertenece a algun grupo de compañias?">
        </vue-radio>
    </b-form-row>

    <b-form-row v-if="form.group_company == 'SI'">
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.company_group_id" :error="form.errorsFor('company_group_id')" :selected-object="form.multiselect_company_group" name="company_group_id" label="Grupo de compañias" placeholder="Seleccione el grupo" :url="companiesGroupDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row v-if="isEdit">
      <div class="col-md-12">
        <div class="float-right" style="padding-top: 10px;">
              <b-btn variant="primary" @click.prevent="addUser()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Usuario</b-btn>
          </div>
      </div>
    </b-form-row>

    <b-form-row style="padding-top: 15px;" v-if="isEdit">
      <template v-for="(user, index) in form.users">
        <div class="col-md-12" :key="user.key">
          <b-form-row>
            <div class="col-md-12">
              <div class="float-right">
                <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar Usuario" @click.prevent="removeUser(index)"><span class="ion ion-md-close-circle"></span></b-btn>
              </div>
            </div>
          </b-form-row>
          <b-form-row>
            <vue-advanced-select class="col-md-6" v-model="form.users[index].user_id" :error="form.errorsFor(`users.${index}.user_id`)" name="user" label="Usuario" placeholder="Seleccione un usuario" :options="usersOptions"  :searchable="true">
              </vue-advanced-select>
              <vue-advanced-select class="col-md-6" v-model="form.users[index].role_id" :error="form.errorsFor(`users.${index}.role_id`)" name="role" label="Rol" placeholder="Seleccione un rol" :options="rolesOptions" :multiple="true"  :searchable="true">
              </vue-advanced-select>
          </b-form-row>
        </div>
      </template>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueRadio,
    VueInput
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    usersOptions: {
			type: Array,
			default: function() {
				return [];
			}
    },
    rolesOptions: {
			type: Array,
			default: function() {
				return [];
			}
		},
    company: {
      default() {
        return {
          name: '',
          users: [],
          group_company: '',
          company_group_id: ''
        };
      }
    }
  },
  watch: {
    company() {
      this.loading = false;
      this.form = Form.makeFrom(this.company, this.method);
    }
  },
  /*computed: {
    usersComputed() {
      let data = [];

      if (this.usersOptions.length > 0)
      {
        let ids = this.form.users.map((f) => {
          return f.user_id;
        });

        data = this.usersOptions.filter((f) => {
          return !ids.includes(f.value);
        });
      }

      return data;
    }
  },*/
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.company, this.method),
      companiesGroupDataUrl: '/selects/companiesGroup',
      siNo: [
          {text: 'SI', value: 'SI'},
          {text: 'NO', value: 'NO'}
        ],
    };
  },
  methods: {
    addUser() {
      this.form.users.push({
        key: new Date().getTime() + Math.round(Math.random() * 10000),
        user_id: '',
        role_id: []
      })
    },
    removeUser(index)
    {
      if (this.form.users[index].user_id != undefined)
        this.form.delete.push(this.form.users[index].user_id)

      this.form.users.splice(index, 1)
    },
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "system-companies" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
