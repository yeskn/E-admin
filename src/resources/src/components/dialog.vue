<template>
    <el-dialog v-model="visible" v-bind="$attrs">
        <template #title>
            <span>
                <slot name="title"></slot>
             </span>
        </template>
        <slot></slot>
    </el-dialog>
    <span @click="show">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {defineComponent} from "vue";
    import render from '/@/components/render.vue'
    import {useVisible} from '/@/hooks'
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
