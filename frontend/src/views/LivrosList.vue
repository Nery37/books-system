<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Lista de Livros</h2>
      <router-link to="/livros/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Livro
      </router-link>
    </div>



    <!-- Loading -->
    <div v-if="loading" class="text-center my-4">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>

    <!-- Tabela de Livros -->
    <div v-else class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Editora</th>
                <th>Edição</th>
                <th>Ano</th>
                <th>Valor</th>
                <th>Autores</th>
                <th>Assuntos</th>
                <th width="120">Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="livros.length === 0">
                <td colspan="9" class="text-center text-muted py-4">
                  Nenhum livro encontrado
                </td>
              </tr>
              <tr v-for="livro in livros" :key="livro.id">
                <td>{{ livro.id }}</td>
                <td>{{ livro.titulo }}</td>
                <td>{{ livro.editora }}</td>
                <td>{{ livro.edicao }}</td>
                <td>{{ livro.anoPublicacao }}</td>
                <td>{{ formatarMoeda(livro.valor) }}</td>
                <td>
                  <span 
                    v-for="autor in livro.autores" 
                    :key="autor.id"
                    class="badge bg-secondary me-1"
                  >
                    {{ autor.nome }}
                  </span>
                </td>
                <td>
                  <span 
                    v-for="assunto in livro.assuntos" 
                    :key="assunto.id"
                    class="badge bg-info me-1"
                  >
                    {{ assunto.descricao }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <router-link 
                      :to="`/livros/${livro.id}/edit`" 
                      class="btn btn-sm btn-outline-primary"
                      title="Editar"
                    >
                      <i class="bi bi-pencil"></i>
                    </router-link>
                    <button 
                      @click="confirmarExclusao(livro)" 
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
    <div v-if="pagination.total > pagination.per_page" class="d-flex justify-content-center mt-4">
      <nav aria-label="Navegação de páginas">
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
            <a class="page-link" href="#" @click.prevent="irParaPagina(pagination.current_page - 1)">
              <i class="bi bi-chevron-left"></i>
            </a>
          </li>
          
          <li 
            v-for="pagina in paginasVisiveis" 
            :key="pagina" 
            class="page-item" 
            :class="{ active: pagina === pagination.current_page }"
          >
            <a class="page-link" href="#" @click.prevent="irParaPagina(pagina)">{{ pagina }}</a>
          </li>
          
          <li class="page-item" :class="{ disabled: pagination.current_page === pagination.total_pages }">
            <a class="page-link" href="#" @click.prevent="irParaPagina(pagination.current_page + 1)">
              <i class="bi bi-chevron-right"></i>
            </a>
          </li>
        </ul>
      </nav>
    </div>
    
    <!-- Info da paginação -->
    <div v-if="pagination.total > 0" class="text-center text-muted mt-2">
      Mostrando {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} até 
      {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} de 
      {{ pagination.total }} resultados
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'LivrosList',
  data() {
    return {
      livros: [],
      autores: [],
      assuntos: [],
      loading: true,
      livroParaExcluir: null,
      filtros: {
        titulo: '',
        autor: '',
        assunto: ''
      },
      pagination: {
        current_page: 1,
        per_page: 15,
        total: 0,
        total_pages: 0
      }
    }
  },
  async mounted() {
    await this.carregarDados()
  },
  methods: {
    async carregarDados() {
      try {
        this.loading = true
        
        const [autoresResponse, assuntosResponse] = await Promise.all([
          api.autores.getAll(),
          api.assuntos.getAll()
        ])
        
        this.autores = autoresResponse.data.data.data
        this.assuntos = assuntosResponse.data.data.data
        
        await this.buscarLivros()
        
      } catch (error) {
        console.error('Erro ao carregar dados:', error)
        this.$swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Não foi possível carregar os dados iniciais.'
        })
      } finally {
        this.loading = false
      }
    },
    
    async buscarLivros(pagina = 1) {
      try {
        const params = {
          include: 'autores,assuntos',
          page: pagina,
          orderBy: 'Codl',
          sortedBy: 'desc'
        }
        
        if (this.filtros.titulo) {
          params.titulo = this.filtros.titulo
        }
        
        if (this.filtros.autor) {
          params.autor_id = this.filtros.autor
        }
        
        if (this.filtros.assunto) {
          params.assunto_id = this.filtros.assunto
        }
        
        const response = await api.livros.getAll(params)
        const dados = response.data.data
        
        // Processar os dados para extrair autores e assuntos da estrutura correta
        this.livros = dados.data.map(livro => ({
          ...livro,
          autores: livro.autores && livro.autores.data ? livro.autores.data : [],
          assuntos: livro.assuntos && livro.assuntos.data ? livro.assuntos.data : []
        }))
        
        // Atualizar informações de paginação
        if (dados.meta && dados.meta.pagination) {
          this.pagination = dados.meta.pagination
        }
        
      } catch (error) {
        console.error('Erro ao buscar livros:', error)
        this.$swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Não foi possível buscar os livros.'
        })
        this.livros = []
      }
    },
    
    async confirmarExclusao(livro) {
      const result = await this.$swal.fire({
        title: 'Confirmar exclusão',
        text: `Tem certeza que deseja excluir o livro: ${livro.titulo}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
      })
      
      if (result.isConfirmed) {
        this.excluirLivro(livro)
      }
    },
    
    async excluirLivro(livro) {
      try {
        await api.livros.delete(livro.id)
        this.$swal.fire({
          icon: 'success',
          title: 'Sucesso!',
          text: 'Livro excluído com sucesso!',
          timer: 2000,
          showConfirmButton: false
        })
        await this.buscarLivros()
        
      } catch (error) {
        console.error('Erro ao excluir livro:', error)
        
        let errorMessage = 'Não foi possível excluir o livro.'
        
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
    },
    
    formatarMoeda(valor) {
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(valor)
    },
    
    irParaPagina(pagina) {
      if (pagina >= 1 && pagina <= this.pagination.total_pages && pagina !== this.pagination.current_page) {
        this.buscarLivros(pagina)
      }
    },
    
    aplicarFiltros() {
      this.buscarLivros(1) // Sempre volta para a primeira página ao filtrar
    }
  },
  
  computed: {
    paginasVisiveis() {
      const total = this.pagination.total_pages
      const atual = this.pagination.current_page
      const paginas = []
      
      if (total <= 7) {
        // Mostrar todas as páginas se forem 7 ou menos
        for (let i = 1; i <= total; i++) {
          paginas.push(i)
        }
      } else {
        // Lógica para mostrar páginas com ... quando necessário
        if (atual <= 4) {
          for (let i = 1; i <= 5; i++) {
            paginas.push(i)
          }
          paginas.push('...')
          paginas.push(total)
        } else if (atual >= total - 3) {
          paginas.push(1)
          paginas.push('...')
          for (let i = total - 4; i <= total; i++) {
            paginas.push(i)
          }
        } else {
          paginas.push(1)
          paginas.push('...')
          for (let i = atual - 1; i <= atual + 1; i++) {
            paginas.push(i)
          }
          paginas.push('...')
          paginas.push(total)
        }
      }
      
      return paginas.filter(p => p !== '...' || paginas.indexOf(p) === paginas.lastIndexOf(p))
    }
  }
}
</script>
