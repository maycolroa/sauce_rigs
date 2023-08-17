<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input v-show="!isDeleted" :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>

    <b-card v-if="isEdit || isDeleted">
      <div>
        <template>
          <vue-table
            configName="industrialsecure-tags-administrative-control-search"
            :params="{ keyword: form.name}"
          ></vue-table>
        </template>
      </div>
    </b-card>

    <b-form-row v-if="isEdit && !isDeleted">
      <vue-radio :disabled="viewOnly" :checked="form.rewrite" class="col-md-12" v-model="form.rewrite" :options="siNo" name="rewrite" :error="form.errorsFor('rewrite')" label="¿Desea sobreescribir el valor en todas las matrices en las cuales se encuentra?">
                    </vue-radio>
    </b-form-row>

    <b-form-row v-if="isDeleted">
      <vue-radio :disabled="viewOnly" :checked="form.replace" class="col-md-12" v-model="form.replace" :options="siNo" name="replace" :error="form.errorsFor('replace')" label="¿Desea reemplazar el valor a eliminar en todas las matrices en las cuales se encuentra?">
                    </vue-radio>
      <vue-ajax-advanced-select v-if="form.replace == 'SI'" :disabled="viewOnly" class="col-md-12" v-model="form.replace_deleted" name="replace_deleted" :error="form.errorsFor('replace_deleted')" label="Tags para remmplazar" placeholder="Seleccione los controles administrativos" :url="tagsAdministrativeControlsDataUrl" :multiple="false" :allowEmpty="true" :taggable="false">
                    </vue-ajax-advanced-select>

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
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueRadio,
    VueAjaxAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    isDeleted: { type: Boolean, default: false },
    tag: {
      default() {
        return {
            name: '',
            rewrite: '',
            replace: '',
            replace_deleted: ''
        };
      }
    }
  },
  watch: {
    tag() {
      this.loading = false;
      this.form = Form.makeFrom(this.tag, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.tag, this.method),
      siNo: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
      tagsAdministrativeControlsDataUrl: '/selects/tagsAdministrativeControls',
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;

          this.$router.push({ name: "industrialsecure-dangermatrix-tags-administrative-controls" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
