import ElementPlus from 'element-plus';
import 'element-plus/lib/theme-chalk/index.css';
import router from './router'
import {store,state} from './store'
import './styles/index.scss'
import app from  './app'
import './component'
app.use(ElementPlus,{size: 'medium'})
app.use(router)
app.provide(store, state)
app.mount('#app')
