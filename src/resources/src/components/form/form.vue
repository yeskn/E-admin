<template>
    <el-form ref="EadminForm" v-bind="$attrs" @submit.native.prevent>
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
    import {defineComponent, inject, ref} from 'vue'
    import request from '/@/utils/axios'
    import render from "/@/components/render.vue"
    import manyItem from "./manyItem.vue"
    import { store } from '/@/store'
    export default defineComponent({
        components:{
            render,manyItem
        },
        inheritAttrs: false,
        name: "EadminForm",
        props:{
            action:Object,
            //提交url
            submitUrl: String,
        },
        setup(props,ctx){
            const state = inject(store)
            const proxyData = state.proxyData
            let loading = ref(false)
            //提交
            function sumbitForm(formName) {
                loading.value = true
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        loading.value = true
                        request({
                            url:props.submitUrl,
                            method:'POST',
                            data: ctx.attrs.model
                        }).then(res=>{
                            if(ctx.attrs.eadminDialogVisible){
                                proxyData[ctx.attrs.eadminDialogVisible] = false
                            }
                            if(ctx.attrs.eadminDrawerVisible){
                                proxyData[ctx.attrs.eadminDrawerVisible] = false
                            }
                        }).finally(res=>{
                            loading.value = false
                        })
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
