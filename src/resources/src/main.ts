import ElementPlus from "element-plus";
import 'ant-design-vue/dist/antd.css';
import 'element-plus/lib/theme-chalk/index.css';
import router from './router'
import {store,state,action} from './store'
import './styles/index.scss'
import app from  './app'
import './component'
import './directive'
import zhLocale from 'element-plus/lib/locale/lang/zh-cn'
import request from '@/utils/axios'
import Switch from "ant-design-vue/lib/switch"; // 加载 JS
import Table from "ant-design-vue/lib/table"; // 加载 JS

app.use(Table)
app.use(Switch)
app.use(ElementPlus,{size: 'medium', locale :zhLocale})
app.use(router)
app.provide(store, state)
app.config.globalProperties.$request = request
app.config.globalProperties.$action = action
app.mount('#app')
