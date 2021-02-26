<template>
  <a target="_blank" :href="url">
    <div style="display: flex;align-items:center;height: 35px" @mouseover="showDownload=true" @mouseout="showDownload=false">
      <i v-show="showDownload" class="el-icon-download down" />
      <el-image v-show="!showDownload" :src="fileIcon(url)" style="width: 32px;height: 32px;">
        <div slot="error" style="display: flex; align-items: center;"> <i class="el-icon-document" style="font-size: 32px" /></div>
      </el-image>
      &nbsp;&nbsp;{{ lastName(url) }}
    </div>
  </a>
</template>

<script>
import {defineComponent,ref,computed} from "vue";
export default defineComponent({
  name: 'EadminDownloadFile',
  props: {
    filename: String,
    url: String
  },
  setup(props){
    const showDownload = ref(false)
    function lastName(path) {
      if (props.filename) {
        return props.filename
      } else {
        var index = path.lastIndexOf('\/')
        return path.substring(index + 1, path.length)
      }
    }
    function fileIcon(path){
      var index = path.lastIndexOf('\.')
      var ext = path.substring(index + 1, path.length)
      try {
        return require('@/assets/file_icon/' + ext + '.png')
      } catch (e) {
        return ''
      }
    }
    return {
      fileIcon,
      lastName,
      showDownload
    }
  }
})
</script>

<style lang="scss" scoped>
  @import "../styles/element-variables.scss";
  .down{
    color: $--color-primary;
  }
</style>
