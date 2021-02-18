import app from  './app'
import {link} from '/@/utils'
//跳转
app.directive('redirect', {
    created(el,binding) {
        el.onclick  = function(){
            link(binding.value)
        }
    }
})
