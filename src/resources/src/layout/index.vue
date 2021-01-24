<template>
    <div class="app-wrapper">
        <sidebar v-if="sidebar.visible"></sidebar>
        <div class="main-container">
            <header-top></header-top>
            <div class="main-content">
                <el-backtop target=".main-content"></el-backtop>
                <div class="header-title" v-if="proxyData.eadmin_title && mainComponent">
                    <div class="title" >{{proxyData.eadmin_title}}</div>
                    <div class="breadcrumb">
                        <breadcrumb></breadcrumb>
                        <i class="el-icon-back back" @click="back"></i>
                    </div>
                </div>
                <transition name="el-fade-in">
                    <render :data="mainComponent"></render>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
    import {useRouter,useRoute} from 'vue-router'
    import {defineComponent, inject, computed, reactive, watch} from 'vue'
    import headerTop from './headerTop.vue'
    import Sidebar from './sidebar/sidebar.vue'
    import render from '/@/components/render.vue'
    import breadcrumb from '/@/components/breadcrumb.vue'
    import { store } from '/@/store'
    export default defineComponent({
        name: "index",
        components: {
            Sidebar,
            headerTop,
            render,
            breadcrumb
        },
        setup(){
            const router = useRouter()
            const state = inject(store)
            let proxyData = state.proxyData
            let sidebar = state.sidebar
            const mainComponent = computed(()=>{
                return state.mainComponent
            })
            //重新加载赋值proxyData
            watch(()=>state.mainComponent,(newValue)=>{
               if(newValue){
                   for(let i in proxyData){
                       delete proxyData[i]
                   }
                }else{


               }
            })
            function back() {
                router.back()
            }
            return {
                state,
                sidebar,
                mainComponent,
                proxyData,
                back
            }
        }
    })
</script>

<style scoped>
    .header-title .title{
        font-size: 18px;
    }
    .header-title{
        display: flex;
        justify-content: space-between;
        height: 40px;
        padding-left:15px;
        align-items: center;
        background: #ffffff;
        border-bottom: 1px solid #ededed;

    }

    .breadcrumb{
        display: flex;
        align-items: center;
    }
    .back{
        margin-left: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-left: 1px solid #ededed;
        height: 40px;
        width: 40px;
        cursor: pointer
    }
    .back:hover{
        background: #EFEFEF;
    }
</style>
