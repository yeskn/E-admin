import { createRouter, createWebHashHistory ,RouteLocationNormalized,NavigationGuardNext} from 'vue-router'
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
var formRoute
router.beforeEach( async(to:RouteLocationNormalized, from:RouteLocationNormalized,next:NavigationGuardNext) => {
    formRoute = from
    if(!localStorage.getItem('eadmin_token') && to.path !== '/login'){
        return next('/login?redirect='+to.fullPath)
    }
    if(!state.info.id && localStorage.getItem('eadmin_token')){
        await action.getInfo()
    }
    action.loading(true)
    if(to.path === '/refresh'){
        await loadComponent(from.fullPath)
        return next(from.fullPath)
    }
    if(to.fullPath !== '/'){
        await loadComponent(to.fullPath)
    }

    return next()
})
router.afterEach((to:RouteLocationNormalized)=>{
    const styleDoms = document.querySelectorAll('[data-key=eadmin_style_' + md5(formRoute.path)+']')
    if(styleDoms){
        styleDoms.forEach(item=>{
            item.remove()
        })
    }
    action.component('')
    setTimeout(()=>{
        action.component(asyncCmponent)
    },1)
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

