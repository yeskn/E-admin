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
    import {defineComponent, ref,watch,inject} from "vue";
    import render from '/@/components/render.vue'
    import { store } from '/@/store'
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
            const state = inject(store)
            let proxyData = state.proxyData
            console.log(props.modelValue)
            const visible = ref(props.modelValue)
            watch(()=>props.modelValue,(value)=>{
                console.log(1)
                //visible.value = value
            })
            watch(visible,(value)=>{
                ctx.emit('update:modelValue',value)
                console.log(proxyData)
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
