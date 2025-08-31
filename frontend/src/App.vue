<template>
  <div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
        <router-link class="navbar-brand" to="/">
          <i class="fas fa-book"></i>
          Sistema de Livros
        </router-link>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
             <router-link class="nav-link" to="/livros">
                <i class="fas fa-book"></i> Livros
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/autores">
                <i class="fas fa-user"></i> Autores
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/assuntos">
                <i class="fas fa-tags"></i> Assuntos
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/relatorios">
                <i class="fas fa-chart-bar"></i> Relatórios
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
.router-link-active {
  font-weight: bold;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
