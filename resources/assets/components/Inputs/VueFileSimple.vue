<template>
    <b-form-group :feedback="error">
        <div slot="label" :class="classBlock">
            <div v-if="label">{{label}}</div>
            <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
        </div>
        <b-input-group :class="state" :append="append" :prepend="prepend">
        <b-file 
            :value="value" 
            :state="state" 
            :placeholder="placeholder" 
            :accept="accept"
            :name="name"
            :disabled="disabled"
            @input="updateValue($event)"
            :key="key"
            />
            
        </b-input-group>
        <b-form-text v-if="helpText" v-html="helpText">
            {{ helpText }}
        </b-form-text>
        <!-- <b-form-feedback class="d-block" v-if="state == 'invalid'">
            {{ error }}
        </b-form-feedback> -->
    </b-form-group>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  props: {
    error: {type: String, default: null},
    label: {type: String},
    value: {type: [Object, File, String], default:''},
    placeholder: {type:String},
    accept	: { type: String, default: 'text' },
    append: {type: String, default: null},
    prepend: {type: String, default: null},
    name: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    textBlock: {type: String},
    actionBlock: {type: String},
    helpText: {type: String},
    maxFileSize: { type: Number, default: 20000000 },
  },
  watch:{

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
  data(){
    return {
      key: true,
    }
  },
  methods: {
    updateValue(value) 
    {
      if (this.accept != 'text')
      {
        if (!value.name.endsWith(this.accept))
          return this.emitError(`Tipo de archivo inválido: La extensión del archivo debe ser ${this.accept}`)
      }
      
      if (value.size > this.maxFileSize) 
      {
        return this.emitError(`Archivo muy pesado: El tamaño del archivo no puede superar ${this.maxFileSize/1000000}MB.`)
      }

      this.$emit('input', value);
    },
    emitError(message)
    {
      Alerts.error('Error', message);
      this.key = !this.key
      this.$emit('input', '');
      return;
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