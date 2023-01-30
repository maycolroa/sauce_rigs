<template>
  <div>
    <div class="row">
      <!-- Logo -->
      <div class="col-md-6 offset-md-2" style="max-height: 300px; max-width: 450px;">
        <img class="img-fluid" src="~@/images/Sauce-ML Logo RiGS Principal.png">
      </div>
      <!-- / Logo -->
    </div>
    <div class="row">
      <div class="col-md-5 offset-md-2" style="padding-left: 50px; padding-right: 50px;">
      <p>Únete y conoce esta herramienta para gestionar los procesos de seguridad y salud en su empresa</p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 offset-md-2">
        <hr class="border-dark mt-0 mb-4" style="padding-bottom: 10px;">
      </div>
    </div>
    <div class="row">
      <div class="col-md-7 offset-md-1">
        <template v-for="(item_row, key_row) in data">
          <div class="row" :key="`second-${key_row}`" v-if="key_row > 0">
            <div class="col-md-12">
              <hr class="border-dark mt-0 mb-4">
            </div>
          </div>
          <b-row :key="key_row">
              <b-col v-for="(parameter, key) in item_row" :key="key">
                <router-link :to="{ name: parameter.url}" v-on:click.native="activityUser(parameter.display_name)" class="text-dark cursor-pointer item-app-navbar">
                  <center>
                    <div class="my-2 mx-2 text-center" :ref="`${parameter.image}`" @mouseover="changeClassImage(parameter.image, `${parameter.image}_hover`)">
                      <img class="ui-w-80" :src="`/images/${parameter.image}.png`" alt="" >

                      <div class="text-center font-weight-bold pt-1">
                        {{ parameter.display_name }}
                      </div>
                    </div>
                    <div class="my-2 mx-2 text-center imgHidden" :ref="`${parameter.image}_hover`" @mouseleave="changeClassImage(`${parameter.image}_hover`, parameter.image)">
                      <img class="ui-w-80" :src="`/images/${parameter.image}_hover.png`" alt="">

                      <div class="text-center font-weight-bold pt-1" style="text-decoration: underline rgb(244, 75, 82); text-underline-position: under;">
                        {{ parameter.display_name }}
                      </div>
                    </div>
                  </center>
                </router-link>
              </b-col>
             <b-col v-if="key_row == 1">
                <router-link :to="{ name: 'useractivitymonitoring'}" v-on:click.native="activityUser('Monitoreo de actividades')" class="text-dark cursor-pointer item-app-navbar">
                  <center>
                    <div class="my-2 mx-2 text-center" ref="userActivity" @mouseover="changeClassImage('userActivity', 'userActivity_hover')">
                      <img class="ui-w-80" src="/images/userActivity.png" alt="" >

                      <div class="text-center font-weight-bold pt-1">
                        Monitoreo de actividades
                      </div>
                    </div>
                    <div class="my-2 mx-2 text-center imgHidden" ref="userActivity_hover" @mouseleave="changeClassImage('userActivity_hover', 'userActivity')">
                      <img class="ui-w-80" src="/images/userActivity_hover.png" alt="">

                      <div class="text-center font-weight-bold pt-1" style="text-decoration: underline rgb(244, 75, 82); text-underline-position: under;">
                        Monitoreo de actividades
                      </div>
                    </div>
                  </center>
                </router-link>
              </b-col>
          </b-row>
        </template>
      </div>
    </div>
  </div>
</template>

<style>
.imgHidden {
    display: none;
}
</style>

<script>
  export default {
    props: {
      apps: {
        type: [Object, Array],
        required: true,
        default: function() {
          return [];
        }
      }
    },
    computed: {
      data() {
        if (Object.keys(this.apps).length > 0)
        {
          //return this.apps;
          let aux = []
          let data_row = []

          for (var prop in this.apps)
          {
            console.log(prop)
            let item = this.apps[prop]
            item.url = prop
            item.current_img = item.image

            aux.push(item);

            if (aux.length == 3)
            {
              data_row.push(aux)
              aux = []
            }
          }

          if (aux.length > 0)
          {
            data_row.push(aux)
          }

          return data_row;
        }

        return [];
      }
    },
    methods: {
      imagePath(image) {
        return require(`../images/${image}.png`)
      },
      changeClassImage(image, imageHover) {
        this.$refs[image][0].classList.add("imgHidden");
        this.$refs[imageHover][0].classList.remove("imgHidden");
      },
      activityUser(description)
      {
        this.userActivity(description)
      }
    }
  }
</script>

<style>