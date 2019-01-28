<template>
    <div>
        <div class="row" v-if="!ready">
            <div class="col">
                <div class="sk-circle sk-primary">
                    <div class="sk-circle1 sk-child"></div>
                    <div class="sk-circle2 sk-child"></div>
                    <div class="sk-circle3 sk-child"></div>
                    <div class="sk-circle4 sk-child"></div>
                    <div class="sk-circle5 sk-child"></div>
                    <div class="sk-circle6 sk-child"></div>
                    <div class="sk-circle7 sk-child"></div>
                    <div class="sk-circle8 sk-child"></div>
                    <div class="sk-circle9 sk-child"></div>
                    <div class="sk-circle10 sk-child"></div>
                    <div class="sk-circle11 sk-child"></div>
                    <div class="sk-circle12 sk-child"></div>
                </div>
            </div>
        </div>

        <b-form-row>
            <vue-advanced-select 
                v-for="(item, index) in data"
                :key="index" 
                v-model="item.value_id"
                :disabled="viewOnly" class="col-md-6" :multiple="false" :options="item.values" :hide-selected="false" name="qualification" :label="item.description" :btnLabelPopover="createHelp(item.help)" placeholder="Seleccione la calificación"
                :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.qualifications.${index}.value_id`)" >
                    </vue-advanced-select>
        </b-form-row>
    </div>
</template>

<style src="@/vendor/libs/spinkit/spinkit.scss" lang="scss"></style>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";

export default {
    components: {
        VueAdvancedSelect
    },
    props: {
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        form: { type: Object, required: true }, 
        indexActivity: { type: Number, required: true },
        indexDanger: { type: Number, required: true },
        qualifications: {
            type: [Array, Object],
            default: function() {
                return [];
            }
        },
        qualificationsData: { type: [Array, Object]},
    },
    watch: {
        data: {
            handler(val){
                this.$emit('input', this.data)
            },
            deep: true
        }
    },
    data() {
        return {
            data: [],
            ready: false
        }
    },
    created() {
    },
    mounted() {
        setTimeout(() => {
            this.fetchData()
        }, 3000)
    }, 
    methods: {
        fetchData() {
            _.forIn(this.qualifications, (value, key) => {
                let value_id = ''

                if (this.qualificationsData && this.qualificationsData[value.type_id] != undefined)
                    value_id = this.qualificationsData[value.type_id].value_id

                this.data.push({
                    "description": value.description,
                    "help": value.help,
                    "type_id": value.type_id,
                    "value_id": value_id,
                    "values": value.values
                })
            });

            this.ready = true
        },
        createHelp(help) {
            if (help)
            {
                return {
                    title: "Descripción de las calificaciones",
                    icon: 'fas fa-info',
                    content: help
                }
            }
            
            return {}
        }
    }
};
</script>