<template>
    <el-select v-model="value"><slot>{{a}}</slot></el-select>
</template>

<script>
    import {defineComponent,watch,ref} from "vue";
    import request from '@/utils/axios'
    export default defineComponent({
        name: "EadminSelect",
        props:{
            params: Object,
            modelValue:[Object,Array,String,Number],
            loadOption:[Object,Array,String,Number],
            loadField:[Object,Array,String,Number],
            options:[Object,Array,String,Number],
        },
        emits:['update:modelValue','update:loadField','update:loadOptionField'],
        setup(props,ctx){
            const value = ref(props.modelValue)
            watch(()=>props.modelValue,val=>{
                value.value =val
                changeHandel(val)
            })
            watch(value,value=>{
                ctx.emit('update:modelValue',value)
            })
            function changeHandel(value) {
                if(props.params){
                    request({
                        url: '/eadmin.rest',
                        method:'post',
                        params: Object.assign(props.params, {eadminSelectLoad: true, eadmin_id: value}),
                    }).then(res=>{
                        ctx.emit('update:loadOptionField',res.data)
                        ctx.emit('update:loadField','')
                    })
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
