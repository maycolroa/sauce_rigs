<template>
    <b-form-group :class="customClass" :label="label">
        <div v-if="!stacked">
            <center>
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
            </center>
        </div>
        <div v-else>
            <b-radio-group
                :name="name" 
                :state="state" 
                :options="options" 
                :disabled="disabled" 
                @input="updateChecked($event)"
                :checked="checked"
                stacked/>
            <b-form-invalid-feedback v-if="error" :force-show="true">
                {{error}}
            </b-form-invalid-feedback>
        </div>
    </b-form-group>
</template>
<script>
export default {
    props:{
        name: { type: String, required: true },
        disabled: { type: Boolean, default: false },
        checked: { type: [String, Number, Boolean], default: '' },
        label: {type: String},
        options: {type: [Array, Object], required: true},
        error: {type: String, default: null},
        stacked: { type: Boolean, default: false },
        customClass: { type: String, default: 'text-center'}
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
