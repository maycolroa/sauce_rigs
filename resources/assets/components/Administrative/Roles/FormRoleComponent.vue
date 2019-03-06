<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <div class="row">
      <div class="col-md-6"> 
        <b-form-row>
          <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
        </b-form-row>

        <vue-textarea :disabled="viewOnly" v-model="form.description" label="Descripción" :error="form.errorsFor('description')" name="description" placeholder="Descripción"></vue-textarea>
 
        <b-form-row v-if="auth.can['roles_manage_defined']">          
          <vue-checkbox-simple :disabled="isEdit || viewOnly" class="col-md-3" v-model="form.type_role" label="¿Definido?" :checked="form.type_role" name="type_role" checked-value="Definido" unchecked-value="No Definido"></vue-checkbox-simple>

          <vue-advanced-select-group v-show="form.type_role == 'Definido'" :disabled="viewOnly" v-model="form.module_id" class="col-md-9" :options="allModules" :limit="1000" :searchable="true" name="module_id" label="Aplicación \ Módulo al que se asignara el Rol" placeholder="Seleccione un modulo" :error="form.errorsFor('module_id')" :selected-object="form.multiselect_module">
          </vue-advanced-select-group>
        </b-form-row>
        
        <hr class="border-light container-m--x mt-0 mb-4">
        
        <b-form-row>
          <vue-advanced-select-group :disabled="viewOnly" v-model="module_selected" class="col-md-12" :options="modules" :limit="1000" :searchable="true" name="modules_multiselect" label="Aplicación \ Módulo" placeholder="Seleccione un modulo" :return-object="true">
          </vue-advanced-select-group>
        </b-form-row>

        <b-form-row>
          <vue-advanced-select :disabled="viewOnly" v-model="permission_selected" :value="permission_selected" class="col-md-12" :options="permissions_module" :close-on-select="false" :limit="1000" :searchable="true" name="permissions_multiselect" label="Permisos" placeholder="Seleccione los permisos">
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
                <a class="d-flex justify-content-between text-white" href="javascript:void(0)" v-b-toggle="'accordion' + index+'-1'"> {{ item.name }} <div class="collapse-icon"></div> </a>
              </b-card-header>
              <b-collapse :id="`accordion${index}-1`" visible :accordion="`accordion${index}`">
                <b-card-body>
                  <b-list-group>
                    <b-list-group-item v-for="(itemPermission, indexPermission) in item.permissions" 
                        class="d-flex justify-content-between align-items-center" :key="indexPermission">
                      <strong>{{ itemPermission.name }}</strong>
                      <span class="badge badge-secondary" v-if="!viewOnly && auth.can[itemPermission.value]" style="cursor: pointer;" @click="removePermission(index, indexPermission, itemPermission)"><i class="ion ion-md-close"></i></span>
                      <span class="badge badge-secondary" v-else-if="!viewOnly && !auth.can[itemPermission.value]" v-b-popover.hover.focus.left="'No puede remover este permiso'" ><i class="ion ion-ios-lock"></i></span>
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
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueAdvancedSelect,
    VueAdvancedSelectGroup,
    PerfectScrollbar,
    VueTextarea,
    VueCheckboxSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    allModules: {
      type: Array,
      default: function() {
        return [];
      }
    },
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
    modulesRemoved: {
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
            type_role: '',
            module_id: '',
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

          /***** */
          if (this.permissions_module.length == 0)
          {
            for(var indexApp in this.modules)
            {
              for(var indexChild in this.modules[indexApp].children)
              {
                if (this.modules[indexApp].children[indexChild].value == this.module_selected.value)
                {
                  this.modules[indexApp].children.splice(indexChild,1)
                  this.$set(this.modulesRemoved, this.module_selected.value, indexApp)
                }
              }
            }
          }

          break;
        }
      }
    },
    removePermission(module_id, index_permission, object_permission)
    {
      this.permission_selected = ''
      this.role.permissions_asignates[module_id].permissions.splice(index_permission, 1)

      if (this.permissions[module_id].length == 0)
      {
        let indexApp = this.modulesRemoved[module_id]
        this.modules[indexApp].children.push({"name": this.role.permissions_asignates[module_id].name, "value": module_id})
        this.modulesRemoved.splice(module_id,1)
      }

      this.permissions[module_id].push(object_permission)
    }
  }
};
</script>
