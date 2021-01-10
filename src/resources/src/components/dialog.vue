<template>
    <el-dialog v-model="visible" v-bind="$attrs">
        <template #title>
            <span>
                <slot name="title"></slot>
             </span>
        </template>
        <slot></slot>
    </el-dialog>
    <span @click="opend">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {defineComponent, ref,watch} from "vue";
    import render from '/@/components/render.vue'
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
            const visible = ref(props.modelValue)
            watch(()=>props.modelValue,(value)=>{
                visible.value = value
            })
            watch(visible,(value)=>{
                ctx.emit('update:modelValue',value)
            })
            function opend(){
                visible.value = true
            }
            return {
                visible,
                opend
            }
        }
    })
</script>

<style scoped>

</style>
