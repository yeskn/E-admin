import { createRouter, createWebHashHistory } from 'vue-router'
import request from '/@/utils/axios'
import { action } from '/@/store'
import Layout from '/@/layout/index.vue'

const routes = [
    {
        path: '/',
        name: 'layout',
        component: Layout
    },
    {
        path: '/test',
        name: 'test',
        component: Layout
    },
]
const router = createRouter({
    history: createWebHashHistory(),
    routes
})
export default router
router.beforeEach( (to, from) => {
    request({
        url:'/admin/index/dashboard'
    }).then(res=>{
        action.component(res)
    })
    return true
})
