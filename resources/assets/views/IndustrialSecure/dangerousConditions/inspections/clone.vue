<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="CREAR INSPECCIÓN PLANEADA"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <form-inspection-personalized
                v-if="type == '3'"
                url="/industrialSecurity/dangerousConditions/inspection"
                method="POST"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                areas-data-url="/selects/areas"
                processes-data-url="/selects/processes"
                :inspection="data"
                :typesInspection="typesInspection"
                :typesItems="typesItems"
                :cancel-url="{ name: 'dangerousconditions-inspections'}"/>
            <form-inspection
                v-else
                url="/industrialSecurity/dangerousConditions/inspection"
                method="POST"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                areas-data-url="/selects/areas"
                processes-data-url="/selects/processes"
                :typesInspection="typesInspection"
                :inspection="data"
                :cancel-url="{ name: 'dangerousconditions-inspections' }"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormInspection from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormInspectionComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import FormInspectionPersonalized from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormInspectionPersonalizadasComponent.vue';

export default {
  name: 'dangerousconditions-inspections-create',
  metaInfo: {
    title: 'Inspecciones Planeadas - Clonar'
  },
  components:{
    FormInspection,
    FormInspectionPersonalized
  },
  data(){
    return {
      data: [],
      typesInspection: [],
      typesItems: [],
      type: ''
    }
  },
  created(){
    this.fetchSelect('typesInspection', '/selects/industrialSecurity/inspectionType')
    this.fetchSelect('typesItems', '/selects/industrialSecurity/inspectionTypeItems')

    axios.get(`/industrialSecurity/dangerousConditions/inspection/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.type = response.data.data.type_id
        delete this.data.id
        this.data.additional_fields.map((field) => {
          delete field.id
          delete field.inspection_id
          return field
        })
        this.data.themes.map((theme) => {
          delete theme.id
          delete theme.inspection_id

          theme.items.map((item) => {
            delete item.id
            delete item.inspection_section_id
            return item;
          });

          return theme;
        });
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
  methods: {
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
        .then(response => {
            this[key] = response;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });
    },
  }
}
</script>
