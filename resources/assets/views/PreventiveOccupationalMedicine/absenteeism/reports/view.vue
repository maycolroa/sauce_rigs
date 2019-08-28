<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Informes /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body><!--
          <div class="col-md-12">
            <iframe src="{{ $url }}" class="col-md-12" height="800" frameborder="0" id="iframeInforme"></iframe>
          </div>
            <iframe :src="queryComplete" frameborder="0"></iframe>-->
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>

import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-reports-view',
  metaInfo: {
    title: 'Informes - Ver'
  }/*,
  data () {
    return {
      query: ''
    }
  },
  computed: {
    queryComplete () {
      return this.query;
    }
  }*/
}
</script>