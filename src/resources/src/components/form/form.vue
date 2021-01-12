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
    import {defineComponent, inject,ref} from 'vue'
    import render from "/@/components/render.vue"
    import manyItem from "./manyItem.vue"
    import { store } from '/@/store'
    import { http } from '/@/hooks'
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
        },
        emits: ['success'],
        setup(props,ctx){
            const loading = ref(false)
            const state = inject(store)
            const proxyData = state.proxyData
            //提交
            function sumbitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        if(props.submitUrl){
                            http({
                                url:props.setAction,
                                method:'POST',
                                data: ctx.attrs.model
                            },loading).then(res=>{
                                ctx.emit('success')
                            })
                        }else{
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
