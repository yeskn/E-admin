/**
 * Created by PanJiaChen on 16/11/18.
 */

/**
 * @param {string} path
 * @returns {Boolean}
 */
import router from '/@/router'
export function isExternal(path) {
  return /^(https?:|mailto:|tel:)/.test(path)
}
export function link(url){
  if (isExternal(url)) {
    window.open(url)
  }else{
    router.push(url)
  }
}
export function isJSON(str) {
  if (typeof str == 'string') {
    try {
      var obj=JSON.parse(str);
      if(typeof obj == 'object' && obj ){
        return true;
      }else{
        return false;
      }

    } catch(e) {
      console.log('error：'+str+'!!!'+e);
      return false;
    }
  }
}

