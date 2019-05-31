<template>
    <b-form-group class="class" :label="label">
        <b-check-group 
            :stacked="vertical"
            :name="name" 
            :state="state" 
            :options="options" 
            :disabled="disabled" 
            :checked="checked"
            @input="updateChecked($event)"/>
    </b-form-group>
</template>
<script>
export default {
    props:{
        name: { type: String, required: true },
        disabled: { type: Boolean, default: false },
        label: {type: String},
        options: {type: Array, required: true},
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
        updateChecked(value) {
            this.$emit('input', value);
        } 
    }
}
</script>
