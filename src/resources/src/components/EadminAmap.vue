<template>
  <div>
    <el-input v-model="mapAddress">
      <template #append><el-button icon="el-icon-map-location" @click="drawer = true">地图</el-button></template>
    </el-input>
    <el-drawer
      :append-to-body="true"
      v-model="drawer"
      direction="ttb"
      size="40%"
      :destroy-on-close="true"
      :with-header="false"
      @opened="init"
    >
      <div ref="container" style="width: 100%;height: 100%" />
      <div style="position: absolute;right: 10px;top: 10px">
        <el-card :body-style="{ padding: '10px',}">
          <el-input ref="input" v-model="searchText" placeholder="请输入关键字" size="mini" prefix-icon="el-icon-search" />
        </el-card>
      </div>
      <div style="position: absolute;right: 10px;bottom: 10px">
        <el-card :body-style="{ padding: '10px',}">
          <div class="info"><b>当前选择地址: </b>{{ mapAddress }}</div>
        </el-card>
      </div>
    </el-drawer>
  </div>
</template>
<script>
import md5 from 'js-md5'
import {defineComponent, ref,watch,onMounted} from "vue";
export default defineComponent({
  name: 'EadminAmap',
  props: {
    modelValue: [String, Array],
    lat: [Number, String],
    lng: [Number, String],
    address: String,
    apiKey: {
      type: String,
      default: ''
    }
  },
  setup(props){
    if (props.modelValue) {
      [this.mapLng, this.mapLat, this.mapAddress] = props.modelValue
    }
    const container = ref('')
    const input = ref('')
    function loadScript(url, id) {
      const jsapi = document.createElement('script')
      jsapi.charset = 'utf-8'
      jsapi.id = id
      jsapi.src = url
      document.head.appendChild(jsapi)
    }
    onMounted(()=>{
      if (document.getElementById('amapscript') == null) {
        const url = 'https://webapi.amap.com/maps?v=1.4.15&key=' + props.apiKey + '&plugin=AMap.Autocomplete,AMap.PlaceSearch,AMap.Geocoder,AMap.Geolocation'
        loadScript(url, 'amapscript')
      }
    })

    function onComplete(data) {
      // data是具体的定位信息
      this.mapAddress = data.formattedAddress
      this.mapLat = data.position.lat
      this.mapLng = data.position.lng
      this.$emit('input', [this.mapLng, this.mapLat, this.mapAddress])
      position()
    }
    let map = null
    let geolocation = null
    let placeSearch = null
    let marker = null
    const geocoder = new AMap.Geocoder({
      extensions: 'all'
    })
    function position() {
      map.setCenter([this.mapLng, this.mapLat])
      marker = new AMap.Marker({
        position: [this.mapLng, this.mapLat],
        map: this.map
      })
      const info = []
      info.push("<div class='input-card content-window-card'> ")
      info.push("<p class='input-item'>" + this.mapAddress + '</p></div></div>')
      const infoWindow = new AMap.InfoWindow({
        content: info.join(''),
        offset: new AMap.Pixel(0, -35)
      })
      infoWindow.open(map, [this.mapLng, this.mapLat])
      map.setFitView([marker])
    }
    function clickMap(e) {
      geocoder.getAddress(e.lnglat, (status, result) => {
        if (status === 'complete' && result.regeocode) {
          const address = result.regeocode.formattedAddress
          this.mapAddress = address
          this.mapLat = e.lnglat.lat
          this.mapLng = e.lnglat.lng
          this.$emit('input', [this.mapLng, this.mapLat, this.mapAddress])
          if (marker) {
            map.remove(marker)
          }
          position()
        }
      })
    }
    function init() {
      map = new AMap.Map(this.containerId, {
        resizeEnable: true
      })

      if (this.value) {
        position()
      } else {
        AMap.plugin('AMap.Geolocation', () => {
          geolocation = new AMap.Geolocation({
            // 是否使用高精度定位，默认：true
            enableHighAccuracy: true,
            // 设置定位超时时间，默认：无穷大
            timeout: 10000,
            // 定位按钮的停靠位置的偏移量，默认：Pixel(10, 20)
            buttonOffset: new AMap.Pixel(10, 20),
            //  定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
            zoomToAccuracy: true,
            //  定位按钮的排放位置,  RB表示右下
            buttonPosition: 'RB'
          })
        })
        this.geolocation.getCurrentPosition()
        AMap.event.addListener(geolocation, 'complete', onComplete)
      }
      map.on('click', clickMap)

      // 输入提示
      const autoOptions = {
        input: input.value
      }
      const auto = new AMap.Autocomplete(autoOptions)
      placeSearch = new AMap.PlaceSearch({
        map: map
      })
      this.map.plugin(['AMap.ToolBar'], () => {
        this.map.addControl(new AMap.ToolBar())
      })
      if (location.href.indexOf('&guide=1') !== -1) {
        map.setStatus({ scrollWheel: false })
      }
      AMap.event.addListener(auto, 'select', select)// 注册监听，当选中某条记录时会触发
      AMap.event.addListener(placeSearch, 'markerClick', e => {
        this.mapAddress = e.data.address
        this.mapLat = e.location.lat
        this.mapLng = e.location.lng
        this.$emit('input', [this.mapLng, this.mapLat, this.mapAddress])
      })
    }
    function select(e) {
      const poi = e.poi
      placeSearch.setCity(poi.adcode)
      placeSearch.search(poi.name) // 关键字查询查询
      this.mapAddress = poi.district + poi.address
      this.mapLat = poi.location.lat
      this.mapLng = poi.location.lng
      this.$emit('input', [this.mapLng, this.mapLat, this.mapAddress])
    }
    return {
      input,
      container
    }
  },
})
</script>
<style>
  .amap-sug-result {
    z-index: 10000 !important;
  }

  .auto-item {
    font-size: 14px;
    padding: 5px 0px;
    text-indent: 10px;
  }
</style>
