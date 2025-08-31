<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Lista de Assuntos</h2>
      <router-link to="/assuntos/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Assunto
      </router-link>
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
        orderBy: 'codAs',
        sortedBy: 'desc'
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
        this.$swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Não foi possível carregar a lista de assuntos.'
        })
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
    
    async confirmarExclusao(assunto) {
      const result = await this.$swal.fire({
        title: 'Confirmar exclusão',
        text: `Tem certeza que deseja excluir o assunto: ${assunto.descricao}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
      })
      
      if (result.isConfirmed) {
        this.excluirAssunto(assunto)
      }
    },
    
    async excluirAssunto(assunto) {
      try {
        await api.assuntos.delete(assunto.id)
        this.$swal.fire({
          icon: 'success',
          title: 'Sucesso!',
          text: 'Assunto excluído com sucesso!',
          timer: 2000,
          showConfirmButton: false
        })
        await this.carregarAssuntos()
        
      } catch (error) {
        console.error('Erro ao excluir assunto:', error)
        
        let errorMessage = 'Não foi possível excluir o assunto.'
        
        // Se há erros específicos
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors
          const errorList = Object.values(errors).flat()
          errorMessage = errorList.join('<br>')
        } else if (error.response?.data?.message) {
          errorMessage = error.response.data.message
        }
        
        this.$swal.fire({
          icon: 'error',
          title: 'Erro!',
          html: errorMessage
        })
      }
    }
  }
}
</script>
