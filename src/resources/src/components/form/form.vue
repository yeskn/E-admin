<template>
    <el-form ref="EadminForm" v-bind="$attrs" :model="modelValue" @submit.native.prevent>
        <slot></slot>
        <el-form-item>
            <slot name="leftAction"></slot>
            <render v-if="action.submit" native-type="submit" :loading="loading" :data="action.submit" @click="sumbitForm('EadminForm')"></render>
            <render v-if="action.reset" :data="action.reset" @click="resetForm('EadminForm')"></render>
            <slot name="rightAction"></slot>
        </el-form-item>
    </el-form>
</template>

<script>
    import {defineComponent, inject,reactive,triggerRef} from 'vue'
    import render from "/@/components/render.vue"
    import manyItem from "./manyItem.vue"
    import { store } from '/@/store'
    import { useHttp } from '/@/hooks'
    import request from '/@/utils/axios'
    export default defineComponent({
        components:{
            render,manyItem
        },
        inheritAttrs: false,
        name: "EadminForm",
        props:{
            model:Object,
            action:Object,
            //提交url
            setAction: String,
            setActionMethod:{
                type:String,
                default:'post'
            }
        },
        emits: ['success','gridRefresh'],
        setup(props,ctx){
            const {loading,http} = useHttp()
            const state = inject(store)
            const proxyData = state.proxyData
            const modelValue = reactive(props.model)
            // if(ctx.attrs.editId){
            //     const editId = ctx.attrs.editId
            //     request({
            //         url:'eadmin/'+editId+'/edit.rest',
            //         params: modelValue
            //     }).then(res=>{
            //         for(let field in res.data){
            //             modelValue[field] = res.data[field]
            //         }
            //     })
            // }
            //提交
            function sumbitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        if(props.setAction){
                            http({
                                url: props.setAction,
                                method: props.setActionMethod,
                                data: modelValue
                            }).then(res=>{
                                ctx.emit('gridRefresh')
                                ctx.emit('success')
                            })
                        }else{
                            ctx.emit('gridRefresh')
                            ctx.emit('success')
                        }
                    } else {
                        return false;
                    }
                })
            }
            //重置
            function resetForm(formName) {
                this.$refs[formName].resetFields();
            }
            return {
                modelValue,
                proxyData,
                loading,
                resetForm,
                sumbitForm,
            }
        }
    })
</script>

<style scoped>

</style>
