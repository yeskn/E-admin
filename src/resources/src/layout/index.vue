<template>
    <div class="app-wrapper">
        <sidebar></sidebar>
        <div class="main-container">
            <header-top></header-top>
            <div class="main-content">
                <render :data="mainComponent"></render>
            </div>
        </div>
    </div>
</template>

<script>
    import {defineComponent, inject, computed, reactive, watch} from 'vue'
    import headerTop from './headerTop.vue'
    import Sidebar from './sidebar.vue'
    import render from '/@/components/render.vue'
    import { store } from '/@/store'
    export default defineComponent({
        name: "index",
        components: {
            Sidebar,
            headerTop,
            render,
        },

        setup(){
            const state = inject(store)
            let proxyData = state.proxyData
            const mainComponent = computed(()=>{
               return  state.mainComponent
            })
            //赋值方法
            function setProxyData(data){
                for(let field in data.bind){
                    proxyData[field] = data.bind[field]
                }
                for(let slot in data.content){
                    data.content[slot].forEach(item=>{
                        if(typeof(item) == 'object'){
                            setProxyData(item)
                        }
                    })
                }
            }
            //重新加载赋值proxyData
            watch(()=>state.mainComponent,(newValue,oldVlaue)=>{
               if(newValue){
                   for(let i in proxyData){
                       delete proxyData[i]
                   }
                   setProxyData(newValue)
                }
            })
            return {
                mainComponent,
                proxyData
            }
        }
    })
</script>

<style scoped>

</style>
