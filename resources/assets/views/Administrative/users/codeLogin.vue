<template>
  <div>
    <header-module
      title="CÓDIGO DE VALIDACIÓN"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <div>
            <template>
              <code-login-form
                url="/verifyCodeLogin"
                method="POST"/>
            </template>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import CodeLoginForm from '@/components/Administrative/Users/CodeLoginForm.vue';
import Loading from "@/components/Inputs/Loading.vue";
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'code-login',
  metaInfo: {
    title: 'Código de validación'
  },
  components:{
    CodeLoginForm,
    Loading
  },
  data(){
    return {
      ready: false
    }
  },
  created(){}
}
</script>
