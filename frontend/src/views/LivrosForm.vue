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
                  <input type="number" id="anoPublicacao" v-model="form.anoPublicacao" class="form-control" required>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="valor" class="form-label">Valor *</label>
                <div class="input-group">
                  <span class="input-group-text">R$</span>
                  <input 
                    type="text" 
                    id="valor" 
                    v-model="form.valorFormatado" 
                    v-mask="'###.###.###,##'" 
                    class="form-control" 
                    placeholder="0,00"
                    required
                    @input="atualizarValor"
                  >
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="autores" class="form-label">Autores *</label>
                  <select multiple id="autores" v-model="form.autores" class="form-select" size="5" required>
                    <option v-for="autor in autores" :key="autor.id" :value="autor.id">{{ autor.nome }}</option>
                  </select>
                  <div class="form-text">Segure Ctrl para selecionar múltiplos autores</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="assuntos" class="form-label">Assuntos *</label>
                  <select multiple id="assuntos" v-model="form.assuntos" class="form-select" size="5" required>
                    <option v-for="assunto in assuntos" :key="assunto.id" :value="assunto.id">{{ assunto.descricao }}</option>
                  </select>
                  <div class="form-text">Segure Ctrl para selecionar múltiplos assuntos</div>
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
        valorFormatado: '',
        autores: [],
        assuntos: []
      },
      autores: [],
      assuntos: [],
      loading: false,
      isEditing: false
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
          api.autores.getAll(),
          api.assuntos.getAll()
        ])
        this.autores = autoresResponse.data.data.data
        this.assuntos = assuntosResponse.data.data.data
      } catch (error) {
        console.error('Erro ao carregar dados:', error)
        alert('Erro ao carregar dados')
      }
    },
    async carregarLivro() {
      try {
        const response = await api.livros.getById(this.$route.params.id, 'autores,assuntos')
        const livro = response.data.data
        
        this.form = {
          titulo: livro.titulo,
          editora: livro.editora,
          edicao: livro.edicao,
          anoPublicacao: livro.ano_publicacao,
          valor: parseFloat(livro.valor),
          valorFormatado: this.formatarValorParaInput(livro.valor),
          autores: livro.autores && livro.autores.data ? livro.autores.data.map(autor => autor.id) : [],
          assuntos: livro.assuntos && livro.assuntos.data ? livro.assuntos.data.map(assunto => assunto.id) : []
        }
      } catch (error) {
        console.error('Erro ao carregar livro:', error)
        alert('Erro ao carregar livro')
        this.$router.push('/livros')
      }
    },
    async salvarLivro() {
      try {
        this.loading = true
        const data = { 
          ...this.form, 
          valor: this.form.valor,
          ano_publicacao: this.form.anoPublicacao
        }
        
        // Remover campos que não são do backend
        delete data.valorFormatado
        delete data.anoPublicacao
        
        if (this.isEditing) {
          await api.livros.update(this.$route.params.id, data)
          alert('Livro atualizado com sucesso!')
        } else {
          await api.livros.create(data)
          alert('Livro criado com sucesso!')
        }
        this.$router.push('/livros')
      } catch (error) {
        console.error('Erro ao salvar livro:', error)
        alert('Erro ao salvar livro')
      } finally {
        this.loading = false
      }
    },
    
    atualizarValor() {
      // Converter valor formatado para número
      if (this.form.valorFormatado) {
        const valor = this.form.valorFormatado.replace(/[^\d,]/g, '').replace(',', '.')
        this.form.valor = parseFloat(valor) || 0
      } else {
        this.form.valor = 0
      }
    },
    
    formatarValorParaInput(valor) {
      return parseFloat(valor).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    }
  }
}
</script>
