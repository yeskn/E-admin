<template>
  <el-dialog
    v-model="visable"
    width="80%"
    :fullscreen="true"
    :before-close="close"
  >
    <div v-if="errorData != null">
      <div v-for="item in errorData.traces">
        <p><b>错误信息:</b></p><h1 style="color: #000000;">{{ item.message }}</h1></p>
        <p><b>错误异常:</b> {{ item.name }} <el-tag type="info" size="mini">{{ item.code }}</el-tag></p>
        <p><b>错误文件:</b> {{ item.file }} <el-tag size="mini">{{ item.line }} 行</el-tag></p>
        <div class="source-code">
          <pre><ol :start="item.source.first"><li v-for="(sourceItem,key) in item.source.source" :style="{backgroundColor:(item.source.first+key == item.line? '#f8cbcb':'')}"><code>{{ sourceItem }}</code></li></ol></pre>
        </div>
        <div class="trace">
          <h2>Call Stack</h2>
          <ol>
            <li v-for="fileItem in item.trace">
              <span v-if="fileItem.class">{{ getClass(fileItem.class) }}{{ fileItem.type }}{{ fileItem.function }}(<span v-for="(args,index) in fileItem.args">
                <span v-if="index == fileItem.args.length-1">'{{ args }}'</span><span v-else>'{{ args }}',</span></span>)</span><span v-else>{{ fileItem.function }}</span>
              <el-tooltip v-if="fileItem.file" class="item" effect="light" :content="fileItem.file" placement="top">
                <el-tag size="mini" type="info">{{ getFile(fileItem.file) }}</el-tag>
              </el-tooltip>
              <el-tag v-if="fileItem.line" size="mini">{{ fileItem.line }} 行</el-tag>
            </li>
          </ol>
        </div>
      </div>
      <div class="exception-var">
        <div>
          <h2 style="color: #2d8cf0">GET Data <el-tag v-if="errorData.tables['GET Data'].length == 0" type="info" size="mini">空</el-tag></h2>
          <div v-for="(item,key) in errorData.tables['GET Data']" style="display: flex;line-height: 25px">
            <b style="width: 250px">{{ key }}</b><div style="flex: 1;">{{ item }}</div>
          </div>
          <el-divider />
          <h2 style="color: #2d8cf0">POST Data <el-tag v-if="errorData.tables['POST Data'].length == 0" type="info" size="mini">空</el-tag></h2>
          <div v-for="(item,key) in errorData.tables['POST Data']" style="display: flex;line-height: 25px">
            <b style="width: 250px">{{ key }}</b><div style="flex: 1;">{{ item }}</div>
          </div>
          <el-divider />
          <h2 style="color: #2d8cf0">Session <el-tag v-if="errorData.tables['Session'].length == 0" type="info" size="mini">空</el-tag></h2>
          <div v-for="(item,key) in errorData.tables['Session']" style="display: flex;line-height: 25px">
            <b style="width: 250px">{{ key }}</b><div style="flex: 1;">{{ item }}</div>
          </div>
          <el-divider />
          <h2 style="color: #2d8cf0">Cookies <el-tag v-if="errorData.tables['Cookies'].length == 0" type="info" size="mini">空</el-tag></h2>
          <div v-for="(item,key) in errorData.tables['Cookies']" style="display: flex;line-height: 25px">
            <b style="width: 250px">{{ key }}</b><div style="flex: 1;">{{ item }}</div>
          </div>
          <el-divider />
          <h2 style="color: #2d8cf0">Server/Request Data</h2>
          <div v-for="(item,key) in errorData.tables['Server/Request Data']" style="display: flex;line-height: 25px">
            <b style="width: 250px">{{ key }}</b><div style="flex: 1;">{{ item }}</div>
          </div>
        </div>
      </div>
    </div>
  </el-dialog>
</template>

<script>
  import { store,action } from '/@/store'
  import {defineComponent, inject, computed, reactive, watch} from 'vue'
  export default defineComponent({
    name: 'EadminErrorPage',
    setup(){
      const state = inject(store)
      let errorData = computed(()=>{
        return state.errorPage.data
      })
      let visable = computed(()=>{
        return state.errorPage.visable
      })
      function close() {
        action.errorPageClose()
      }
      function getFile(str) {
        const index = str.lastIndexOf('/')
        return str.substring(index + 1, str.length)
      }
      function getClass(str) {
        const index = str.lastIndexOf('\\')
        return str.substring(index + 1, str.length)
      }
      return {
        visable,
        errorData,
        close,
        getFile,
        getClass
      }
    }
})
</script>

<style scoped>
  ol {
    display: block;
    list-style-type: decimal;
    margin-block-start: 1em;
    margin-block-end: 1em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    padding-inline-start: 40px;
  }
  .trace ol li {
    padding: 2px 4px;
  }
  .trace {
    padding: 6px;
    border: 1px solid #ddd;
    border-top: 0 none;
    line-height: 16px;
    font-size: 14px;
    font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
  }
  pre {
    display: block;
    font-family: monospace;
    white-space: pre;
    margin: 1em 0px;
  }
  .source-code pre {
    margin: 0;
  }
  .source-code {
    padding: 6px;
    border: 1px solid #ddd;
    background: #f9f9f9;
    overflow-x: auto;
  }
  .source-code pre ol {
    margin: 0;
    color: #4288ce;
    min-width: 100%;
    box-sizing: border-box;
    font-size: 14px;
    font-family: "Century Gothic",Consolas,"Liberation Mono",Courier,Verdana;
  }
  .source-code pre li {
    margin: 0;
    padding: 0;
    border-left: 1px solid #ddd;
    height: 18px;
    line-height: 18px;
    display: list-item;
    text-align: -webkit-match-parent;
  }
  .source-code pre code {
    color: #333;
    height: 100%;
    display: inline-block;
    border-left: 1px solid #fff;
    font-size: 14px;
    font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
  }
</style>
