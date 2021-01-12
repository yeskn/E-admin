import {ref,watch} from "vue";
const useVisible = function(props,ctx){
    const visible = ref(false)
    watch(()=>props.modelValue,(value)=>{
       visible.value = value
    })
    watch(visible,(value)=>{
        ctx.emit('update:modelValue',value)
    })
    function show(){
        visible.value = true
    }
    function hide(){
        visible.value = true
    }
    return {visible,show,hide}
}
export default useVisible

