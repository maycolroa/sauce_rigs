<template>
    <div>
        <div class="row">
            <div class="col-md-4" v-for="(item, index) in modules" :key="index">
                <b-card class="mb-4">

                    <template v-if="item['subModules'] != undefined"> <!--Sub Modulos -->
                        <template>
                            <center>
                                <b-dd variant="default" class="text-dark text-center font-weight-bolder" :text="item.display_name" :right="isRTL">
                                    <b-dd-item :to="{ name: (item.name+'-'+subItem.name) }" @click="activityUser(subItem.display_name)" v-for="(subItem, subIndex) in item.subModules" :key="subIndex">{{ keywordCheck(subItem.name, subItem.display_name) }} </b-dd-item>
                                </b-dd>
                            </center>
                        </template>
                    </template>

                    <template v-else> <!-- Link Directo -->
                        <router-link class="text-dark cursor-pointer item-app-navbar" v-on:click.native="activityUser(item.display_name)" :to="{ name: (routeAppName+'-'+item.name)}" :key="index"> 
                            <center>
                                <div class="my-2 mx-2 text-center">
                                    <div class="text-center">
                                        <span class="text-big">
                                            {{ keywordCheck(item.name, item.display_name) }} 
                                        </span>
                                    </div>
                                </div>
                            </center>
                        </router-link>
                    </template>

                </b-card>
            </div>
            <div class="col-md-4">
              <b-card class="mb-4" v-if="showHelp">
                <template>
                    <router-link class="text-dark cursor-pointer item-app-navbar" v-on:click.native="activityUser('Ayudas Administrativo')" :to="{name:'administrative-customHelpers'}"> 
                        <center>
                            <div class="my-2 mx-2 text-center">
                                <div class="text-center">
                                    <span class="text-big">
                                        Ayudas
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
    data() {
      return {
        showHelp: false
      }
    },
    computed: {
      modules() {
        if (Object.keys(this.apps).length > 0)
        {
          if (this.routeAppName == 'administrative')
          {
            this.showHelp = true;
          }

          return this.apps[this.routeAppName] != undefined ? this.apps[this.routeAppName].modules : []
        }

        return [];
      }
    },
    methods: {
      activityUser(description)
      {
        this.userActivity(description)
      }
    }
  }
</script>
