<template>
    <b-form-group>
        <div slot="label" :class="classBlock">
            <div v-if="label">{{label}}</div>
            <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
        </div>
        <multiselect v-model="selectValue"
                :state="state" 
                :options="options"
                :searchable="searchable"
                :show-labels="false"
                :placeholder="placeholder"
                :disabled="disabled"
                label="name"
                :hide-selected="hideSelected"
                deselect-label="Puede quitar este valor"
                :class="state"
                track-by="name"
                @input="updateValue"
                :allow-empty="true"
                :multiple="multiple"
                :close-on-select="!multiple"
                :limit="limit"
                :limit-text="limitText"
                :group-select="groupSelect"
                group-label="parent"
                group-values="children">
            <span slot="noResult">No se encontraron elementos</span>
        </multiselect>
        
        <b-form-invalid-feedback v-if="error" :force-show="true">
            {{error}}
        </b-form-invalid-feedback>
    </b-form-group>
</template>
<style src="@/vendor/libs/vue-multiselect/vue-multiselect.scss" lang="scss"></style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<script>
import Multiselect from "vue-multiselect";
export default {
  props: {
    error: { type: String },
    name: { type: String, required: true },
    value: [String, Number, Object, Array],
    options: { type: Array, required: true },
    label: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
    searchable: { type: Boolean, default: false },
    hideSelected: { type: Boolean, default: true },
    placeholder: { type: String, default: "" },
    multiple: { type: Boolean, default: false },
    textBlock: { type: String },
    actionBlock: { type: String },
    limit: { type: Number, default: 1000 },
    groupSelect: {type: Boolean, default: false},
    returnObject: {type: Boolean, default: false},
    selectedObject: { type: Object },
  },
  components: {
    Multiselect
  },
  data() {
    return {
      selectValue: ""
    };
  },
  watch: {
    options() {
      this.setMultiselectValue();
    },
    selectedObject(){
      this.selectValue = this.selectedObject;
    },
  },
  mounted() {
      if (this.selectedObject) {
        this.selectValue = this.selectedObject;
      }

      this.updateValue();
    },
  methods: {
    limitText(count) {
      return `y ${count} mas`;
    },
    updateValue() {
      let value = (this.multiple || this.returnObject) ? this.selectValue : (this.selectValue ? this.selectValue.value : '');

      this.$emit("input", value);
    },
    setMultiselectValue() {
      if (this.value) {
        if (this.multiple) {
          if (typeof this.value == "object") {
            this.selectValue = this.value;
          } else {
            this.selectValue = this.value.split(",").map(v => {
              
              return {'name': v, 'value': v}
            });
          }
        } else {
           this.selectValue = this.value
              ? _.find(this.options, { value: this.value })
              : "";
        }

        this.updateValue()
      }
    }
  },
  computed: {
    state() {
      if (!this.error) {
        return null;
      } else {
        return "is-invalid";
      }
    },
    classBlock() {
      return this.textBlock
        ? "d-flex justify-content-between align-items-end"
        : "";
    }
  }
};
</script>