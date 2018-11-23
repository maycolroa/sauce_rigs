<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <div class="row">
      <div class="col-md-6"> 
        <b-form-row>
          <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
        </b-form-row>

        <vue-textarea :disabled="viewOnly" v-model="form.description" label="Descripción" :error="form.errorsFor('description')" name="description" placeholder="Descripción"></vue-textarea>

        <b-form-row>
          <vue-advanced-select-group :disabled="viewOnly" v-model="module_selected" :value="module_selected" class="col-md-12" :options="modules" :limit="1000" :searchable="true" name="modules_multiselect" label="Aplicación \ Módulo" placeholder="Seleccione un modulo">
          </vue-advanced-select-group>
        </b-form-row>

        <b-form-row>
          <vue-advanced-select :disabled="viewOnly" v-model="permission_selected" :value="permission_selected" class="col-md-12" :options="permissions_module" :limit="1000" :searchable="true" name="permissions_multiselect" label="Permisos" placeholder="Seleccione los permisos">
            </vue-advanced-select>
        </b-form-row>
      </div>

      <div class="col-md-6"> 
        <div class="d-flex justify-content-between align-items-end" style="padding-bottom: 10px;">
          Permisos Seleccionados
          <b-form-feedback class="d-block" v-if="form.errorsFor('permissions_asignates')">
              {{ form.errorsFor('permissions_asignates') }}
          </b-form-feedback>
        </div>
        <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 450px; padding-right: 10px;">
          <template  v-for="(item, index) in form.permissions_asignates">
            <b-card no-body class="mb-2 border-secondary" v-if="item != undefined && Object.keys(item.permissions).length > 0" :key="index">
              <b-card-header class="bg-secondary">
                <a class="d-flex justify-content-between text-white" href="javascript:void(0)" v-b-toggle="'accordion-' + index"> {{ item.name }} <div class="collapse-icon"></div> </a>
              </b-card-header>
              <b-collapse :id="`accordion-${index}`" visible accordion="accordion">
                <b-card-body>
                  <b-list-group>
                    <b-list-group-item v-for="(itemPermission, indexPermission) in item.permissions" 
                        class="d-flex justify-content-between align-items-center" :key="indexPermission">
                      <strong>{{ itemPermission.name }}</strong> <span class="badge badge-secondary" v-if="!viewOnly" style="cursor: pointer;" @click="removePermission(index, indexPermission, itemPermission)"><i class="ion ion-md-close"></i></span>
                    </b-list-group-item>
                  </b-list-group>
                </b-card-body>
              </b-collapse>
            </b-card>
          </template>
        </perfect-scrollbar>
      </div>
    </div>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar'
import VueInput from "@/components/Inputs/VueInput.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAdvancedSelectGroup from "@/components/Inputs/VueAdvancedSelectGroup.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueAdvancedSelect,
    VueAdvancedSelectGroup,
    PerfectScrollbar,
    VueTextarea
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    modules: {
      type: Array,
      default: function() {
        return [];
      }
    },
    permissions: {
      type: [Object, Array],
      default: function() {
        return [];
      }
    },
    role: {
      default() {
        return {
            name: '',
            description: '',
            permissions_asignates: []
        };
      }
    }
  },
  watch: {
    role() {
      this.loading = false;
      this.form = Form.makeFrom(this.role, this.method);
    },
    module_selected() {
      this.permissions_module = (this.permissions[this.module_selected.value] != undefined) ? this.permissions[this.module_selected.value] : []
    },
    permission_selected()
    {
      if (this.permission_selected != "")
        this.removeModuleSelected()
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.role, this.method),
      module_selected: '',
      permission_selected: '',
      permissions_module: []
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "administrative-roles" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    removeModuleSelected()
    {
      for(var i in this.permissions_module)
      {
        if(this.permissions_module[i].value == this.permission_selected)
        {
          if (this.role.permissions_asignates[this.module_selected.value] == undefined)
          {
            let arr = {
              'id': this.module_selected.value,
              'name': this.module_selected.name,
              'permissions': []
            }

            this.$set(this.role.permissions_asignates, this.module_selected.value, arr)
          }

          this.role.permissions_asignates[this.module_selected.value].permissions.push(this.permissions_module[i])
          this.permissions_module.splice(i,1);
          break;
        }
      }
    },
    removePermission(module_id, index_permission, object_permission)
    {
      this.permission_selected = ''
      this.role.permissions_asignates[module_id].permissions.splice(index_permission, 1)
      this.permissions[module_id].push(object_permission)
    }
  }
};
</script>
