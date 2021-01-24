import { createRouter, createWebHashHistory ,RouteLocationNormalized} from 'vue-router'
import request from '/@/utils/axios'
import { action,state } from '/@/store'
import md5 from 'js-md5'
import Layout from '/@/layout/index.vue'
import Login from '/@/layout/login.vue'
let asyncCmponent
const routes = [
    {
        path: '/login',
        component: Login,
    },
    {
        path: '/:pathMatch(.*)',
        component: Layout,
    },
]
const router = createRouter({
    history: createWebHashHistory(),
    routes
})
router.beforeEach( async(to:RouteLocationNormalized, from:RouteLocationNormalized) => {
    const styleDoms = document.querySelectorAll('[data-key=eadmin_style_' + md5(from.path)+']')
    if(styleDoms){
        styleDoms.forEach(item=>{
            item.remove()
        })
    }
    action.loading(true)
    if(!state.info.id){
        await action.getInfo()
    }
    await loadComponent(to.fullPath)
    return true
})
router.afterEach((to:RouteLocationNormalized)=>{
    action.component(asyncCmponent)
})
function loadComponent(url){
    return new Promise((resolve, reject) =>{
        request({
            url:url
        }).then(res=>{
            asyncCmponent = res;
            resolve(res)
        }).catch(res=>{
            asyncCmponent = ''
            resolve(res)
        }).finally(()=>{
            action.loading(false)
        })
    })
}

export default router

