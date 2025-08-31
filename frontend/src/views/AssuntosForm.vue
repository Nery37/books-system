<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h4 class="mb-0">{{ isEditing ? "Editar Assunto" : "Novo Assunto" }}</h4>
          </div>
          <div class="card-body">
            <form @submit.prevent="salvarAssunto">
              <div class="mb-3">
                <label for="descricao" class="form-label">Descrição *</label>
                <input 
                  type="text" 
                  id="descricao" 
                  v-model="form.descricao" 
                  class="form-control"
                  required
                  maxlength="20"
                >
              </div>

              <div class="d-flex justify-content-between">
                <router-link to="/assuntos" class="btn btn-secondary">
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
  name: 'AssuntosForm',
  data() {
    return {
      form: {
        descricao: ''
      },
      loading: false,
      isEditing: false
    }
  },
  async mounted() {
    if (this.$route.params.id) {
      this.isEditing = true
      await this.carregarAssunto()
    }
  },
  methods: {
    async carregarAssunto() {
      try {
        const response = await api.assuntos.getById(this.$route.params.id)
        const assunto = response.data.data.data
        
        this.form = {
          descricao: assunto.descricao
        }
        
      } catch (error) {
        console.error('Erro ao carregar assunto:', error)
        alert('Erro ao carregar assunto')
        this.$router.push('/assuntos')
      }
    },
    
    async salvarAssunto() {
      try {
        this.loading = true
        
        if (this.isEditing) {
          await api.assuntos.update(this.$route.params.id, this.form)
          alert('Assunto atualizado com sucesso!')
        } else {
          await api.assuntos.create(this.form)
          alert('Assunto criado com sucesso!')
        }
        
        this.$router.push('/assuntos')
        
      } catch (error) {
        console.error('Erro ao salvar assunto:', error)
        alert('Erro ao salvar assunto')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
