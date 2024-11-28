<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="REPORTE DE SUBIDA DE ARCHIVOS"
        url="legalaspects-upload-files"
    />

    <div class="col-md">
        <b-card no-body>
            <b-card-body>
                <vue-table
                    configName="legalaspects-fileUpload-reports"
                    @filtersUpdate="setFilters"
                    ></vue-table>
            </b-card-body>
        </b-card>
        <b-card>
            <b-card-body>                
                <vue-table
                    ref="employeeContracts"
                    configName="legalaspects-fileUpload-employee-contracts-report"
                    @filtersUpdate="setFilters"
                    :params="{filters}"
                    :customColumnsName="true" 
                    ></vue-table>
            </b-card-body>
        </b-card>
    </div>
  </div>
</template>

<script>
export default {
    name: 'legalaspects-upload-files-report',
    metaInfo: {
        title: 'Subida de archivos - Reportes'
    },
    data () {
    return {
      filters: []
    }
  },
  watch: {
      filters: {
          handler(val) {
            console.log('entro aqui')
              this.$refs.employeeContracts.refresh()
          },
          deep: true,
      },
  },
  methods: {
    setFilters(value)
    {
      this.filters = value
    }
  }
}
</script>