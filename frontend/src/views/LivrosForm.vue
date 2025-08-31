<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h4>{{ isEditing ? "Editar Livro" : "Novo Livro" }}</h4>
          </div>
          <div class="card-body">
            <form @submit.prevent="salvarLivro">
              <div class="mb-3">
                <label for="titulo" class="form-label">Título *</label>
                <input type="text" id="titulo" v-model="form.titulo" class="form-control" required maxlength="40">
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="editora" class="form-label">Editora *</label>
                  <input type="text" id="editora" v-model="form.editora" class="form-control" required maxlength="40">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="edicao" class="form-label">Edição *</label>
                  <input type="number" id="edicao" v-model="form.edicao" class="form-control" required min="1">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="anoPublicacao" class="form-label">Ano *</label>
                  <input 
                    type="number" 
                    id="anoPublicacao" 
                    v-model="form.anoPublicacao" 
                    class="form-control" 
                    required 
                    min="1000" 
                    max="9999"
                    @input="limitarAno"
                  >
                </div>
              </div>
              
              <div class="mb-3">
                <label for="valor" class="form-label">Valor *</label>
                <div class="input-group">
                  <span class="input-group-text">R$</span>
                  <input 
                    type="text" 
                    id="valor" 
                    v-model="form.valor" 
                    v-money="moneyConfig"
                    class="form-control" 
                    placeholder="0,00"
                    required
                  >
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Autores *</label>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Selecione os autores do livro</small>
                    <div>
                      <button type="button" class="btn btn-sm btn-outline-primary me-1" @click="selecionarTodosAutores">
                        Todos
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" @click="limparAutores">
                        Limpar
                      </button>
                    </div>
                  </div>
                  <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                    <div v-if="autores.length === 0" class="text-muted">Carregando autores...</div>
                    <div v-for="autor in autores" :key="autor.id" class="form-check mb-2">
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        :id="'autor-' + autor.id"
                        :value="autor.id" 
                        v-model="form.autores"
                      >
                      <label class="form-check-label" :for="'autor-' + autor.id">
                        {{ autor.nome }}
                      </label>
                    </div>
                  </div>
                  <div class="form-text">
                    <i class="bi bi-info-circle"></i> 
                    Selecionados: {{ form.autores.length }} autor(es)
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Assuntos *</label>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Selecione os assuntos do livro</small>
                    <div>
                      <button type="button" class="btn btn-sm btn-outline-primary me-1" @click="selecionarTodosAssuntos">
                        Todos
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" @click="limparAssuntos">
                        Limpar
                      </button>
                    </div>
                  </div>
                  <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                    <div v-if="assuntos.length === 0" class="text-muted">Carregando assuntos...</div>
                    <div v-for="assunto in assuntos" :key="assunto.id" class="form-check mb-2">
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        :id="'assunto-' + assunto.id"
                        :value="assunto.id" 
                        v-model="form.assuntos"
                      >
                      <label class="form-check-label" :for="'assunto-' + assunto.id">
                        {{ assunto.descricao }}
                      </label>
                    </div>
                  </div>
                  <div class="form-text">
                    <i class="bi bi-info-circle"></i> 
                    Selecionados: {{ form.assuntos.length }} assunto(s)
                  </div>
                </div>
              </div>
              
              <div class="d-flex justify-content-between">
                <router-link to="/livros" class="btn btn-secondary">Voltar</router-link>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  {{ isEditing ? "Atualizar" : "Salvar" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'LivrosForm',
  data() {
    return {
      form: {
        titulo: '',
        editora: '',
        edicao: 1,
        anoPublicacao: new Date().getFullYear(),
        valor: 0,
        autores: [],
        assuntos: []
      },
      autores: [],
      assuntos: [],
      loading: false,
      isEditing: false,
      moneyConfig: {
        decimal: ',',
        thousands: '.',
        prefix: '',
        suffix: '',
        precision: 2,
        masked: false
      }
    }
  },
  async mounted() {
    await this.carregarDados()
    if (this.$route.params.id) {
      this.isEditing = true
      await this.carregarLivro()
    }
  },
  methods: {
    async carregarDados() {
      try {
        const [autoresResponse, assuntosResponse] = await Promise.all([
          api.autores.getAll({ orderBy: 'CodAu', sortedBy: 'asc' }),
          api.assuntos.getAll({ orderBy: 'codAs', sortedBy: 'asc' })
        ])
        this.autores = autoresResponse.data.data.data
        this.assuntos = assuntosResponse.data.data.data
      } catch (error) {
        console.error('Erro ao carregar dados:', error)
        this.$swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Não foi possível carregar autores e assuntos.'
        })
      }
    },
    async carregarLivro() {
      try {
        const response = await api.livros.getById(this.$route.params.id, 'autores,assuntos')
        const livro = response.data.data.data
        
        Object.assign(this.form, {
          titulo: livro.titulo,
          editora: livro.editora,
          edicao: livro.edicao,
          anoPublicacao: livro.anoPublicacao,
          valor: livro.valor,
          autores: livro.autores?.data?.map(autor => parseInt(autor.id)) || [],
          assuntos: livro.assuntos?.data?.map(assunto => parseInt(assunto.id)) || []
        })
        
      } catch (error) {
        console.error('Erro ao carregar livro:', error)
        this.$swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Não foi possível carregar os dados do livro.'
        })
        this.$router.push('/livros')
      }
    },
    async salvarLivro() {
      try {
        // Validações customizadas
        if (this.form.autores.length === 0) {
          this.$swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: 'Selecione pelo menos um autor'
          })
          return
        }
        
        if (this.form.assuntos.length === 0) {
          this.$swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: 'Selecione pelo menos um assunto'
          })
          return
        }
        
        this.loading = true
        
        // Converter valor formatado para número
        let valorNumerico = this.form.valor
        if (typeof valorNumerico === 'string') {
          valorNumerico = parseFloat(valorNumerico.replace(/\./g, '').replace(',', '.')) || 0
        }
        
        const data = { 
          Titulo: this.form.titulo,
          Editora: this.form.editora,
          Edicao: this.form.edicao,
          AnoPublicacao: this.form.anoPublicacao.toString(),
          Valor: valorNumerico,
          autores: this.form.autores,
          assuntos: this.form.assuntos
        }
        
        console.log('Dados a serem enviados:', data)
        
        if (this.isEditing) {
          await api.livros.update(this.$route.params.id, data)
          this.$swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Livro atualizado com sucesso!',
            timer: 2000,
            showConfirmButton: false
          })
        } else {
          await api.livros.create(data)
          this.$swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Livro criado com sucesso!',
            timer: 2000,
            showConfirmButton: false
          })
        }
        this.$router.push('/livros')
      } catch (error) {
        console.error('Erro ao salvar livro:', error)
        console.error('Detalhes do erro:', error.response?.data)
        
        let errorMessage = 'Não foi possível salvar o livro.'
        
        // Se há erros de validação específicos
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
      } finally {
        this.loading = false
      }
    },
    
    // Métodos auxiliares para seleção
    selecionarTodosAutores() {
      this.form.autores = this.autores.map(autor => autor.id)
    },
    
    limparAutores() {
      this.form.autores = []
    },
    
    selecionarTodosAssuntos() {
      this.form.assuntos = this.assuntos.map(assunto => assunto.id)
    },
    
    limparAssuntos() {
      this.form.assuntos = []
    },

    // Método para limitar ano a 4 dígitos
    limitarAno(event) {
      let valor = event.target.value
      if (valor.length > 4) {
        this.form.anoPublicacao = parseInt(valor.toString().slice(0, 4))
      }
    }
  }
}
</script>
