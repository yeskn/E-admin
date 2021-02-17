<template>
    <el-dialog v-model="visible" v-bind="$attrs">
        <template #title>
            <span>
                <slot name="title"></slot>
             </span>
        </template>
        <slot></slot>
        <render :data="content" :slot-props="slotProps" @success="hide"></render>
    </el-dialog>
    <span @click.stop="open">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {defineComponent, ref,watch} from "vue";
    import render from '/@/components/render.vue'
    import {useVisible, useHttp} from '/@/hooks'

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
            url: String,
            params:Object,
            slotProps:Object
        },
        emits: ['update:modelValue'],
        setup(props, ctx) {
            const {visible,hide} = useVisible(props, ctx)
            let content = ref(null)
            watch(()=>props.modelValue,(value)=>{
                if(value){
                    open()
                }
            })
            function open(){
                if (props.url) {
                    const {http} = useHttp()
                    http({
                        url: props.url,
                        params:props.params
                    }).then(res => {
                        content.value = res
                        visible.value = true
                    })
                }else{
                    visible.value = true
                }
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
