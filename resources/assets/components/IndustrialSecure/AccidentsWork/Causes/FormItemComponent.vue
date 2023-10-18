<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.item_name" label="Nombre" type="text" name="item_name" :error="form.errorsFor('item_name')" placeholder="Nombre"></vue-input>
    </b-form-row>
    <b-form-row v-if="isEdit && form.category_default">
       <vue-ajax-advanced-select :disabled="true" class="col-md-6" v-model="form.category_id" :error="form.errorsFor('category_id')" :selected-object="form.multiselect_category" name="category_id" label="Categoria" placeholder="Seleccione una opción" :url="sectionsDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row v-else>
       <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.category_id" :error="form.errorsFor('category_id')" :selected-object="form.multiselect_category" name="category_id" label="Categoria" placeholder="Seleccione una opción" :url="sectionsDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAjaxAdvancedSelect,
    VueInput
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    item: {
      default() {
        return {
            item_name: '',
            category_id: '',
            category_default_id: ''
        };
      }
    }
  },
  watch: {
    item() {
      this.loading = false;
      this.form = Form.makeFrom(this.item, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.item, this.method),
      sectionsDataUrl: '/selects/awCategories2'
    };
  },
  methods: {
    submit(e) {
      console.log(this.url)
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-accidentswork-causes-items" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
