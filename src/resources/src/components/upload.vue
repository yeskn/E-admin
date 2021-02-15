<template>
  <div>
    <span v-if="displayType=='image'">
      <div v-for="(file,index) in files" :key="index" class="imgContainer" :style="{height: styleHeight,width:styleWidth}">
        <el-image
          class="image"
          fit="contain"
          :src="file"
          :preview-src-list="files"
          :style="{height: styleHeight,width:styleWidth}"
          @mouseover="showImgTool(index)"
          @mouseout="showImgToolIndex = -1"
        >
          <div
            slot="error"
            class="imageslot"
            :style="{width:styleWidth,height: styleHeight,backgroundColor:'#f5f7fa'}"
            @mouseover="showImgTool(index)"
            @mouseout="showImgToolIndex = -1"
          >
            <span>加载失败</span>
          </div>
        </el-image>
        <span v-show="showImgToolIndex == index" class="el-upload-list__item-actions" @mouseout="showImgToolIndex = -1" @mouseover="showImgTool(index)">
          <span v-if="!singleFile" @click="imgLeft(index)"><i class="el-icon-caret-left" /></span>
          <span @click="fileDelete(index)"><i class="el-icon-delete" /></span>
          <span v-if="!singleFile" @click="imgRight(index)"><i class="el-icon-caret-right" /></span>
        </span>
      </div>
    </span>
    <label v-if="displayType=='image'" v-show="showUploadBtn" ref="btn" class="uploader-btn" :style="{height: styleHeight,width:styleHeight}" @click="handelBrowse">
      <el-progress v-show="progressShow" class="progess" type="circle" :width="height" :percentage="percentage" />
      <i v-show="!progressShow" class="el-icon-plus progess" />
    </label>
    <span v-if="displayType=='file' || displayType=='audio' || displayType=='video'" class="fileList">
      <div v-for="(file,index) in files" :key="index" style="margin-bottom: 10px;">
        <div class="el-upload-list__item is-success" :style="{width:styleWidth}" @mouseover="showImgTool(index)" @mouseout="showImgToolIndex = -1">
          <a class="el-upload-list__item-name" target="_blank" :href="file">
            <div v-if="showImgToolIndex == index">
              <span style="display: flex;align-items: center;">
                <i v-show="showImgToolIndex == index" class="el-icon-download" />
                <el-image v-show="showImgToolIndex != index" :src="fileIcon(file)" style="width: 32px;height: 32px;">
                  <div slot="error" style="display: flex; align-items: center; top: 3px; position: absolute; left: 10px;"> <i class="el-icon-document" /></div>
                </el-image>
                &nbsp;&nbsp;{{ lastName(file) }}
              </span>
            </div>
            <div v-else>
              <span style="display: flex;align-items: center;top: 3px;position: absolute;left:5px">
                <i v-show="showImgToolIndex == index" class="el-icon-download" />
                <el-image v-show="showImgToolIndex != index" :src="fileIcon(file)" style="width: 32px;height: 32px;">
                  <div slot="error" style="display: flex; align-items: center; top: 3px; position: absolute; left: 10px;"> <i class="el-icon-document" /></div>
                </el-image>
                &nbsp;&nbsp;{{ lastName(file) }}
              </span>
            </div>

          </a>
          <label class="el-upload-list__item-status-label"><i class="el-icon-upload-success el-icon-check" style="color: #ffffff" /></label>
          <i class="el-icon-close" @click="fileDelete(index)" /><i class="el-icon-close-tip" />
        </div>
        <!--<video-player-->
          <!--v-if="files.length > 0 && displayType == 'video'"-->
          <!--:style="{width:styleWidth}"-->
          <!--class="video-player vjs-custom-skin"-->
          <!--:playsinline="true"-->
          <!--:options="videoList[index]"-->
        <!--/>-->
      </div>
      <!--<aplayer-->
        <!--v-if="files.length > 0 && displayType == 'audio'"-->
        <!--:style="{width:styleWidth}"-->
        <!--:music="audio"-->
        <!--:list="audioList"-->
      <!--/>-->
      <el-progress v-show="progressShow" style="margin: 13px 0px" :text-inside="true" :stroke-width="15" :percentage="percentage" />
      <label v-if="displayType == 'file' || displayType=='audio' || displayType=='video'" v-show="showUploadBtn" ref="btn" class="fileButton" @click="handelBrowse">
        <template v-if="drag">
          <i class="el-icon-upload" />
          <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
        </template>
        <template v-else>
          <i class="el-icon-upload" /> 上传文件
        </template>
      </label>
    </span>
    <el-dialog title="图片剪裁" v-model="dialogVisible" append-to-body width="50%">
      <div class="cropper-content">
        <div class="vue-cropper-content">
          <VueCropper
            ref="cropper"
            :img="cropper.img"
            :output-size="cropper.outputSize"
            :output-type="cropper.outputType"
            :info="true"
            :full="cropper.full"
            :can-move="cropper.canMove"
            :can-move-box="cropper.canMoveBox"
            :center-box="options.centerBox"
            :original="cropper.original"
            :auto-crop="cropper.autoCrop"
            :auto-crop-width="cropper.autoCropWidth"
            :auto-crop-height="cropper.autoCropHeight"
            :fixed-box="cropper.fixedBox"
            :fixed="cropper.fixed"
            :fixed-number="cropper.fixedNumber"
          />
        </div>
      </div>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="cropperFinish">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import Uploader from 'simple-uploader.js'
