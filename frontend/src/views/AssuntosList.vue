<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Lista de Assuntos</h2>
      <router-link to="/assuntos/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Assunto
      </router-link>
    </div>

    <!-- Filtros de Busca -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <label for="searchDescricao" class="form-label">Buscar por Descrição:</label>
            <input 
              id="searchDescricao"
              v-model="filtros.search" 
              @input="buscarAssuntos"
              type="text" 
              class="form-control" 
              placeholder="Digite a descrição do assunto..."
            >
          </div>
          <div class="col-md-3">
            <label for="orderBy" class="form-label">Ordenar por:</label>
            <select 
              id="orderBy"
              v-model="filtros.orderBy" 
              @change="buscarAssuntos"
              class="form-select"
            >
              <option value="">Selecione</option>
              <option value="Descricao">Descrição</option>
              <option value="CodAs">ID</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="sortedBy" class="form-label">Direção:</label>
            <select 
              id="sortedBy"
              v-model="filtros.sortedBy" 
              @change="buscarAssuntos"
              class="form-select"
            >
              <option value="asc">Crescente</option>
              <option value="desc">Decrescente</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center my-4">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>

    <!-- Tabela de Assuntos -->
    <div v-else class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th width="120">Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="assuntos.length === 0">
                <td colspan="3" class="text-center text-muted py-4">
                  Nenhum assunto encontrado
                </td>
              </tr>
              <tr v-for="assunto in assuntos" :key="assunto.id">
                <td>{{ assunto.id }}</td>
                <td>{{ assunto.descricao }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <router-link 
                      :to="`/assuntos/${assunto.id}/edit`" 
                      class="btn btn-sm btn-outline-primary"
                      title="Editar"
                    >
                      <i class="bi bi-pencil"></i>
                    </router-link>
                    <button 
                      @click="confirmarExclusao(assunto)" 
                      class="btn btn-sm btn-outline-danger"
                      title="Excluir"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Paginação -->
    <nav v-if="pagination.last_page > 1" aria-label="Navegação de páginas">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
          <button 
            class="page-link" 
            @click="irParaPagina(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
          >
            Anterior
          </button>
        </li>
        
        <li 
          v-for="pagina in paginasVisiveis" 
          :key="pagina" 
          class="page-item" 
          :class="{ active: pagina === pagination.current_page, disabled: pagina === '...' }"
        >
          <button 
            v-if="pagina !== '...'"
            class="page-link" 
            @click="irParaPagina(pagina)"
          >
            {{ pagina }}
          </button>
          <span v-else class="page-link">{{ pagina }}</span>
        </li>
        
        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
          <button 
            class="page-link" 
            @click="irParaPagina(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
          >
            Próximo
          </button>
        </li>
      </ul>
    </nav>

    <!-- Informações da Paginação -->
    <div v-if="pagination.total > 0" class="text-center text-muted mt-3">
      Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} assuntos
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'AssuntosList',
  data() {
    return {
      assuntos: [],
      loading: true,
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
        from: 0,
        to: 0
      },
      filtros: {
        search: '',
        orderBy: '',
        sortedBy: 'asc'
      }
    }
  },
  computed: {
    paginasVisiveis() {
      const current = this.pagination.current_page
      const last = this.pagination.last_page
      const delta = 2
      const range = []
      
      // Se há poucas páginas, mostra todas
      if (last <= 7) {
        for (let i = 1; i <= last; i++) {
          range.push(i)
        }
        return range
      }
      
      // Lógica para muitas páginas
      const left = current - delta
      const right = current + delta + 1
      
      for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= left && i < right)) {
          range.push(i)
        }
      }
      
      // Adiciona "..." onde necessário
      const result = []
      let prev = 0
      
      for (const i of range) {
        if (prev + 1 < i) {
          result.push('...')
        }
        result.push(i)
        prev = i
      }
      
      return result
    }
  },
  async mounted() {
    await this.carregarAssuntos()
  },
  methods: {
    async carregarAssuntos(pagina = 1) {
      try {
        this.loading = true
        const params = {
          page: pagina,
          ...this.filtros
        }
        
        const response = await api.assuntos.getAll(params)
        this.assuntos = response.data.data.data
        this.pagination = response.data.data.meta.pagination
        
      } catch (error) {
        console.error('Erro ao carregar assuntos:', error)
        alert('Erro ao carregar assuntos')
      } finally {
        this.loading = false
      }
    },

    async buscarAssuntos() {
      await this.carregarAssuntos(1)
    },

    async irParaPagina(pagina) {
      if (pagina >= 1 && pagina <= this.pagination.last_page && pagina !== this.pagination.current_page) {
        await this.carregarAssuntos(pagina)
      }
    },
    
    confirmarExclusao(assunto) {
      if (confirm(`Tem certeza que deseja excluir o assunto: ${assunto.descricao}?`)) {
        this.excluirAssunto(assunto)
      }
    },
    
    async excluirAssunto(assunto) {
      try {
        await api.assuntos.delete(assunto.id)
        alert('Assunto excluído com sucesso!')
        await this.carregarAssuntos()
        
      } catch (error) {
        console.error('Erro ao excluir assunto:', error)
        alert('Erro ao excluir assunto')
      }
    }
  }
}
</script>
