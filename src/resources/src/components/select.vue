<template>
    <el-select v-model="value"><slot>{{a}}</slot></el-select>
</template>

<script>
    import {defineComponent,watch,ref} from "vue";
    import request from '@/utils/axios'
    import {findTree} from '@/utils'
    export default defineComponent({
        name: "EadminSelect",
        props:{
            params: Object,
            modelValue:[Object,Array,String,Number],
            loadOptionField:[Object,Array,String,Number],
            loadField:[Object,Array,String,Number],
            options:[Object,Array,String,Number],
        },
        emits:['update:modelValue','update:loadField','update:loadOptionField'],
        setup(props,ctx){
            const value = ref(props.modelValue)
            let loadFieldValue = props.loadField
            watch(()=>props.modelValue,val=>{
                value.value =val
                changeHandel(val,true)
            })
            watch(value,value=>{
                ctx.emit('update:modelValue',value)
            })
            if(!findTree(props.options,value.value,'id')){
                value.value = ''
                loadFieldValue = ''
            }
            changeHandel(value.value,false)
            function changeHandel(val,reset=true) {
                if(props.params){
                    request({
                        url: '/eadmin.rest',
                        method:'post',
                        params: Object.assign(props.params, {eadminSelectLoad: true, eadmin_id: val}),
                    }).then(res=>{
                        ctx.emit('update:loadOptionField',res.data)
                        if(reset){
                            ctx.emit('update:loadField','')
                        }else{
                            ctx.emit('update:loadField',loadFieldValue)
                        }
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
