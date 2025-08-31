import Vue from 'vue'
import App from './App.vue'
import router from './router'
import 'bootstrap/dist/css/bootstrap.min.css'
import { VueMaskDirective } from 'v-mask'

Vue.config.productionTip = false

// Registrar v-mask para máscaras
Vue.directive('mask', VueMaskDirective)

// Registrar filtros globais
Vue.filter('currency', function (value) {
  if (!value) return ''
  return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',')
})

Vue.filter('formatDate', function (value) {
  if (!value) return ''
  const date = new Date(value)
  return date.toLocaleDateString('pt-BR')
})

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
