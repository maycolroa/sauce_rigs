<template>
    <b-form-group>
        <div slot="label" :class="classBlock">
            <b-row align-v="end">
              <b-col>
                <div v-if="label">{{label}}</div>
                <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
              </b-col>
              <b-col v-if="filterTypeSearch" align-self="end">
                <div class="float-right radioSearch" style="padding-bottom: 0px; padding-right: 0px;padding-left: 0px;">
                  <vue-radio :checked="filterTypeSearchValue" v-model="filterTypeSearchValueResult" :options="[{'text':'=', 'value':'IN'}, {'text':'≠', 'value':'NOT IN'}]"  label="" :name="`${name}_filterTypeSearch`" @input="updateFilterTypeSearch">
                  </vue-radio>
                </div>
              </b-col>
              <b-col v-if="btnLabelPopover && Object.keys(btnLabelPopover).length > 0">
                <div class="float-right" style="padding-right: 10px;">
                    <b-btn v-b-popover.hover.focus.left="btnLabelPopover.content" :title="btnLabelPopover.title" variant="primary" class="btn-circle-micro"><span :class="btnLabelPopover.icon"></span></b-btn>
                </div>
              </b-col>
            </b-row>
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
                @input="updateValue($event, true)"
                :allow-empty="true"
                :multiple="multiple"
                :close-on-select="closeOnSelectState"
                :limit="limit"
                :limit-text="limitText"
                tag-placeholder="Añadir esto como nueva etiqueta"
                :taggable="taggable"
                @tag="addTag">
            <span slot="noResult">No se encontraron elementos</span>
        </multiselect>
        <b-form-invalid-feedback v-if="error" :force-show="true">
            {{error}}
        </b-form-invalid-feedback>
    </b-form-group>
</template>
<style src="@/vendor/libs/vue-multiselect/vue-multiselect.scss" lang="scss"></style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>
.radioSearch .form-group {
  margin-bottom: 0rem;
}
</style>

<script>
import Multiselect from "vue-multiselect";
import VueRadio from "@/components/Inputs/VueRadio.vue";
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
    btnLabelPopover: { type: Object },
    limit: { type: Number, default: 5 },
    closeOnSelect: {type: Boolean, default: true},
    taggable: {type: Boolean, default: false},
    filterTypeSearch: { type: Boolean, default: false },
    filterTypeSearchValue: { type: String, default: "IN" },
  },
  components: {
    Multiselect,
    VueRadio
  },
  data() {
    return {
      selectValue: [],
      filterTypeSearchValueResult: 'IN'
    };
  },
  watch: {
    options() {
      this.setMultiselectValue();
    }
  },
  mounted() {
      setTimeout(() => {
          this.setMultiselectValue();
      }, 3000)
  },
  methods: {
    limitText(count) {
      return `y ${count} mas`;
    },
    updateValue(event, onChange = false) {
      let value = this.multiple ? this.selectValue : (this.selectValue ? this.selectValue.value : '');

      this.$emit("input", value);

      if (!this.multiple)
      {
        this.$emit("selectedName", this.selectValue ? this.selectValue.name : '');
      }

      if (onChange)
        this.$emit("change");
    },
    setMultiselectValue() {
      if (this.value && this.options.length > 0) {
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
      else if (this.value && this.options.length == 0 && this.taggable) {
          if (this.multiple) {
              if (typeof this.value == "object") {
                  this.selectValue = this.value;
              } else {
                  this.selectValue = this.value.split(",").map(v => {
                  
                  return {'name': v, 'value': v}
                  });
              }
          }

          this.updateValue()
      }
    },
    addTag (newTag) {
      this.selectValue.push({
        name: newTag,
        value: newTag
      })
      
      this.updateValue()
    },
    updateFilterTypeSearch(value)
    {
        this.$emit('updateFilterTypeSearch', value);
    },
    refreshData()
    {
      this.setMultiselectValue()
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
    },
    closeOnSelectState()
    {
      return this.closeOnSelect ? !this.multiple : this.closeOnSelect
    }
  }
};
</script>