<template>
    <b-form-group class="class">
        <b-form-checkbox
            :name="name" 
            :disabled="disabled"
            :value="checkedValue"
            :unchecked-value="uncheckedValue"
            @input="updateChecked($event)"
            :checked="checked"
        > {{ label }} </b-form-checkbox>
    </b-form-group>
</template>
<script>
export default {
    props:{
        name: { type: String, required: true },
        disabled: { type: Boolean, default: false },
        label: {type: String, default: ''},
        checkedValue: {type: [String, Boolean, Number], default: true},
        uncheckedValue: {type: [String, Boolean, Number], default: false},
        checked: { type: [String, Number, Boolean] },
    },
    methods:{
        updateChecked(value) {
            this.$emit('input', value);
        } 
    }
}
</script>
