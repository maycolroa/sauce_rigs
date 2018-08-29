<template>
    <b-form-group :feedback="error">
        <div slot="label" :class="classBlock">
            <div v-if="label">{{label}}</div>
            <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
        </div>
        <b-input
            :value="value" 
            :state="state" 
            :placeholder="placeholder" 
            :type="type"
            :step="type == 'number' ? step : ''"
            :name="name"
            :disabled="disabled"
            :autocomplete="autocomplete ? 'off' : ''"
            @input="updateValue($event)"
            />
    </b-form-group>
</template>

<script>
export default {
  props: {
    error: {type: String, default: null},
    label: {type: String},
    value: {type: String, default:''},
    placeholder: {type:String},
    type: { type: String, default: 'text' },
    step: { type: String, default: '' },
    name: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    autocomplete: { type: Boolean, default: false },
    textBlock: {type: String},
    actionBlock: {type: String},
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
    } 
  }

}
</script>