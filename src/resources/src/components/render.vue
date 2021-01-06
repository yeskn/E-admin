<script>
    import {defineComponent, computed, toRaw, h, resolveComponent, inject, withCtx, createVNode} from 'vue'
    import {store} from '/@/store'
    import {compile} from '@vue/compiler-dom'

    export default defineComponent({

        name: "EadminRender",
        props: {
            data: {
                type: [String, Number, Array, Object],
                default: '',
            }
        },
        render() {
            return this.render
        },
        setup(props, ctx) {
            const state = inject(store)
            const modelValue = state.proxyData

            const renderComponent = (data, slotProps) => {
                let expression, children = {}, name,attribute

                //属性绑定
                for (let bindAttr in data.bindAttribute) {
                    expression = 'data.attribute[bindAttr] = modelValue.' + data.bindAttribute[bindAttr]
                    eval(expression)
                }

                //双向绑定值
                if (data.bindAttribute && data.bindAttribute.modelValue) {
                    let field = data.bindAttribute.modelValue
                    // 本次渲染是循环属性
                    if (slotProps && slotProps.row) {
                        data.attribute.modelValue = slotProps.row[field]
                        data.attribute['onUpdate:modelValue'] = value => {
                            slotProps.row[field] = value
                        }
                    } else {
                        expression = 'data.attribute.modelValue = modelValue.' + field
                        eval(expression)
                        data.attribute['onUpdate:modelValue'] = value => {
                            expression = 'modelValue.' + field + ' = value'
                            eval(expression)
                        }
                    }
                }
                //插槽名称对应内容
                for (let slot in data.content) {
                    children[slot] = (scope) => {
                        if (JSON.stringify(scope) === '{}') {
                            scope = slotProps
                        }
                        return userRender(data.content[slot], scope)
                    }
                }
                name = resolveComponent(data.name)
                attribute = {...data.attribute}
                //for 遍历中的 ElFormItem prop处理
                if(data.name == 'ElFormItem'){
                    if(slotProps && slotProps.propField){
                        attribute.prop = slotProps.propField + '.' + slotProps.$index+ '.' + attribute.prop
                    }
                }
                if (data.map.bindName) {
                    let field = data.map.bindName
                    return modelValue[field].map(item => {
                        for (let attr in data.map.attribute) {
                            data.attribute[attr] = item[data.map.attribute[attr]]
                        }
                        return h(name, attribute, children)
                    })
                } else {
                    return h(name, attribute, children)
                }
            }

            function userRender(slot, scope) {
                return slot.map(item => {
                    if (typeof (item.where) == 'object' && (item.where.AND.length > 0 || item.where.OR.length > 0)) {
                        // //条件if渲染实现
                        let expression = whereCompile(item.where.AND, item.where.OR)
                        if (typeof (item) == 'object') {
                            expression = expression + ' ? renderComponent(item) : null'
                        } else {
                            expression = expression + ' ? h({setup(){return {...modelValue}},template:item}) : null'
                        }
                        return eval(expression)
                    } else {
                        if (typeof (item) == 'object') {
                            return renderComponent(item, scope)
                        } else {
                            return h({
                                setup() {
                                    return {
                                        ...modelValue
                                    }
                                },
                                template: item
                            })
                        }
                    }
                })

            }

            /**
             * 合并where
             * @param whereAnd
             * @param whereOr
             * @returns {string}
             */
            function whereCompile(whereAnd, whereOr) {
                let expressionStr = parseWhere(whereAnd, 'AND')
                let expressionOr = parseWhere(whereOr, 'OR')
                if (expressionStr && expressionOr) {
                    expressionStr += ' || ' + expressionOr
                } else if (expressionOr) {
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
            function parseWhere(wheres, op) {
                let evals = []
                let expression = ''
                wheres.forEach((where, index) => {
                    if (where.where) {
                        let expressionStr = whereCompile(where.where.AND, where.where.OR)
                        evals.push("(" + expressionStr + ")")
                    } else {
                        let val = eval('modelValue.' + where.field)
                        evals.push("'" + val + "' " + where.op + ' ' + "'" + where.condition + "'")
                    }
                })
                if (op == 'AND') {
                    op = '&&'
                } else {
                    op = '||'
                }
                expression += evals.join(' ' + op + ' ')
                return expression
            }

            const render = computed(() => {
                if (props.data) {
                    const jsonRender = toRaw(props.data)
                    return renderComponent(jsonRender)
                } else {
                    return null
                }
            })
            return {
                render
            }
        },
    })
</script>
