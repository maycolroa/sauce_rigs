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
            <template v-for="(item, index) in data">
                <vue-input 
                    :key="index"
                    :ref="item.description"
                    v-if="item.type_input == 'text'" 
                    :disabled="item.readonly == 'SI' || viewOnly" 
                    class="col-md-6" v-model="item.value_id" :label="item.description" type="text" name="qualification" 
                    :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.qualifications.${index}.value_id`)" 
                    :placeholder="item.description">
                </vue-input>

                <vue-advanced-select 
                    :key="index"
                    :ref="item.description"
                    v-if="item.type_input == 'select'" 
                    v-model="item.value_id"
                    :disabled="item.readonly == 'SI' || viewOnly" class="col-md-6" :multiple="false" :options="item.values" :hide-selected="false" name="qualification" :label="item.description" :btnLabelPopover="createHelp(item.help)" placeholder="Seleccione la calificación"
                    :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.qualifications.${index}.value_id`)" >
                        </vue-advanced-select>
            </template>

            <template v-if="data.length > 0 && qualifications.type == 'Tipo 1'">
                <vue-input  
                    :disabled="true" class="col-md-6" v-model="calification" label="Calificación" type="text" name="qualification" 
                    placeholder="Calificación">
                </vue-input>
            </template>
        </b-form-row>
    </div>
</template>

<style src="@/vendor/libs/spinkit/spinkit.scss" lang="scss"></style>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";

export default {
    components: {
        VueInput,
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
                this.updateData()
                this.updateCalification()
            },
            deep: true
        }
    },
    data() {
        return {
            data: [],
            casillas: [],
            calification: '',
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
            _.forIn(this.qualifications.data, (value, key) => {
                let value_id = ''

                if (this.qualificationsData && this.qualificationsData[value.type_id] != undefined)
                    value_id = this.qualificationsData[value.type_id].value_id

                this.data.push({
                    "description": value.description,
                    "type_input": value.type_input,
                    "readonly": value.readonly,
                    "help": value.help,
                    "type_id": value.type_id,
                    "value_id": value_id,
                    "values": value.values
                })

                this.$set(this.casillas, value.description, key)
            });

            this.ready = true

            setTimeout(() => {
                this.updateCalification()
            }, 2000)
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
        },
        updateData() {

            if (this.qualifications.type == 'Tipo 1')
            {
                if (this.$refs["NR Personas"] != undefined && this.$refs["NR Económico"] != undefined && this.$refs["NR Imagen"] != undefined)
                {
                    if (this.data[this.casillas['NR Personas']].value_id && this.data[this.casillas['NR Económico']].value_id && this.data[this.casillas['NR Imagen']].value_id)
                    {
                        this.data[this.casillas['NRI']].value_id = Math.max(this.data[this.casillas['NR Personas']].value_id, this.data[this.casillas['NR Económico']].value_id, this.data[this.casillas['NR Imagen']].value_id)
                    }
                    else
                        this.data[this.casillas['NRI']].value_id = ''
                }
            }
        },
        updateCalification() {

            if (this.qualifications.type == 'Tipo 1')
            {
                if (this.$refs["NRI"] != undefined && this.$refs["Nivel de Probabilidad"] != undefined)
                {
                    if (this.data[this.casillas['NRI']].value_id && this.data[this.casillas['Nivel de Probabilidad']].value_id)
                    {
                        let nri = this.data[this.casillas['NRI']].value_id
                        let ndp = this.data[this.casillas['Nivel de Probabilidad']].value_id

                        if (this.qualifications.matriz_calification[nri] != undefined && this.qualifications.matriz_calification[nri][ndp] != undefined)
                        {
                            this.calification = this.qualifications.matriz_calification[nri][ndp]
                        }
                        else
                            this.calification = '' 
                    }
                }
            }
        }
    }
};
</script>