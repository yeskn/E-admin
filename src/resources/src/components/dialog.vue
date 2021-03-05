<template>
    <el-dialog v-model="visible" v-bind="$attrs">
        <template #title>
            <slot name="title"></slot>
        </template>
        <slot></slot>
        <render :data="content" :slot-props="slotProps" @success="hide"></render>
    </el-dialog>
    <span @click.stop="open">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {defineComponent, watch} from "vue";
    import render from '@/components/render.vue'
    import {useVisible} from '@/hooks'

    export default defineComponent({
        name: "EadminDialog",
        components: {
            render,
        },
        inheritAttrs: false,
        props: {
            modelValue: {
                type: Boolean,
                default: false,
            },
            show:Boolean,
            url: String,
            params:Object,
            //请求method
            method: {
                type: String,
                default: 'get'
            },
            slotProps:Object
        },
        emits: ['update:modelValue','update:show'],
        setup(props, ctx) {
            const {visible,hide,useHttp} = useVisible(props,ctx)
            const {content,http} = useHttp()
            watch(()=>props.show,(value)=>{
                if(value){
                    open()
                }
                ctx.emit('update:show',value)
            })
            function open(){
                http(props)
            }
            return {
                hide,
                content,
                visible,
                open,
            }
        }
    })
</script>

<style scoped>

</style>
