<template>
    <el-drawer v-model="visible" v-bind="$attrs">
        <template #title>
            <span>
                <slot name="title"></slot>
             </span>
        </template>
        <slot></slot>
    </el-drawer>
    <span @click="show">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {defineComponent} from "vue";
    import {useVisible} from '/@/hooks'
    import render from '/@/components/render.vue'
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
            }
        },
        emits:['update:modelValue'],
        setup(props,ctx){
            const {visible,show} = useVisible(props,ctx)
            return {
                visible,
                show
            }
        }
    })
</script>

<style scoped>

</style>
