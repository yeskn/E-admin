<template>
    <el-switch @change="changeHandel" v-model="value"></el-switch>
</template>

<script>
    import {defineComponent,ref} from "vue";
    import request from '/@/utils/axios'
    export default defineComponent({
        name: "EadminSwitch",
        props: {
            modelValue: [String,Number,Boolean],
            url: String,
            params:Object,
            field:String,
        },
        emits: ['update:modelValue'],
        setup(props,ctx){
            const value = ref(props.modelValue)
            function changeHandel(val) {
                if(props.url){
                    let failValue
                    if (val == ctx.attrs.activeValue) {
                        failValue = ctx.attrs.inactiveValue
                    } else {
                        failValue = ctx.attrs.activeValue
                    }
                    let params = props.params
                    params[props.field] = val
                    request({
                        url:props.url,
                        method: 'put',
                        data: params
                    }).then(res=>{
                        ctx.emit('update:modelValue',val)
                    }).catch(res=>{
                        value.value = failValue
                        ctx.emit('update:modelValue',failValue)
                    })
                }else{
                    ctx.emit('update:modelValue',val)
                }
            }
            return {
                changeHandel,
                value
            }
        }
    })
</script>

<style scoped>

</style>