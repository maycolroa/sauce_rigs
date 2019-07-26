<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <div class="row">
            <div class="col-md-12">
                <center>
                    <loading :display="loading"/>
                    <div class="my-4 mx-2 text-center" v-if="!loading && form.old_logo">
                        <img class="ui-w-200" :src="`${form.logo_path}`" alt="">
                    </div>
                </center>
            </div>
        </div>
        <b-form-row> 
            <vue-file-simple :help-text="form.old_logo ? `Para descargar el logo actual, haga click <a href='/administration/logo/download' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-12" @input="submit" accept=".png" v-model="form.logo" label="Logo (*.png)" name="logo" :error="form.errorsFor('logo')" placeholder="Seleccione una imagen"></vue-file-simple>
        </b-form-row>
        <b-row align-h="center">
            <b-col cols="2">
                <b-btn v-if="form.logo" @click="deleteFile" variant="primary"><span class="ion ion-md-close-circle"></span> Eliminar Logo</b-btn>
            </b-col>
        </b-row>
    </b-form>
</template>

<script>
import Form from "@/utils/Form.js";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Loading from "@/components/Inputs/Loading.vue";

export default {
    components: {
        VueFileSimple,
        Loading
    },
    props: {
        url: { type: String },
        method: { type: String },
        cancelUrl: { type: [String, Object], required: true },
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        logo: {
            default() {
                return {
                    logo: '',
                };
            }
        }
    },
    watch: {
        logo() {
            this.loading = false;
            this.form = Form.makeFrom(this.logo, this.method, false, false);
            this.ready = true
        }
    },
    data() {
        return {
            ready: false,
            loading: this.isEdit,
            form: Form.makeFrom(this.logo, this.method)
        };
    },
    methods: {
        deleteFile()
        {
            this.form.logo = null;
            this.submit() 
        },
        submit() {
            this.loading = true;
            this.form.resetError()
            this.form
            .submit('/administration/logo')
            .then(response => {
                this.loading = false;
                this.form.logo = response.data.data.logo
                this.form.old_logo = response.data.data.old_logo
                this.form.logo_path = response.data.data.logo_path
            })
            .catch(error => {
                this.loading = false;
            });
        }
    }
};
</script>