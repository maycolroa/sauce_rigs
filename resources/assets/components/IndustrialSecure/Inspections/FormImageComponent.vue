<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
            
                <center>
                    <loading :display="loading"/>
                    <div class="my-4 mx-2 text-center" v-if="!loading && form.old">
                        <img class="mw-100" :src="`${form.path}`" alt="Max-width 100%">
                    </div>
                </center>
        
        <b-form-row> 
            <vue-file-simple v-if="!viewOnly" class="col-md-12" @input="submit" v-model="form.image" name="image" :error="form.errorsFor('image')" placeholder="Seleccione una imagen" :maxFileSize="10"></vue-file-simple>
            <!--<vue-file-simple :help-text="form.old ? `Para descargar la imagen actual, haga click <a href='conditionsReports/image/download' target='blank'>aqui</a> `: null" :disabled="!auth.can['logos_c']" class="col-md-12" @input="submit" accept=".png" v-model="form.image" label="Imagen (*.png, *.jpg, *.jpeg)" name="image" :error="form.errorsFor('image')" placeholder="Seleccione una imagen"></vue-file-simple>-->
        </b-form-row>
        <b-row align-h="center">
            <b-col cols="2">
                <!--<b-btn v-if="auth.can['logos_c'] && form.image" @click="deleteFile" variant="primary"><span class="ion ion-md-close-circle"></span> Eliminar Imagen</b-btn>-->
                <b-btn v-if="form.image && !viewOnly" @click="deleteFile" variant="primary"><span class="ion ion-md-close-circle"></span> Eliminar Imagen</b-btn>
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
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        reportId: { type: Number },
        image: { type: String },
        path: { type: String },
        old: { type: String },
        column: { type: String },
        logo: {
            default() {
                return {
                    image: '',
                    path: '',
                    old: '',
                };
            }
        }
    },
    data() {
        return {
            ready: false,
            loading: false,
            form: Form.makeFrom(this.logo, this.method)
        };
    },
    watch:{
        reportId(){
            this.form.image = this.image
            this.form.path = this.path
            this.form.old = this.old
        }

    },
    methods: {
        deleteFile()
        {
            this.form.image = null;

            this.submit() 
        },
        submit() {
            
                let data = new FormData();
            data.append('report_id', this.reportId);
            data.append('column', this.column);
            data.append('path', this.form.path);
            data.append('old', this.form.old);
            if(this.form.image != null){
                data.append('image', this.form.image);
            }
            
    
            this.loading = true;
            this.form.resetError()
            this.form
            .submit(this.url, false, data)
            .then(response => {
                this.loading = false;
                this.form.image = response.data.data.image
                this.form.old = response.data.data.old
                this.form.path = response.data.data.path
            })
            .catch(error => {
                this.loading = false;
            });
        }
    }
};
</script>