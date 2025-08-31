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
        this.$swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Não foi possível carregar os dados do autor.'
        })
        this.$router.push('/autores')
      }
    },
    
    async salvarAutor() {
      try {
        this.loading = true
        
        // Converter o formato para o que o backend espera
        const dados = {
          Nome: this.form.nome
        }
        
        if (this.isEditing) {
          await api.autores.update(this.$route.params.id, dados)
          this.$swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Autor atualizado com sucesso!',
            timer: 2000,
            showConfirmButton: false
          })
        } else {
          await api.autores.create(dados)
          this.$swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Autor criado com sucesso!',
            timer: 2000,
            showConfirmButton: false
          })
        }
        
        this.$router.push('/autores')
        
      } catch (error) {
        console.error('Erro ao salvar autor:', error)
        
        let errorMessage = 'Não foi possível salvar o autor.'
        
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
    }
  }
}
</script>
