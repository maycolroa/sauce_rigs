<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-textarea>
    </b-form-row>
    <b-form-row>
      <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-12" v-model="form.category" name="category" :error="form.errorsFor('category')" label="Categoría" placeholder="Seleccione una opción" :url="tagsCategoriesDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
      </vue-ajax-advanced-select-tag-unic>
      <!--<vue-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.category" :error="form.errorsFor('category')" :multiple="false" :options="categories" :hide-selected="false" name="category" label="Categoría" placeholder="Seleccione una opción">
          </vue-advanced-select>-->
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" v-if="!modal" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueAdvancedSelect,
    VueTextarea,
    VueAjaxAdvancedSelectTagUnic
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    modal: { type: Boolean, default: false },
    categories: {
      type: Array,
      default: function() {
        return [];
      }
    },
    risk: {
      default() {
        return {
            name: '',
            category: ''
        };
      }
    }
  },
  watch: {
    risk() {
      this.loading = false;
      this.form = Form.makeFrom(this.risk, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.risk, this.method),
      tagsCategoriesDataUrl: '/selects/tagsRmCategories',
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;

          if (this.modal)
          {  
            Object.assign(this.$data, this.$options.data.apply(this))
            this.$parent.$emit("closeModal")
          }
          else
            this.$router.push({ name: "industrialsecure-risks" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
