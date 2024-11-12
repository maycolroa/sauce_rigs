<template>
      <div>
        <b-card bg-variant="transparent" border-variant="transparent" title="" class="px-0 box-shadow-none">
          <b-row>
            <b-col>
              <div v-if="row.link">
                <b><h5 style="color: red;">Para usar este link debe darle click derecho y seleccionar abrir en pestaña de incognito.</h5></b>
                <a :href="row.link">Iniciar sesion</a>
              </div>
            </b-col>
          </b-row>
        </b-card>
      </div>
</template>


<script>
export default {
  props:{
    row: {
      type: Object, 
      default() {
        return {
          link: ""
        };
      }
    },
  },
  components: {},
  computed:{},
  data:() => ({}),
  watch:{},
  mounted() {}
}
</script>
