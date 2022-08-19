<template>
   <q-card flat bordered class="my-card bg-white q-pa-md">
      <div class="row items-center no-wrap">
        <div class="q-py-md" v-html="description"/>
      </div>
    </q-card>
</template>


<script>
import "@hipsjs/flowy-vue/dist/lib/flowy-vue.css";

export default {
  props:{
    description: { type: String }
  },
  data () {
    return {
      text: 'This is component A'
    }
  }
}
</script>
