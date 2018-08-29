<template>
    <b-form-group class="class" :label="label">
        <b-check-group 
            :name="name" 
            :state="state" 
            :options="options" 
            :disabled="disabled" 
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
