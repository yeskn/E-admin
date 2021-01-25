import ElementPlus from 'element-plus';
import 'element-plus/lib/theme-chalk/index.css';
import router from './router'
import {store,state,action} from './store'
import './styles/index.scss'
import app from  './app'
import './component'
import zhLocale from 'element-plus/lib/locale/lang/zh-cn'
import request from '/@/utils/axios'
app.use(ElementPlus,{size: 'medium', locale :zhLocale})
app.use(router)
app.provide(store, state)
app.config.globalProperties.$request = request
app.mount('#app')
