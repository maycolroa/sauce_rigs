<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Audiometrias
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header>
                <b-btn :to="{name:'audiometry-create'}" variant="primary">Crear Audiometria</b-btn>
        </b-card-header>
        <b-card-body>
            <!-- <vue-table
                url-data="{{route('audiometry.data')}}"
                :fields="{{ $viewService->getAudiometryVuetable() }}"
                :filter-columns="true"
                ></vue-table> -->
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
export default {
  name: 'audiometry',
  metaInfo: {
    title: 'Audiometria'
  }
}
</script>