import OSS from 'ali-oss'
import md5 from 'js-md5'
import * as qiniu from 'qiniu-js'
// import Aplayer from 'vue-aplayer'
import { VueCropper } from 'vue-cropper'
export default {
  name: 'EadminUpload',
  components: {
    //Aplayer,
    VueCropper
  },
  props: {
    valueModel: [String, Number, Array],
    token: {
      type: String,
      default: ''
    },
    width: {
      type: [String, Number],
      default: 'auto'
    },
    height: {
      type: [String, Number],
      default: 'auto'
    },

    cropWidth: {
      type: [String, Number],
      default: 'auto'
    },
    cropHeight: {
      type: [String, Number],
      default: 'auto'
    },
    url: {
      type: String,
      default: '/'
    },
    upType: {
      type: String,
      default: 'local'
    },
    accessKey: {
      type: String,
      default: ''
    },
    secretKey: {
      type: String,
      default: ''
    },
    bucket: {
      type: String,
      default: ''
    },
    region: {
      type: String,
      default: ''
    },
    domain: {
      type: String,
      default: ''
    },
    uploadToken: {
      type: String,
      default: ''
    },
    accept: {
      type: String,
      default: '*'
    },
    saveDir: {
      type: String,
      default: ''
    },
    singleFile: {
      type: Boolean,
      default: true
    },
    displayType: {
      type: String,
      default: 'file'
    },
    isUniqidmd5: {
      type: Boolean,
      default: false
    },
    cropperOn: {
      type: Boolean,
      default: false
    },
    cropperAuto: {
      type: Boolean,
      default: false
    },
    drag: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      styleWdith: '',
      styleHeight: '',
      files: [],
      audio: {},
      audioList: [],
      videoList: [],
      dialogVisible: false,
      cropper: {
        img: '',
        outputSize: 1, // 剪切后的图片质量（0.1-1）
        full: true, // 输出原图比例截图 props名full
        outputType: 'png',
        canMove: true,
        canMoveBox: true,
        centerBox: true,
        autoCrop: true,
        autoCropWidth: this.cropWidth,
        autoCropHeight: this.cropHeight,
        fixedBox: false,
        fixed: false,
        original: false
      },
      options: {
        target: this.url,
        query: {
          saveDir: this.saveDir,
          isUniqidmd5: this.isUniqidmd5,
          upType: this.upType
        },
        testChunks: true,
        chunkSize: 1 * 1024 * 1024,
        headers: {
          Authorization: this.token
        }
      },
      uploader: null,
      attrs: {
        accept: this.accept
      },
      // 进度条显示
      progressShow: false,
      // 进度条百分比
      percentage: 0,
      // 图片工具栏index,判断显示隐藏
      showImgToolIndex: -1,
      // 显示隐藏上传按钮
      showUploadBtn: true,
      oss: null
    }
  },

  watch: {
    files(val) {
      if (this.singleFile && this.files.length == 1) {
        this.showUploadBtn = false
      } else if (this.files.length == 0) {
        this.showUploadBtn = true
      }
      this.audio = null
      this.audioList = []
      this.videoList = []
      this.files.forEach(item => {
        if (this.audio == null) {
          this.audio = {
            title: this.lastName(item),
            author: ' ',
            url: item
          }
        }
        this.audioList.push({
          title: this.lastName(item),
          author: ' ',
          url: item
        })
        this.videoList.push({
          // 播放速度
          playbackRates: [0.5, 1.0, 1.5, 2.0],
          // 如果true,浏览器准备好时开始回放。
          autoplay: false,
          // 默认情况下将会消除任何音频。
          muted: false,
          // 导致视频一结束就重新开始。
          loop: false,
          // 建议浏览器在<video>加载元素后是否应该开始下载视频数据。auto浏览器选择最佳行为,立即开始加载视频（如果浏览器支持）
          preload: 'auto',
          language: 'zh-CN',
          // 将播放器置于流畅模式，并在计算播放器的动态大小时使用该值。值应该代表一个比例 - 用冒号分隔的两个数字（例如"16:9"或"4:3"）
          aspectRatio: '16:9',
          // 当true时，Video.js player将拥有流体大小。换句话说，它将按比例缩放以适应其容器。
          fluid: true,
          sources: [{
            // 类型
            // type: 'video/mp4',
            // url地址
            src: item
          }],
          // 你的封面地址
          poster: '',
          // 允许覆盖Video.js无法播放媒体源时显示的默认信息。
          notSupportedMessage: '此视频暂无法播放，请稍后再试',
          controlBar: {
            timeDivider: true,
            durationDisplay: true,
            remainingTimeDisplay: false,
            // 全屏按钮
            fullscreenToggle: true
          }
        })
      })
      this.$emit('update:modelValue', this.files.join(','))

    },
    valueModel(val) {
      if (typeof val === 'string') {
        this.files = val.split(',')
        this.files = this.files.filter(function(s) {
          return s && s.trim()
        })
      } else if (typeof val === 'object' && val instanceof Array) {
        this.files = val
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.uploader.assignDrop(this.$refs.btn)
      this.uploader.assignBrowse(this.$refs.btn, false, this.singleFile, this.attrs)
    })
  },
  created() {
    if (this.width != 'auto') {
      this.styleWidth = this.width + 'px'
    } else {
      this.styleWidth = '100%'
    }
    if (this.height != 'auto') {
      this.styleHeight = this.height + 'px'
    } else {
      this.styleHeight = '100%'
    }
    if (typeof this.valueModel === 'string') {
      this.files = this.valueModel.split(',')
      this.files = this.files.filter(function(s) {
        return s && s.trim()
      })
    } else if (typeof this.valueModel === 'object' && this.valueModel instanceof Array) {
      this.files = this.valueModel
    }
    if (this.upType == 'oss') {
      this.oss = new OSS({
        accessKeyId: this.accessKey,
        accessKeySecret: this.secretKey,
        bucket: this.bucket,
        region: this.region
      })
    }
    this.uploader = new Uploader(this.options)
    const that = this
    this.uploader.on('fileAdded', function(file, event) {
      if (that.checkExt(file)) {
        if (that.cropperOn && file.name.indexOf('eadmincropper') == -1) {

          var reader = new FileReader()

          reader.readAsDataURL(file.file)
          reader.onload = function(e) {
            that.cropper.img = this.result
            that.dialogVisible = true
            if (that.cropperAuto) {
              setTimeout(function() {
                that.cropperFinish()
              }, 500)
            }
          }
          return false
        }
        if (that.upType == 'oss') {
          that.ossMultipartUpload(file)
        } else if (that.upType == 'qiniu') {
          that.qiniuMultipartUpload(file)
        }
      } else {
        that.uploader.cancel()
        that.$message({
          type: 'error',
          message: '不支持的上传类型格式'
        })
        return false
      }
    })
    // 开始上传
    this.uploader.on('uploadStart', function() {

    })
    // 文件已经加入到上传列表中，一般用来开始整个的上传。
    this.uploader.on('filesSubmitted', function(files, fileList) {
      if (that.upType != 'oss' && that.upType != 'qiniu') {
        that.uploader.upload()
      }
      if (files.length > 0) {
        that.progressShow = true
      }
    })
    // 单个文件上传成功
    this.uploader.on('fileSuccess', function(rootFile, file, message) {
      try {
        const res = JSON.parse(message)
        if (res.code == 200) {
          that.uploader.removeFile(file)
          that.progressShow = false
          if (that.singleFile) {
            that.files = []
          }
          that.files.push(res.data)
        }
      } catch (e) {
        that.uploader.removeFile(file)
        that.progressShow = false
        that.$message({
          type: 'error',
          message: '上传失败:未知错误'
        })
      }
    })
    this.uploader.on('fileProgress', function(rootFile, file, chunk) {
      that.progressShow = true
      that.percentage = parseInt(this.uploader.progress() * 100)
    })
    // 根下的单个文件（文件夹）上传完成
    this.uploader.on('fileComplete', function(rootFile) {
      // console.log(rootFile)
    })
    // 某个文件上传失败了
    this.uploader.on('fileError', function(rootFile, file, message) {
      that.uploader.removeFile(file)
      that.progressShow = false
      try {
        const res = JSON.parse(message)
        that.$message({
          type: 'error',
          message: res.message
        })
      } catch (e) {
        that.$message({
          type: 'error',
          message: '上传失败:未知错误'
        })
      }
    })
  },
  methods: {
    // 确认裁剪
    cropperFinish() {
      this.dialogVisible = false
      this.$refs.cropper.getCropBlob((data) => {
        var file = new File([data], this.uniqidMd5() + 'eadmincropper.png', { type: 'image' })
        this.uploader.addFile(file)
      })
    },
    fileIcon(path) {
      var index = path.lastIndexOf('\.')
      var ext = path.substring(index + 1, path.length)
      try {
        return '/@/assets/file_icon/' + ext + '.png'
      } catch (e) {
        return ''
      }
    },
    lastName(path) {
      var index = path.lastIndexOf('\/')
      return path.substring(index + 1, path.length)
    },
    uniqidMd5() {
      const rand = ('0000' + (Math.random() * Math.pow(36, 4) << 0).toString(36)).slice(-4)
      return md5(rand)
    },
    // 图片工具栏显示
    showImgTool(index) {
      this.showImgToolIndex = index
    },
    // 图片左移动
    imgLeft(index) {
      if (index > 0) {
        this.swapArray(this.files, index - 1, index)
      }
    },
    // 图片右移动
    imgRight(index) {
      if (index < this.files.length - 1) {
        this.swapArray(this.files, index, index + 1)
      }
    },
    swapArray(arr, index1, index2) {
      arr[index1] = arr.splice(index2, 1, arr[index1])[0]
      return arr
    },
    // 图片移除
    fileDelete(key) {
      this.files.splice(key, 1)
    },
    // 判断是否支持上传个类型格式
    checkExt(file) {
      if (this.accept == '*') {
        return true
      } else if (this.displayType == 'image' && file.fileType.indexOf('image') != -1) {
        return true
      } else {
        const ext = this.accept.split(',')
        if (ext.indexOf('.' + file.getExtension()) == -1) {
          return false
        } else {
          return true
        }
      }
    },
    // 七牛云上传
    async qiniuMultipartUpload(file) {
      let filename = ''
      if (this.isUniqidmd5) {
        filename = this.saveDir + this.uniqidMd5() + '.' + file.getExtension()
      } else {
        filename = this.saveDir + file.name
      }
      const that = this
      that.progressShow = true
      var observable = qiniu.upload(file.file, filename, this.uploadToken, {
        fname: filename,
        params: {}
      })
      await observable.subscribe({
        next(res) {
          that.progressShow = true
          that.percentage = parseInt(res.total.percent)
        },
        error(err) {
          that.progressShow = false
          that.$message({
            type: 'error',
            message: err.message
          })
        },
        complete(res) {
          that.uploader.removeFile(file)
          that.progressShow = false
          const url = `${that.domain}/${filename}`
          if (that.singleFile) {
            that.files = []
          }
          that.files.push(url)
        }
      })
    },
    // 阿里云开始分片上传。
    async ossMultipartUpload(file) {
      const that = this
      let filename = ''
      if (this.isUniqidmd5) {
        filename = this.saveDir + this.uniqidMd5() + '.' + file.getExtension()
      } else {
        filename = this.saveDir + file.name
      }
      that.progressShow = true
      // object-name可以自定义为文件名（例如file.txt）或目录（例如abc/test/file.txt）的形式，实现将文件上传至当前Bucket或Bucket下的指定目录。
      await this.oss.multipartUpload(filename, file.file, {
        progress: function(percentage) {
          that.progressShow = true
          that.percentage = parseInt(percentage * 100)
        }
      }).then(result => {
        // 生成文件下载地址
        that.uploader.removeFile(file)
        that.progressShow = false
        const url = `${this.domain}/${filename}`
        if (that.singleFile) {
          that.files = []
        }
        that.files.push(url)
      }).catch(err => {
        that.progressShow = false
        that.$message({
          type: 'error',
          message: err
        })
      })
    },
    handelBrowse() {
      this.percentage = 0
    }
  }
}
</script>

