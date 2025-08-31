import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from './views/Home.vue'
import LivrosList from './views/LivrosList.vue'
import LivrosForm from './views/LivrosForm.vue'
import AutoresList from './views/AutoresList.vue'
import AutoresForm from './views/AutoresForm.vue'
import AssuntosList from './views/AssuntosList.vue'
import AssuntosForm from './views/AssuntosForm.vue'
import Relatorios from './views/Relatorios.vue'

Vue.use(VueRouter)

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/livros', name: 'LivrosList', component: LivrosList },
  { path: '/livros/create', name: 'LivrosCreate', component: LivrosForm },
  { path: '/livros/:id/edit', name: 'LivrosEdit', component: LivrosForm },
  { path: '/autores', name: 'AutoresList', component: AutoresList },
  { path: '/autores/create', name: 'AutoresCreate', component: AutoresForm },
  { path: '/autores/:id/edit', name: 'AutoresEdit', component: AutoresForm },
  { path: '/assuntos', name: 'AssuntosList', component: AssuntosList },
  { path: '/assuntos/create', name: 'AssuntosCreate', component: AssuntosForm },
  { path: '/assuntos/:id/edit', name: 'AssuntosEdit', component: AssuntosForm },
  { path: '/relatorios', name: 'Relatorios', component: Relatorios }
]

export default new VueRouter({
  mode:'history',
  routes
})
