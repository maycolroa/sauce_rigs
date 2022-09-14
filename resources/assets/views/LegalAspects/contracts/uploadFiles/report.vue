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
  methods: {
    setFilters(value)
    {
      this.filters = value
    }
  }
}
</script>