<template>
  <div>
    <div class="row">
      <div class="col-md-6" v-for="(item, index) in data" :key="index">

        <b-card class="mb-4">
          <router-link :to="{ name: index}" class="text-dark cursor-pointer item-app-navbar">
            <center>
              <div class="my-2 mx-2 text-center">
                <img class="ui-w-60" :src="`/images/${item.image}.png`" alt="">
                <div class="text-center font-weight-bold pt-1">
                  {{ item.display_name }}
                </div>
              </div>
            </center>
          </router-link>
        </b-card>

      </div>
    </div>
  </div>
</template>

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
          return this.apps;
        }

        return [];
      }
    }
  }
</script>

