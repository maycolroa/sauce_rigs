<template>
    <b-form-group :feedback="error">
      <div slot="label" :class="classBlock">
          <div v-if="label">{{label}}</div>
          <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
      </div>
      <b-input-group :class="state" :append="append" :prepend="prepend">
        <b-input 
            v-model="ini"
            :state="state" 
            placeholder="Inicio" 
            type="number"
            :name="name"
            :min="min"
            :max="max"
            :disabled="disabled"
            :autocomplete="autocomplete ? 'off' : ''"
            @blur.native="updateValue"
            />
        <b-input 
          value="A" 
          class="bg-secondary text-white text-center col-1" 
          :disabled="true"
          style="padding-left: 0px; padding-right: 0px;"
          />
        <b-input 
          v-model="end"
          :state="state" 
          placeholder="Fin" 
          type="number"
          :name="name"
          :min="min"
          :max="max"
          :disabled="disabled"
          :autocomplete="autocomplete ? 'off' : ''"
          @blur.native="updateValue"
          />
          
      </b-input-group>
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
    value: {type: [String,Number], default:''},
    //placeholder: {type:String},
    //type: { type: String, default: 'text' },
    min: { type: String, default: '' },
    max: { type: String, default: '' },
    append: {type: String, default: null},
    prepend: {type: String, default: null},
    name: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    autocomplete: { type: Boolean, default: false },
    textBlock: {type: String},
    actionBlock: {type: String},
    helpText: {type: String}
  },
  data () {
    return {
      ini: '',
      end: '',
    }
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
  watch: {
    value() {
      this.refreshData();
    }
  },
  methods: {
    updateValue() {
      if (this.ini || this.end)
        this.$emit('input', this.ini +'_'+ this.end);
      else
        this.$emit('input', '');
    },
    refreshData() {
      let data = this.value.split('_')

      if (data.length == 1)
      {
        this.ini = data[0]
        this.end = ''
      }
      else if (data.length == 2)
      {
        this.ini = data[0]
        this.end = data[1]
      }
      else
      {
        this.ini = ''
        this.end = ''
      }
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