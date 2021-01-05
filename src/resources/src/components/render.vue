<script>
    import {defineComponent, computed, reactive, h, resolveComponent, inject} from 'vue'
    import { store } from '/@/store'
    export default defineComponent({
        name: "EadminRender",
        props: {
            data:{
                type: [String, Number,Array,Object],
                default: '',
            }
        },
        render(){
           return this.render
        },
        setup(props){
            const state = inject(store)
            const modelValue = state.proxyData
            const renderComponent = (data)=>{
                let expression,children  = {},renderArr = [],name
                //属性绑定
                for(let bindAttr in data.bindAttribute){
                    expression = 'data.attribute[bindAttr] = modelValue.' + data.bindAttribute[bindAttr]
                    eval(expression)
                }
                //双向绑定值
                if(data.bindAttribute && data.bindAttribute.modelValue){
                    const field = data.bindAttribute.modelValue
                    expression = 'data.attribute.modelValue = modelValue.'+field
                    eval(expression)
                    data.attribute['onUpdate:modelValue'] = value => {
                        expression = 'modelValue.'+field + ' = value'
                        eval(expression)
                    }
                }
                //插槽名称对应内容
                for(let slot in data.content){
                    renderArr[slot] = []
                    data.content[slot].forEach(item=>{
                        if(typeof(item.where) == 'object' && (item.where.AND.length > 0 || item.where.OR.length > 0)){
                            //条件if渲染实现
                            let expression = whereCompile(item.where.AND,item.where.OR)
                            if(typeof(item) == 'object'){
                                expression = expression + ' ? renderArr[slot].push(renderComponent(item)) : null'
                            }else{
                                expression = expression + ' ? renderArr[slot].push(item) : null'
                            }
                            eval(expression)
                        }else{
                            if(typeof(item) == 'object'){
                                renderArr[slot].push(renderComponent(item))
                            }else{
                                renderArr[slot].push( h({
                                    setup(){
                                        return {
                                            ...modelValue
                                        }
                                    },
                                    template:item
                                }))
                            }
                        }
                    })
                    children[slot] = (props) => renderArr[slot]
                }
                name = resolveComponent(data.name)
                if(data.map.bindName){
                    let field = data.map.bindName
                    return modelValue[field].map(item=>{
                        for(let attr in data.map.attribute){
                            data.attribute[attr] = item[data.map.attribute[attr]]
                        }
                        return h(name, data.attribute, children)
                    })
                }else{
                    return h(name, data.attribute, children)
                }
            }
            /**
             * 合并where
             * @param whereAnd
             * @param whereOr
             * @returns {string}
             */
            function whereCompile(whereAnd,whereOr) {
                let expressionStr = parseWhere(whereAnd,'AND')
                let expressionOr = parseWhere(whereOr,'OR')
                if(expressionStr && expressionOr){
                    expressionStr += ' || ' + expressionOr
                }else if(expressionOr){
                    expressionStr = expressionOr
                }
                return expressionStr
            }
            /**
             * 解析where
             * @param wheres
             * @param op AND | OR
             * @returns {string}
             */
            function parseWhere(wheres,op) {
                let evals = []
                let expression=''
                wheres.forEach((where,index)=>{
                    if(where.where){
                        let expressionStr = whereCompile(where.where.AND,where.where.OR)
                        evals.push("("+expressionStr+")")
                    }else {
                        let val  = eval('modelValue.'+where.field)
                        evals.push("'"+val+"' " + where.op+ ' '+"'"+where.condition+"'")
                    }
                })
                if(op == 'AND'){
                    op = '&&'
                }else{
                    op = '||'
                }
                expression +=  evals.join(' '+op+' ')
                return expression
            }
            const render = computed(()=>{
               if(props.data){
                   const jsonRender = props.data
                   return renderComponent(jsonRender)
               }else{
                   return null
               }
            })
            return {
                render
            }
        },
    })
</script>
