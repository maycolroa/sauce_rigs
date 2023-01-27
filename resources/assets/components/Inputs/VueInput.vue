<template>
    <b-form-group :feedback="error">
        <div slot="label" :class="classBlock">
            <div v-if="label">{{label}}</div>
            <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
        </div>
        <b-input-group :class="state" :append="append" :prepend="prepend">
        <b-input 
            :value="value" 
            :state="state" 
            :placeholder="placeholder" 
            :type="type"
            :name="name"
            :min="min"
            :max="max"
            :step="step"
            :disabled="disabled"
            :autocomplete="autocomplete ? 'off' : ''"
            @input="updateValue($event)"
            @blur.native="onBlur" 
            />
            
        </b-input-group>
        <b-form-text v-if="helpText">
            {{ helpText }}
        </b-form-text>
        <!-- <b-form-feedback class="d-block" v-if="state == 'invalid'">
            {{ error }}
        </b-form-feedback> -->
    </b-form-group>
</template>

<script>
export default {
  props: {
    error: {type: String, default: null},
    label: {type: String},
    value: {type: [String,Number], default:''},
    placeholder: {type:String},
    type: { type: String, default: 'text' },
    min: { type: String, default: '' },
    max: { type: String, default: '' },
    append: {type: String, default: null},
    prepend: {type: String, default: null},
    name: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    autocomplete: { type: Boolean, default: false },
    textBlock: {type: String},
    actionBlock: {type: String},
    helpText: {type: String},
    step: { type: Number, default: '0.01' },
  },
  computed:{
      state(){
          if(!this.error){
              return null;
          }
          else{
              return 'invalid';
          }
      },
      classBlock(){
          return this.textBlock ? 'd-flex justify-content-between align-items-end' : '';
      },
  },
  methods: {
    updateValue(value) {
      this.$emit('input', value);
    },
    onBlur() {
      this.$emit('onBlur');
    } 
  }

}
</script>
<style lang="scss">
.input-group.invalid {
  ~ .invalid-feedback {
    display: block;
  }
}
</style>