<template>
    <el-drawer v-model="visible" v-bind="$attrs">
        <template #title>
            <span>
                <slot name="title"></slot>
             </span>
        </template>
        <slot></slot>
        <render :data="content" :slot-props="slotProps" @success="hide"></render>
    </el-drawer>
    <span @click="open">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {defineComponent,ref,watch} from "vue";
    import {useVisible, useHttp} from '@/hooks'
    import render from '@/components/render.vue'
    export default defineComponent({
        name: "EadminDrawer",
        components: {
            render,
        },
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
        emits:['update:modelValue'],
        setup(props,ctx){
            const {visible,show,hide} = useVisible(props,ctx)
            let content = ref(null)
            watch(()=>props.modelValue,(value)=>{
                if(value){
                    open()
                }
            })
            function open(){
                show(()=>{
                    if (props.url) {
                        const {http} = useHttp()
                        http({
                            url: props.url,
                            params:props.params,
                            method:props.method
                        }).then(res => {
                            content.value = res
                        })
                    }
                })
            }
            return {
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
