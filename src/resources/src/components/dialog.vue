<template>
    <el-dialog v-model="visible" v-bind="$attrs">
        <template #title>
            <span>
                <slot name="title"></slot>
             </span>
        </template>
        <slot></slot>
        <render :data="content" :slot-props="$attrs.slotProps"></render>
    </el-dialog>
    <span @click="open">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {defineComponent, ref} from "vue";
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
        },
        emits: ['update:modelValue'],
        setup(props, ctx) {
            const {visible, show} = useVisible(props, ctx)
            let content = ref(null)
            const url = props.url
            function open(){
                if (url) {
                    const {http} = useHttp()
                    http({
                        url: url,
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
                content,
                visible,
                open
            }
        }
    })
</script>

<style scoped>

</style>
