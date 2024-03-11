<template>
    <b-form-checkbox-group
        v-model="value"
        name="flavour-2"
        stacked
      >
        <b-form-checkbox v-for="(item, key) in options" :value="item.value" :key="key">{{item.text}} &nbsp;&nbsp;<b-btn v-if="descriptions[item.value]" variant="primary" class="btn-circle-micro" v-b-popover.hover.focus.right="descriptions[item.value]" title="Información"><span class="fas fa-info"></span></b-btn></b-form-checkbox>
      </b-form-checkbox-group>
    <!--<b-form-group class="class" :label="label">
        <b-check-group 
            :stacked="vertical"
            :name="name" 
            :state="state" 
            :options="options" 
            :disabled="disabled" 
            :checked="checked"
            @input="updateChecked($event)"/>
    </b-form-group>-->
</template>
<script>
export default {
    props:{
        name: { type: String, required: true },
        disabled: { type: Boolean, default: false },
        label: {type: String},
        options: {type: Array, required: true},
        descriptions: {type: [Array, Object], required: true},
        error: {type: String, default: null},
        vertical: { type: Boolean, default: false },
        checked: { type: [String, Number, Boolean, Array] },
    },
    computed: {
        state(){
            if(!this.error){
                return null;
            }
            else{
                return 'invalid';
            }
        },
    },
    methods:{
        updateChecked() {
            this.$emit('input', this.value);
        } 
    },
    data() {
        return {
            value: [],
            descriptions_options: []
        }
    },
    watch: {
        value() {
            this.updateChecked()
        }
    },
    mounted() {
        if (this.checked && this.checked.length > 0)
            this.value = this.checked
    }
}
</script>
