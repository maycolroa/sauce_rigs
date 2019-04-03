<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <b-form-row>
            <action-plan-component
                :form="form"
                :isEdit="true"
                :isEditItem="true"
                :action-plan-states="actionPlanStates"
                v-model="form.actionPlan"
                :action-plan="form.actionPlan"/>
        </b-form-row>

        <div class="row float-right pt-10 pr-10">
            <template>
                <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
                <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
            </template>
        </div>
    </b-form>
</template>

<script>
import Form from "@/utils/Form.js";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';

export default {
    components: {
        ActionPlanComponent
    },
    props: {
        url: { type: String },
        method: { type: String },
        cancelUrl: { type: [String, Object], required: true },
        actionPlanStates: {
            type: Array,
            default: function() {
                return [];
            }
        },
        actionPlan: {
            default() {
                return {
                    actionPlan: {
                        activities: []
                    }
                }
            }
        }
    },
    watch: {
        actionPlan() {
            this.loading = false;
            this.form = Form.makeFrom(this.actionPlan, this.method);
        }
    },
    data() {
        return {
            loading: this.isEdit,
            form: Form.makeFrom(this.actionPlan, this.method)
        };
    },
    methods: {
        submit(e) {
            this.loading = true;
            this.form
                .submit(e.target.action)
                .then(response => {
                    this.loading = false;
                    this.$router.push({ name: "administrative-actionplans" });
                })
                .catch(error => {
                    this.loading = false;
                });
        },
    }
};
</script>