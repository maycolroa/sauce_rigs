<template>
  <sidenav :orientation="orientation" :class="curClasses">
    <!-- Brand logo -->
    <div class="app-brand logo" v-if="orientation !== 'horizontal'">
      <span class="app-brand-logo logo bg-primary">
        <div class="ui-w-30 rounded-circle align-middle text-circle">{{ firstCharAppName }}</div>
      </span>
      <router-link :to="{ name: routeAppName}" class="app-brand-text logo sidenav-text font-weight-normal ml-2"> {{ appName }} </router-link>
      <a href="javascript:void(0)" class="layout-sidenav-toggle sidenav-link text-large ml-auto" @click="toggleSidenav()">
        <i class="ion ion-md-menu align-middle"></i>
      </a>
    </div>
    <div class="sidenav-divider mt-0" v-if="orientation !== 'horizontal'"></div>

    <!-- Inner -->
    <div class="sidenav-inner" :class="{ 'py-1': orientation !== 'horizontal' }">
      <template v-for="(item, index) in modules">
        <template v-if="item['subModules'] != undefined"> <!--Sub Modulos -->
          <sidenav-menu icon="fas fa-angle-right" :key="index">
            <template slot="link-text">{{ item.display_name }}</template>
            <sidenav-router-link :to="{ name: (item.name+'-'+subItem.name) }" :active="isMenuActive(item.name+'-'+subItem.name)" :exact="true"
                v-for="(subItem, subIndex) in item.subModules" :key="subIndex"> 
                {{ keywordCheck(subItem.name, subItem.display_name) }} 
            </sidenav-router-link>
          </sidenav-menu>
        </template>
        <template v-else> <!-- Link Directo -->
          <sidenav-router-link icon="fas fa-angle-right" :to="{ name: (routeAppName+'-'+item.name)}" :active="isMenuActive(routeAppName+'-'+item.name)" :exact="true" :key="index"> 
              {{ keywordCheck(item.name, item.display_name) }} 
          </sidenav-router-link>
        </template>
      </template>
    </div>
  </sidenav>
</template>

<script>
import { Sidenav, SidenavRouterLink, SidenavMenu, SidenavHeader, SidenavBlock, SidenavDivider } from '@/vendor/libs/sidenav'

export default {
  components: {
    Sidenav,
    SidenavRouterLink,
    SidenavMenu,
    SidenavHeader,
    SidenavBlock,
    SidenavDivider
  },

  props: {
    orientation: {
      type: String,
      default: 'vertical'
    },
    data: {
      type: Object,
      required: true,
      default: {}
    }
  },

  computed: {
    curClasses () {
      let bg = this.layoutSidenavBg

      if (this.orientation === 'horizontal' && (bg.indexOf(' sidenav-dark') !== -1 || bg.indexOf(' sidenav-light') !== -1)) {
        bg = bg
          .replace(' sidenav-dark', '')
          .replace(' sidenav-light', '')
          .replace('-darker', '')
          .replace('-dark', '')
      }

      return `bg-${bg} ` + (
        this.orientation !== 'horizontal'
          ? 'layout-sidenav'
          : 'layout-sidenav-horizontal container-p-x flex-grow-0'
      )
    },
    modules: function () {
      return this.data[this.routeAppName] != undefined ? this.data[this.routeAppName].modules : []
    },
    appName: function () {
        return this.data[this.routeAppName] != undefined ? this.data[this.routeAppName].display_name : ''
    },
    firstCharAppName: function () {
      return this.appName.substr(0,1).toUpperCase()
    }
  },

  methods: {
    isMenuActive (url) {
      return this.$route.name.indexOf(url) === 0
    },

    isMenuOpen (url) {
      return this.$route.path.indexOf(url) === 0 && this.orientation !== 'horizontal'
    },

    toggleSidenav () {
      this.layoutHelpers.toggleCollapsed()
    }
  }
}
</script>
