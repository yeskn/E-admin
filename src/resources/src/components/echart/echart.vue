<template>
  <div ref='echart' :style="{height:height,width:width}" />
</template>

<script>
import * as echarts from 'echarts'
import {defineComponent,ref,onMounted,onActivated,onUnmounted,onUpdated} from 'vue'

export default defineComponent({
  name: 'EadminChart',
  props: {
    width: {
      type: String,
      default: '100%'
    },
    height: {
      type: String,
      default: '350px'
    },
    options: {
      type: Object,
      required: true
    }
  },
  setup(props){

    const echart = ref('')
    function initChart() {
      const chart = echarts.init(echart.value)
      chart.setOption(props.options)
    }
    onMounted(()=>{
      initChart()
    })
    onUpdated(()=>{
      initChart()
    })
    return {
      echart
    }
  }
})
</script>
