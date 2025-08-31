<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h4 class="mb-0">{{ isEditing ? "Editar Autor" : "Novo Autor" }}</h4>
          </div>
          <div class="card-body">
            <form @submit.prevent="salvarAutor">
              <div class="mb-3">
                <label for="nome" class="form-label">Nome *</label>
                <input 
                  type="text" 
                  id="nome" 
                  v-model="form.nome" 
                  class="form-control"
                  required
                  maxlength="40"
                >
              </div>

              <div class="d-flex justify-content-between">
                <router-link to="/autores" class="btn btn-secondary">
                  <i class="bi bi-arrow-left"></i> Voltar
                </router-link>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-check"></i>
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
  name: 'AutoresForm',
  data() {
    return {
      form: {
        nome: ''
      },
      loading: false,
      isEditing: false
    }
  },
  async mounted() {
    if (this.$route.params.id) {
      this.isEditing = true
      await this.carregarAutor()
    }
  },
  methods: {
    async carregarAutor() {
      try {
        const response = await api.autores.getById(this.$route.params.id)
        const autor = response.data.data.data
        
        this.form = {
          nome: autor.nome
        }
        
      } catch (error) {
        console.error('Erro ao carregar autor:', error)
        alert('Erro ao carregar autor')
        this.$router.push('/autores')
      }
    },
    
    async salvarAutor() {
      try {
        this.loading = true
        
        if (this.isEditing) {
          await api.autores.update(this.$route.params.id, this.form)
          alert('Autor atualizado com sucesso!')
        } else {
          await api.autores.create(this.form)
          alert('Autor criado com sucesso!')
        }
        
        this.$router.push('/autores')
        
      } catch (error) {
        console.error('Erro ao salvar autor:', error)
        alert('Erro ao salvar autor')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
