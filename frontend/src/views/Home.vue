<template>
  <div class="container mt-4">
    <div class="jumbotron bg-primary text-white rounded p-4 mb-4">
      <h1 class="display-4">Sistema de Gerenciamento de Livros</h1>
      <p class="lead">Gerencie livros, autores e assuntos de forma simples e eficiente.</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center my-4">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>

    <!-- Estatísticas -->
    <div v-else class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <i class="bi bi-book-fill text-primary" style="font-size: 2rem;"></i>
            <h5 class="card-title mt-2">{{ estatisticas.totalLivros }}</h5>
            <p class="card-text">Total de Livros</p>
            <router-link to="/livros" class="btn btn-outline-primary btn-sm">
              Ver Livros
            </router-link>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <i class="bi bi-person-fill text-success" style="font-size: 2rem;"></i>
            <h5 class="card-title mt-2">{{ estatisticas.totalAutores }}</h5>
            <p class="card-text">Total de Autores</p>
            <router-link to="/autores" class="btn btn-outline-success btn-sm">
              Ver Autores
            </router-link>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <i class="bi bi-tags-fill text-info" style="font-size: 2rem;"></i>
            <h5 class="card-title mt-2">{{ estatisticas.totalAssuntos }}</h5>
            <p class="card-text">Total de Assuntos</p>
            <router-link to="/assuntos" class="btn btn-outline-info btn-sm">
              Ver Assuntos
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Ações Rápidas</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4 mb-2">
                <router-link to="/livros/create" class="btn btn-primary w-100">
                  <i class="bi bi-plus-circle me-2"></i>
                  Novo Livro
                </router-link>
              </div>
              <div class="col-md-4 mb-2">
                <router-link to="/autores/create" class="btn btn-success w-100">
                  <i class="bi bi-plus-circle me-2"></i>
                  Novo Autor
                </router-link>
              </div>
              <div class="col-md-4 mb-2">
                <router-link to="/assuntos/create" class="btn btn-info w-100">
                  <i class="bi bi-plus-circle me-2"></i>
                  Novo Assunto
                </router-link>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-6 mb-2">
                <router-link to="/relatorios" class="btn btn-warning w-100">
                  <i class="bi bi-file-earmark-text me-2"></i>
                  Gerar Relatórios
                </router-link>
              </div>
              <div class="col-md-6 mb-2">
                <router-link to="/livros" class="btn btn-secondary w-100">
                  <i class="bi bi-list me-2"></i>
                  Visualizar Todos os Livros
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Livros Recentes -->
    <div v-if="livrosRecentes.length > 0" class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Livros Recentes</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Título</th>
                    <th>Autor(es)</th>
                    <th>Editora</th>
                    <th>Valor</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="livro in livrosRecentes" :key="livro.id">
                    <td>{{ livro.titulo }}</td>
                    <td>
                      <span 
                        v-for="autor in livro.autores" 
                        :key="autor.id"
                        class="badge bg-secondary me-1"
                      >
                        {{ autor.nome }}
                      </span>
                    </td>
                    <td>{{ livro.editora }}</td>
                    <td>{{ formatarMoeda(livro.valor) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'Home',
  data() {
    return {
      loading: true,
      estatisticas: {
        totalLivros: 0,
        totalAutores: 0,
        totalAssuntos: 0
      },
      livrosRecentes: []
    }
  },
  async mounted() {
    await this.carregarDados()
  },
  methods: {
    async carregarDados() {
      try {
        this.loading = true
        
        const [livrosResponse, autoresResponse, assuntosResponse] = await Promise.all([
          api.livros.getAll(),
          api.autores.getAll(),
          api.assuntos.getAll()
        ])

        this.estatisticas = {
          totalLivros: livrosResponse.data.data.meta.pagination.total,
          totalAutores: autoresResponse.data.data.meta.pagination.total,
          totalAssuntos: assuntosResponse.data.data.meta.pagination.total
        }
        
        // Pegar os 5 primeiros livros como "recentes"
        this.livrosRecentes = (livrosResponse.data.data.data || []).slice(0, 5)
        
      } catch (error) {
        console.error('Erro ao carregar dados:', error)
        alert('Erro ao carregar dados do dashboard')
      } finally {
        this.loading = false
      }
    },
    
    formatarMoeda(valor) {
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(valor)
    }
  }
}
</script>
