<template>
    <render v-loading="loading" :data="component" v-show="show"></render>
</template>

<script>
    import {useHttp} from '@/hooks'
    import {defineComponent,watch,ref} from 'vue'
    export default defineComponent({
        name: "watchComponent",
        props:{
            field:String,
            params:Object,
            watchComponent:Object,
            proxyData:Object,
        },
        setup(props){
            const {loading,http} = useHttp()
            const component = ref(props.watchComponent)
            const show = ref(false)
            watch(()=>props.proxyData[props.field],value=>{
                http({
                    url:'eadmin.rest',
                    params:Object.assign(props.params,{value:value,field:props.field})
                }).then(res=>{
                    component.value = res.content.default[0]
                    show.value = true
                })
            })
            return {
                loading,
                show,
                component
            }
        }
    })
</script>

<style scoped>

</style>