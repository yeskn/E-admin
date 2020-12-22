<template>
  <div class="login-container">
    <div class="title-container">
      <h3 class="title">
        <el-avatar :size="100" fit="fill" :src="$store.state.user.web_logo" /><br>
        <span>{{ $store.state.user.web_name }}</span>
      </h3>
    </div>
    <transition name="el-zoom-in-top">
      <el-form v-if="buttonShow" ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" auto-complete="on" label-position="left">
<!--        <div class="qcrodeBg" v-if="isQrcodeLogin" @click="isQrcodeLogin = false" style="background: none;width: 100px;margin-top: 10px"><el-link type="primary">使用账号登录</el-link></div>-->
<!--        <div class="qcrodeBg" v-else @click="isQrcodeLogin = true"></div>-->
        <eadmin-wxlogin style='padding-left: 15px' scope="snsapi_login" v-if="isQrcodeLogin" appid="wx9d3c6643e5f1e030" redirect-uri="{:request()->domain()}/#/login?wxlogin=true"></eadmin-wxlogin>
        <div v-else>
          <el-form-item prop="username" :style="inputFocusIndex == 'username' ? inputFocusCss : ''">
          <span class="svg-container">
            <svg-icon icon-class="user" />
          </span>
            <el-input
                    ref="username"
                    v-model="loginForm.username"
                    placeholder="请输入账号"
                    name="username"
                    type="text"
                    tabindex="1"
                    auto-complete="on"
                    @focus="inputFocus('username')"
                    @blur="inputFocusIndex = ''"
            />
          </el-form-item>
          <el-form-item prop="password" :style="inputFocusIndex == 'password' ? inputFocusCss : ''">
          <span class="svg-container">
            <svg-icon icon-class="password" />
          </span>
            <el-input
                    :key="passwordType"
                    ref="password"
                    v-model="loginForm.password"
                    :type="passwordType"
                    placeholder="请输入密码"
                    name="password"
                    tabindex="2"
                    auto-complete="on"
                    @focus="inputFocus('password')"
                    @blur="inputFocusIndex = ''"
                    @keyup.enter.native="handleLogin"
            />
            <span class="show-pwd" @click="showPwd">
            <svg-icon :icon-class="passwordType === 'password' ? 'eye' : 'eye-open'" />
          </span>
          </el-form-item>
          <div v-if="verifyMode == 2" style="display: flex;justify-content: space-between;">
            <el-form-item prop="verify" :style="inputFocusIndex == 'verify' ? inputFocusCss : 'width:190px'">
            <span class="svg-container">
              <i class="el-icon-circle-check" />
            </span>
              <el-input
                      ref="verify"
                      v-model="loginForm.verify"
                      placeholder="请输入验证码"
                      name="verify"
                      type="text"
                      tabindex="3"
                      auto-complete="on"
                      style="width: 150px"
                      maxlength="4"
                      @keyup.enter.native="handleLogin"
              />
            </el-form-item>
            <el-image :src="verifyImage" style="height: 52px;cursor: pointer;border-radius: 5px" @click="getVerify" />
          </div>
          <el-form-item v-else-if="verifyMode == 1">
            <eadmin-slider-verify  v-model="sildeVerify" />
          </el-form-item>
          <el-button :loading="loading" type="primary" style="width:100%;margin-bottom:30px;height: 50px" @click.native.prevent="handleLogin">{{loginBtnText}}</el-button>
        </div>
      </el-form>
    </transition>
    <div class="icp">{{ $store.state.user.web_copyright }} | <a href="http://beian.miit.gov.cn" target="_blank">{{ $store.state.user.web_miitbeian }}</a></div>
    <eadmin-theme-picker style="display: none" :visable.sync="buttonShow" @change="themeChange" />
  </div>
