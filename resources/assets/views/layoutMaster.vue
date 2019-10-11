<template>
<div>
  <div class="layout-wrapper layout-2">
    <div class="layout-inner">
      <div class="layout-container" :style="{ 'padding-left': paddingLayoutContainer + 'px' }" id="layout-master-page">
        <Navbar :data="data"/>

        <div class="layout-content">
          <div v-show="!isMainApp" class="router-transitions container-fluid flex-grow-1 container-p-y">
            <router-view :apps="data"/>
          </div>
          <div v-show="isMainApp" class="router-transitions container-fluid flex-grow-1" style="padding-top: 0px; padding-left: 0px;">
            <b-row style="min-height: 95vh">
              <b-col cols="2" style="background-color: #f44b52">
                <!--<template v-if="banner">
                  <img style="width: 94%; height: 100%;" :src="banner">
                </template>
                <template v-else>-->
                <template>
                  <div style="padding-top: 30px;" v-show="appImage">
                    <center>
                      <img :src="`/images/${appImage}_hover.png`" style="width:85%; height: 100%;">
                    </center>
                  </div>
                  <div class="verticaltext_content"><br>{{ appName }}</div>
                </template>
              </b-col>
              <b-col>
                <div class="row">
                  <div class="col-md-12 text-right"  style="padding-top: 30px; padding-bottom: 30px;">
                    <router-link :to="{ path: '/' }" class="text-dark cursor-pointer item-app-navbar">
                      Volver atrás <img class="ui-w-50" src="~@/images/Sauce-ML Boton Volver Atras.png">
                    </router-link>
                  </div>
                </div>
                <router-view style="min-height: 70%;" :apps="data"/>
                <layoutFooterSystem/>
              </b-col>
            </b-row>
          </div>
        </div>
      </div>
      
    </div>
    <div class="layout-overlay" @click="closeSidenav"></div>
    <notifications group="app"/>
  </div>
  <layoutFooter v-show="isHome"/>
  <layoutFooterSystem v-show="!isHome && !isMainApp"/>
</div>
</template>

<style scoped>
.verticaltext_content {
  -webkit-transform: rotate(-90deg);
  -moz-transform: rotate(-90deg);
  -ms-transform: rotate(-90deg);
  -o-transform: rotate(-90deg);
  filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
  left: -200px;
  top: 310px;
  position: absolute;
  color: #FFF;
  text-transform: uppercase;
  width: 600px;
  height: 180px;
  font-size: 40px;                 
  font-family: sans-serif;
  text-align: center;
  vertical-align: middle;
}
</style>
<script>
import Navbar from '@/components/NavbarComponent.vue';
import Sidenav from '@/components/SidenavComponent.vue';
import LayoutFooter from '@/components/LayoutFooter.vue';
import LayoutFooterSystem from '@/components/LayoutFooterSystem.vue';

export default {
    components:{
        Navbar,
        Sidenav,
        LayoutFooter,
        LayoutFooterSystem
    },
    data(){
      return {
        data: {},
      }
    },
    created () {
      this.permits()
    },
    mounted () {
      this.layoutHelpers.init()
      this.layoutHelpers.update()
      this.layoutHelpers.setAutoUpdate(true)
    },
    computed: {
      paddingLayoutContainer() {
        return this.isHomeFunction() ? '0' : '250'
      },
      isHome() {
        return this.isHomeFunction()
      },
      isMainApp() {
        if (this.$route.path != undefined)
        {
          let name = this.$route.path.split('/')
          return (name[1] != '' && !name[2]) ? true : false
        }
        else 
          return false
      },
      banner() {
        return this.data[this.routeAppName] != undefined ? (this.data[this.routeAppName].banner ? this.data[this.routeAppName].banner : null) : null
      },
      appName: function () {
        return this.data[this.routeAppName] != undefined ? this.data[this.routeAppName].display_name : ''
      },
      appImage: function () {
        return this.data[this.routeAppName] != undefined ? this.data[this.routeAppName].image : ''
      }
    },
    beforeDestroy () {
      this.layoutHelpers.destroy()
    },
    methods: {
      isHomeFunction() {
        if (this.$route.path != undefined)
        {
          let name = this.$route.path.split('/')
          return name[1] != '' ? false : true
        }
        else 
          return true
      },
      closeSidenav () {
        this.layoutHelpers.setCollapsed(true)
      },
      permits () {
          axios
            .get('/appWithModules')
            .then(response => {
                this.data = response.data;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
      }
    }
}
</script>
