import { createRouter, createWebHashHistory ,RouteLocationNormalized} from 'vue-router'
import request from '/@/utils/axios'
import { action,state } from '/@/store'
import {inject} from 'vue'
import Layout from '/@/layout/index.vue'

const routes = [
    {
        path: '/:pathMatch(.*)',
        component: Layout,
    },
]
const router = createRouter({
    history: createWebHashHistory(),
    routes
})
export default router
router.beforeEach( async(to:RouteLocationNormalized, from:RouteLocationNormalized) => {
    if(!state.info.id){
        await action.getInfo()
    }
    request({
        url:to.fullPath
    }).then(res=>{
        action.component(res)
    })
    return true
})
