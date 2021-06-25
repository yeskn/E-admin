<template>
    <component :is="drawer" v-model="visible" v-bind="$attrs">
        <template #title>
            <slot name="title"></slot>
        </template>
        <slot></slot>
        <render :data="content" :slot-props="slotProps" @success="hide"></render>
    </component>
    <span @click="open">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {computed, defineComponent, watch} from "vue";
    import {useVisible, useHttp} from '@/hooks'
    export default defineComponent({
        name: "EadminDrawer",

        inheritAttrs: false,
        props: {
            modelValue: {
                type: Boolean,
                default: false,
            },
            url: String,
            params:Object,
            //请求method
            method: {
                type: String,
                default: 'get'
            },
            slotProps:Object
        },
        emits:['update:modelValue','update:show'],
        setup(props,ctx){
            const {visible,hide,useHttp} = useVisible(props,ctx)
            const {content,http} = useHttp()
            let init = false
            watch(()=>props.modelValue,(value)=>{
                if(visible.value && !value){
                    hide()
                }
            })
            watch(()=>props.show,(value)=>{
                if(value){
                    open()
                }
            })
            function open(){
                http(props)
            }
            const drawer = computed(()=>{
                if(visible.value || init){
                    return 'ElDrawer'
                }else{
                    return null
                }
            })
            return {
                drawer,
                open,
                visible,
                content,
                hide
            }
        }
    })
</script>

<style scoped>

</style>
