<template>
  <div ref='echart' :style="{height:height,width:width}" />
</template>

<script>
import * as echarts from 'echarts'
import {defineComponent,ref,onMounted,onUpdated} from 'vue'

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
    let chart = null
    function initChart() {
      setTimeout(()=>{
        chart = echarts.init(echart.value)
        chart.setOption(props.options)
        chart.resize()
      },1)
    }
    onUpdated(()=>{
      initChart()
    })
    return {
      echart
    }
  }
})
</script>
