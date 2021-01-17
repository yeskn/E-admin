import {ref,watch} from "vue";
const useVisible = function(props,ctx){
    const visible = ref(false)
    watch(()=>props.modelValue,(value)=>{
       visible.value = value
    })
    watch(visible,(value)=>{
        ctx.emit('update:modelValue',value)
    })
    function show(callback){
        visible.value = true
        if(callback){
            callback()
        }
    }
    function hide(callback){
        visible.value = true
        if(callback){
            callback()
        }
    }
    return {visible,show,hide}
}
export default useVisible

