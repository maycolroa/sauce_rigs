<template>
    <b-form-group class="class" :label="label">
        <b-card no-body class="demo-vertical-spacing-sm mt-2">
            <b-radio-group
                :name="name" 
                :state="state" 
                :options="options" 
                :disabled="disabled" 
                @input="updateChecked($event)"
                :checked="checked"/>
            <b-form-invalid-feedback v-if="error" :force-show="true">
                {{error}}
            </b-form-invalid-feedback>
        </b-card>
    </b-form-group>
</template>
<script>
export default {
    props:{
        name: { type: String, required: true },
        disabled: { type: Boolean, default: false },
        checked: { type: [String, Number], default: '' },
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
    data: () => ({
    singleModel: 'Rutinario',
    multipleModel: ['Rutinario'],
  }),
    methods:{
        updateChecked(value) {
            this.$emit('input', value);
        } 
    }
}
</script>