<style scoped>

  .vue-cropper-content {
    width: 100%;
    height: 500px;
    padding-bottom: 20px;
    text-align: center;
    display: flex;
    justify-content: space-between;
  }
  .fileButton {
    background: #fff;
    border: 1px solid #dcdfe6;
    color: #606266;
    display: inline-block;
    white-space: nowrap;
    cursor: pointer;
    -webkit-appearance: none;
    text-align: center;
    box-sizing: border-box;
    outline: none;
    margin: 0;
    transition: .1s;
    font-weight: 500;
    -moz-user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;
    padding: 0px 20px;
    font-size: 14px;
    border-radius: 4px;
  }

  .el-upload-list__item-status-label i {
    font-size: 12px;
    margin-top: 12px;
    position: absolute;
    top: 0;
    right: 17px;
    transform: rotate(-45deg);
  }
  .el-upload-list__item {
    display:flex;justify-content:space-between;
    overflow: hidden;
    z-index: 0;
    background-color: #fff;
    border: 1px solid #c0ccda;
    border-radius: 6px;
    box-sizing: border-box;
    padding: 7px 10px 0px 10px;
    height: 40px;
    margin-top: 0px;
    margin-bottom: 10px;
  }
  .el-upload-list__item-status-label {
    position: absolute;
    right: -17px;
    top: -7px;
    width: 46px;
    height: 26px;
    background: #13ce66;
    transform: rotate(45deg);
    box-shadow: 0 1px 1px #ccc;
  }
  .imageslot{
    width: 100%;
    height: 100%;
    background-color: #d3dce6;
    position: relative;
    border-radius: 5px;
    text-align: center;
  }
  .imageslot i{
    font-size: 30px;
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
  }
  .imageslot span{
    font-size: 12px;
    width: 100%;
    text-align: center;
    position: absolute; left: 50%; transform: translate(-50%,0);
  }
  .img_delete{
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
  }
  .el-upload-list__item-actions {
    position: absolute;
    width: 100%;
    height: 35px;
    cursor: pointer;
    left: 0;
    top: 0;
    text-align: center;
    color: #fff;
    font-size: 20px;
    background-color: rgba(0,0,0,.5);
    border-radius: 5%;
    transition: opacity .3s;
  }
  .imgContainer{
    margin-right: 10px;
    position: relative;;
    display: inline-block;
  }
  .image{
    border-radius: 5%;
  }
  .progess{
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
  }
  .uploader-btn {
    background-color: #fbfdff;
    border: 1px dashed #c0ccda;
    border-radius: 5px;
    box-sizing: border-box;
    cursor: pointer;
    vertical-align: top;
    display: inline-block;
    text-align: center;
    position: relative;
  }
  .uploader-btn  i {
    font-size: 28px;
    color: #8c939d;
  }
  .uploader-btn:hover {
    border: 1px dashed #2d8cf0;
  }
</style>
