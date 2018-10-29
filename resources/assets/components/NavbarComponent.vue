<template>
  <b-navbar toggleable="lg" :variant="getLayoutNavbarBg()" class="layout-navbar align-items-lg-center container-p-x">

    <b-navbar-brand to="/" class="app-brand demo d-lg-none py-0 mr-4">
      <span class="app-brand-logo logo bg-primary">
        <div class="ui-w-30 rounded-circle align-middle text-circle">S</div>
      </span>
      <span class="app-brand-text logo font-weight-normal ml-2"> {{ appName }} </span>
    </b-navbar-brand>

    <!-- Sidenav toggle-->
    <b-navbar-nav class="layout-sidenav-toggle d-lg-none align-items-lg-center mr-auto" v-if="sidenavToggle">
      <a class="nav-item nav-link px-0 mr-lg-4" href="javascript:void(0)" @click="toggleSidenav">
        <i class="ion ion-md-menu text-large align-middle" />
      </a>
    </b-navbar-nav>

    <!-- Navbar toggle -->
    <b-navbar-toggle target="app-layout-navbar"></b-navbar-toggle>

  <b-collapse is-nav id="app-layout-navbar">
      <!-- Divider -->
      <hr class="d-lg-none w-100 my-2">

      <b-navbar-nav class="align-items-lg-center">
        <!-- Search -->
        <label class="nav-item navbar-text navbar-search-box p-0 active">
          <i class="ion ion-ios-search navbar-icon align-middle"></i>
          <span class="navbar-search-input pl-2">
            <input type="text" class="form-control navbar-text mx-2" placeholder="Buscar..." style="width:200px">
          </span>
        </label>
      </b-navbar-nav>

      <b-navbar-nav class="align-items-lg-center ml-auto">
        
        <label class="nav-item navbar-text navbar-search-box p-0 active">
          <div class="media-body line-height-condenced ml-3">
            <div class="text-dark">{{ companyName }}</div>
          </div>
        </label>

        <b-nav-item-dropdown no-caret :right="!isRTL" class="demo-navbar-notifications mr-lg-3"
            v-if="Object.keys(company.data).length > 1">
          <template slot="button-content">
            <i class="fas fa-sync navbar-icon align-middle"></i>
            <span class="d-lg-none align-middle">&nbsp; </span>
          </template>

          <b-list-group flush>
            <template v-for="(item, index) in company.data">
              <b-list-group-item href="javascript:void(0)" class="media d-flex align-items-center"
                 :key="index" v-if="index != company.selected" @click="changeCompany(index)">
                <div class="ui-icon ui-icon-sm ion bg-primary border-0 text-white"> {{ item.name.substr(0,1).toUpperCase() }} </div>
                <div class="media-body line-height-condenced ml-3">
                  <div class="text-dark">{{ item.name }}</div>
                </div>
              </b-list-group-item>
            </template>
          </b-list-group>
        </b-nav-item-dropdown>

        <!-- Divider -->
        <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>

        <!--Aplications-->

        <b-nav-item-dropdown no-caret :right="!isRTL" id="navbar-application-sauce" class="navbar-application-sauce mr-lg-3">
          <template slot="button-content">
            <i class="ion ion-md-apps navbar-icon align-middle"></i>
            <span class="d-lg-none align-middle">&nbsp; Aplicaciones</span>
          </template>

          <b-row>
            <template v-for="(item, index) in apps">
              <b-col :key="index" v-if="item.modules.length > 0">
                <router-link :to="{ name: index}" v-on:click.native="toggleApp()" class="text-dark cursor-pointer item-app-navbar">
                <div class="my-2 mx-2 text-center">
                  <img class="ui-w-60" :src="`/images/${item.image}.png`" alt="">
                  <div class="text-center font-weight-bold pt-1">
                    {{ item.display_name }}
                  </div>
                </div>
                </router-link>
              </b-col>
            </template>
          </b-row>
        </b-nav-item-dropdown>

        <!-- Divider -->
        <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>


      <!-- User -->
        <b-nav-item-dropdown :right="!isRTL" class="">
          <template slot="button-content">
            <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
              <span class="app-brand-logo logo bg-primary">
                <div class="ui-w-30 rounded-circle align-middle text-circle">S</div>
              </span>
            </span>
            
          </template>

          <b-dd-item><i class="ion ion-ios-person text-lightest"></i> &nbsp; Mi Perfil</b-dd-item>
          <b-dd-divider />
          <b-dd-item href="/logout"><i class="ion ion-ios-log-out text-danger"></i> &nbsp; Cerrar sesión</b-dd-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  props: {
    sidenavToggle: {
      type: Boolean,
      default: true
    },
    data: {
      type: Object,
      required: true,
      default: {}
    }
  },
  components: {},
  data(){
      return {
        company: {
          selected: null,
          data: []
        }
      }
    },
  methods: {
    toggleSidenav() {
      this.layoutHelpers.toggleCollapsed();
    },

    getLayoutNavbarBg() {
      return this.layoutNavbarBg;
    },
    logout() {
      axios
        .post("/logout")
        .then(response => {
          location.href = "/login";
        })
        .catch(error => {
          location.href = "/login";
        });
    },
    companies () {
      axios
        .get('/getCompanies')
        .then(response => {
            this.company.selected = response.data.selected
            this.company.data = response.data.data
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    },
    changeCompany(company) {
      axios
        .post('/changeCompany', {
            company_id: company,
            currentPath: this.$route.path,
            currentName: this.$route.name
        })
        .then(response => {
            window.location = response.data
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    }, 
    toggleApp: function () {
      document.getElementById('navbar-application-sauce__BV_button_').click()
    }
  },
  created () {
    this.companies()
  },
  computed: {
      apps: function () {
        return this.data;
      },
      appName: function () {
        return this.data[this.routeAppName] != undefined ? this.data[this.routeAppName].display_name : ''
      },
      companyName: function () {
        return this.company.data[this.company.selected] != undefined ? this.company.data[this.company.selected].name : ''
      }
  }
};
</script>
