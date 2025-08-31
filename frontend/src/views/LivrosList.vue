<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Lista de Livros</h2>
      <router-link to="/livros/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Livro
      </router-link>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <label for="filtroTitulo" class="form-label">Buscar por Título:</label>
            <input 
              type="text" 
              id="filtroTitulo" 
              v-model="filtros.titulo" 
              class="form-control" 
              placeholder="Digite o título do livro"
              @input="aplicarFiltros"
            >
          </div>
          <div class="col-md-4">
            <label for="filtroAutor" class="form-label">Filtrar por Autor:</label>
            <select v-model="filtros.autor" class="form-select" @change="aplicarFiltros">
              <option value="">Todos os autores</option>
              <option v-for="autor in autores" :key="autor.id" :value="autor.id">
                {{ autor.nome }}
              </option>
            </select>
          </div>
          <div class="col-md-4">
            <label for="filtroAssunto" class="form-label">Filtrar por Assunto:</label>
            <select v-model="filtros.assunto" class="form-select" @change="aplicarFiltros">
              <option value="">Todos os assuntos</option>
              <option v-for="assunto in assuntos" :key="assunto.id" :value="assunto.id">
                {{ assunto.descricao }}
              </option>
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
        alert('Erro ao carregar dados')
      } finally {
        this.loading = false
      }
    },
    
    async buscarLivros(pagina = 1) {
      try {
        const params = {
          include: 'autores,assuntos',
          page: pagina
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
        alert('Erro ao buscar livros')
        this.livros = []
      }
    },
    
    confirmarExclusao(livro) {
      if (confirm(`Tem certeza que deseja excluir o livro: ${livro.titulo}?`)) {
        this.excluirLivro(livro)
      }
    },
    
    async excluirLivro(livro) {
      try {
        await api.livros.delete(livro.id)
        alert('Livro excluído com sucesso!')
        await this.buscarLivros()
        
      } catch (error) {
        console.error('Erro ao excluir livro:', error)
        alert('Erro ao excluir livro')
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
