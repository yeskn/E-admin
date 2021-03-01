<template>
    <el-main class='form'>
    <el-form ref="eadminForm" :label-position="labelPosition" v-bind="$attrs" @submit.native.prevent>
        <slot></slot>
        <el-form-item>
            <slot name="leftAction"></slot>
            <render v-if="action.submit" native-type="submit" :loading="loading" :data="action.submit" @click="sumbitForm"></render>
            <render v-if="action.reset" :data="action.reset" @click="resetForm"></render>
            <slot name="rightAction"></slot>
        </el-form-item>
    </el-form>
    </el-main>
</template>

<script>
    import {defineComponent, inject,nextTick,ref,watch,computed} from 'vue'
    import render from "@/components/render.vue"
    import manyItem from "./manyItem.vue"
    import { store } from '@/store'
    import { useHttp } from '@/hooks'
    import request from '@/utils/axios'
    export default defineComponent({
        components:{
            render,manyItem
        },
        inheritAttrs: false,
        name: "EadminForm",
        props:{
            action:Object,
            //提交url
            setAction: String,
            setActionMethod:{
                type:String,
                default:'post'
            },
            slotProps:Object
        },
        emits: ['success','gridRefresh'],
        setup(props,ctx){
            const eadminForm = ref(null)
            const {loading,http} = useHttp()
            const state = inject(store)
            const proxyData = state.proxyData
            //提交
            function sumbitForm() {
                if(props.setAction){
                    clearValidator()
                    eadminForm.value.validate(bool=>{
                        if(bool){
                            http({
                                url: props.setAction,
                                method: props.setActionMethod,
                                data: ctx.attrs.model
                            }).then(res=>{
                                if(res.code === 422){
                                    for (let field in res.data){
                                        if(res.index){
                                            let fields = field.split('.')
                                            let name = fields.shift()
                                            let f = fields.shift()
                                            if(!proxyData[ctx.attrs.validator][name]){
                                                proxyData[ctx.attrs.validator][name] = []
                                            }
                                            if(!proxyData[ctx.attrs.validator][name][res.index]){
                                                proxyData[ctx.attrs.validator][name][res.index] = {}
                                            }
                                            proxyData[ctx.attrs.validator][name][res.index][f] = res.data[field]
                                        }else{
                                            proxyData[ctx.attrs.validator][field] = res.data[field]
                                        }
                                    }
                                    scrollIntoView()
                                }else{
                                    ctx.emit('success')
                                    ctx.emit('gridRefresh')
                                }
                            })
                        }else{
                            scrollIntoView()
                            return false
                        }
                    })
                }else{
                    ctx.emit('success')
                    ctx.emit('gridRefresh')
                }
            }
            //滚动到校验错误处
            function scrollIntoView() {
                nextTick(()=>{
                    let isError  = document.getElementsByClassName('is-error')
                    if(isError){
                        isError[0].scrollIntoView({
                            block: 'center',
                            behavior: 'smooth'
                        })
                    }
                })
            }
            //清除校验结果
            function clearValidator() {
                for (let field in proxyData[ctx.attrs.validator]){
                    let value = proxyData[ctx.attrs.validator][field]
                    if(Array.isArray(value)){
                        proxyData[ctx.attrs.validator][field] = []
                    }else{
                        proxyData[ctx.attrs.validator][field] = ''
                    }
                }
                eadminForm.value.clearValidate()
            }
            const labelPosition = computed(()=>{
                if(state.device === 'mobile'){
                    return 'top'
                }else{
                    return 'right'
                }
            })
            //重置
            function resetForm() {
                clearValidator()
                eadminForm.value.resetFields();
            }
            return {
                eadminForm,
                loading,
                resetForm,
                sumbitForm,
                labelPosition,
            }
        }
    })
</script>

<style scoped>
.form{
    background: rgb(255, 255, 255);
    border-radius: 4px;
}
</style>