</template>
<script>
  export default {
    name: 'Login',
    data() {
      const validatePassword = (rule, value, callback) => {
        if (value.length < 5) {
          callback(new Error('密码输入长度不能少于5位'))
        } else {
          callback()
        }
      }
      return {
        sildeVerify: false,
        verifyMode: 1,
        buttonShow: false,
        web_name: '',
        web_miitbeian: '',
        web_copyright: '',
        loginForm: {
          debug: false,
          username: '',
          password: '',
          verify: '',
          hash: '',
          wx_code:'',
        },
        loginRules: {
          username: [{ required: true, trigger: 'blur', message: '请输入账号' }],
          verify: [{ required: true, trigger: 'blur', message: '请输入验证码' }],
          password: [{ required: true, trigger: 'blur', validator: validatePassword }]
        },
        loading: false,
        passwordType: 'password',
        redirect: undefined,
        inputFocusCss: '',
        inputFocusIndex: '',
        verifyImage: '',
        app_debug: true,
        isQrcodeLogin:false,
        loginBtnText:'登录',
      }
    },
    watch: {
      $route: {
        handler: function(route) {
          this.wxLogin()
          this.redirect = route.query && route.query.redirect
        },
        immediate: true
      }
    },

    created() {
      this.getVerify()
      this.$store.dispatch('user/systemInfo')
    },
    methods: {
      themeChange(val) {
        this.$store.dispatch('settings/changeSetting', {
          key: 'theme',
          value: val
        })
      },
      getVerify() {
        this.$request('admin/system/verify').then(res => {
          this.verifyImage = res.data.image
          this.loginForm.hash = res.data.hash
          this.verifyMode = res.data.mode
          if (this.verifyMode === 2 || this.verifyMode === 0) {
            this.sildeVerify = true
          }
          if(res.data.debug === true){
            this.loginForm.username = res.data.username || '';
            this.loginForm.password = res.data.password || '';
          }
        })
      },
      inputFocus(mark) {
        this.inputFocusIndex = mark
        this.inputFocusCss = 'box-shadow: 0px 1px 6px #d3dce6;'
      },
      showPwd() {
        if (this.passwordType === 'password') {
          this.passwordType = ''
        } else {
          this.passwordType = 'password'
        }
        this.$nextTick(() => {
          this.$refs.password.focus()
        })
      },
      wxLogin(){
        const query = this.$route.query
        if(query.wxlogin && query.code){
          this.loading = true
          this.loginForm.wx_code = query.code
          this.$store.dispatch('user/login', this.loginForm).then((res) => {
            this.loading = false
            if(res.code == 422 && res.data == 40000){
              this.$message.error('微信授权失败,请重新授权绑定');
              this.$router.push('/login')
            }else if(res.code == 422){
              this.loginBtnText = '绑定微信'
              this.loginForm.wxBind = true
              this.isQrcodeLogin = false
              this.loginForm.wx_code = ''
              this.loginForm.openid = res.data
            } else{res.code == 200}{
              this.$router.push({ path: this.redirect || '/' })
            }
          }).catch(() => {
            this.loading = false
          })
        }
      },
      handleLogin() {
        this.$refs.loginForm.validate(valid => {
          if (valid && this.sildeVerify) {
            this.loading = true
            this.$store.dispatch('user/login', this.loginForm).then((res) => {
              if(res.code == 422 && res.data == 40000){
                this.$message.error('微信授权失败,请重新授权绑定');
                this.$router.push('/login')
              }else{res.code == 200}{
                this.$router.push({ path: this.redirect || '/' })
              }
              this.loading = false
            }).catch(() => {
              this.getVerify()
              this.loading = false
            })
          } else {
            return false
          }
        })
      }
    }
  }
</script>
<style scoped>
  @supports (-webkit-mask: none) and (not (cater-color: #999)) {
    .login-container .el-input input {
      color: #999;
    }
  }
  /* reset element-ui css */
  .login-container .el-input {
    display: inline-block;
    height: 47px;
    width: 85%;
  }
  .login-container  input {
    background: transparent;
    border: 0px;
    -webkit-appearance: none;
    border-radius: 0px;
    padding: 12px 5px 12px 15px;
    color: #000;
    height: 47px;
    caret-color: #999;
  }
  .login-container .qcrodeBg{
    position: absolute;
    top: 0;
    right: 0;
    height: 50px;
    width: 50px;
    overflow: hidden;
    cursor: pointer;
    background: url("{:request()->domain()}/qrcode_bg.svg") no-repeat top right;
  }

  .login-container  .el-form-item {
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: #f5f7fa;
    border-radius: 5px;
    color: #454545;
  }
  .icp{
    text-align: center;
    width: 100%;
    color: #fff;
    opacity:.5;
    font-size: 12px;
    position: absolute;
    bottom: 70px;
  }
  .login-container {
    min-height: 100%;
    width: 100%;
    background: url("{:request()->domain()}/login.png") no-repeat;
    overflow: hidden;
  }
  .login-container .login-form {
    border-radius: 5px;
    position: relative;
    width: 400px;
    max-width: 100%;
    padding: 50px 35px 0;
    margin: 0 auto;
    overflow: hidden;
    background:#fff;
  }

  .login-container .tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 10px;
  }

  .login-container .svg-container {
    padding: 6px 5px 6px 15px;
    color: #889aa4;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
  }

  .login-container .title-container {
    position: relative;

  }
  .login-container .title-container .title {
    font-size: 26px;
    color: #eee;
    margin: 80px auto 40px auto;
    text-align: center;
    font-weight: bold;
  }
  .login-container .show-pwd {
    position: absolute;
    right: 10px;
    top: 7px;
    font-size: 16px;
    color: #889aa4;
    cursor: pointer;
    user-select: none;
  }
</style>
