<template>
    <b-form-group :feedback="error">
        <div slot="label" :class="classBlock">
            <div v-if="label">{{label}}</div>
            <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
        </div>
            <b-textarea
                :value="value" 
                :state="state" 
                :placeholder="placeholder" 
                :name="name"
                :disabled="disabled"
                @input="updateValue($event)"
                @blur.native="onBlur" 
                :rows="rows" />

           <b-form-text v-if="helpText">
            {{ helpText }}
           </b-form-text>
    </b-form-group>
</template>

<script>
export default {
  props: {
    error: {type: String, default: null},
    label: {type: String},
    value: {default:''},
    placeholder: {type:String},
    rows: { type: [Number, String], default: 3 },
    name: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    textBlock: {type: String},
    actionBlock: {type: String},
    helpText: {type: String}
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