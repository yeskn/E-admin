import {useRoute} from 'vue-router'
import md5 from 'js-md5'
export const getSource = (source, type) => {
  const regex = new RegExp(`<${type}[^>]*>`)
  let openingTag = source.match(regex)

  if (!openingTag) {
    return ''
  } else {
    openingTag = openingTag[0]
  }

  return source.slice(source.indexOf(openingTag) + openingTag.length, source.lastIndexOf(`</${type}>`))
}

export const splitCode = (codeStr) => {

  const script = getSource(codeStr, 'script').replace(/export default/, 'return ')
  const css = getSource(codeStr, 'style')
  const template = getSource(codeStr, 'template')
  if (css) {
    const style = document.createElement('style')
    style.type = 'text/css'
    // style.id = this.id;
    style.innerHTML = css
    const route = useRoute()
    style.setAttribute('data-key','eadmin_style_' + md5(route.path))
    document.getElementsByTagName('head')[0].appendChild(style)
  }
  return {
    ...new Function(script)(), template
  }
}
