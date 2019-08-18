<template>
    <div>
        <div class="row">
            <div class="col-md-4" v-for="(item, index) in modules" :key="index">
                <b-card class="mb-4">

                    <template v-if="item['subModules'] != undefined"> <!--Sub Modulos -->
                        <template>
                            <router-link class="text-dark cursor-pointer item-app-navbar" :to="{ name: (item.name+'-'+subItem.name) }" v-for="(subItem, subIndex) in item.subModules" :key="subIndex"> 
                                <center>
                                    <div class="my-2 mx-2 text-center">
                                        <div class="text-center">
                                            <span class="text-big font-weight-bolder">
                                                {{ item.display_name }} / {{ keywordCheck(subItem.name, subItem.display_name) }} 
                                            </span>
                                        </div>
                                    </div>
                                </center>
                            </router-link>
                        </template>
                    </template>

                    <template v-else> <!-- Link Directo -->
                        <router-link class="text-dark cursor-pointer item-app-navbar" :to="{ name: (routeAppName+'-'+item.name)}" :key="index"> 
                            <center>
                                <div class="my-2 mx-2 text-center">
                                    <div class="text-center">
                                        <span class="text-big font-weight-bolder">
                                            {{ keywordCheck(item.name, item.display_name) }} 
                                        </span>
                                    </div>
                                </div>
                            </center>
                        </router-link>
                    </template>

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
      modules() {
        if (Object.keys(this.apps).length > 0)
        {
            return this.apps[this.routeAppName] != undefined ? this.apps[this.routeAppName].modules : []
        }

        return [];
      }
    }
  }
</script>
