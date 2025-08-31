import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Interceptor para tratar erros
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.data) {
      console.error('API Error:', error.response.data)
    }
    return Promise.reject(error)
  }
)

export default {
  // Livros
  livros: {
    getAll: (params = {}) => api.get('/livros', { params }),
    getById: (id, include = '') => api.get(`/livros/${id}`, { params: { include } }),
    create: (data) => api.post('/livros', data),
    update: (id, data) => api.put(`/livros/${id}`, data),
    delete: (id) => api.delete(`/livros/${id}`)
  },

  // Autores
  autores: {
    getAll: (params = {}) => api.get('/autores', { params }),
    getById: (id) => api.get(`/autores/${id}`),
    create: (data) => api.post('/autores', data),
    update: (id, data) => api.put(`/autores/${id}`, data),
    delete: (id) => api.delete(`/autores/${id}`)
  },

  // Assun>
  assuntos: {
    getAll: (params = {}) => api.get('/assuntos', { params }),
    getById: (id) => api.get(`/assuntos/${id}`),
    create: (data) => api.post('/assuntos', data),
    update: (id, data) => api.put(`/assuntos/${id}`, data),
    delete: (id) => api.delete(`/assuntos/${id}`)
  },

  // Relatórios
  relatorios: {
    livrosPorAutor: (params = {}) => api.get('/relatorios/livros-por-autor', { params }),
    livrosPorAutorPDF: (params = {}) => api.get('/relatorios/livros-por-autor/pdf', { 
      params,
      responseType: 'blob'
    }),
    estatisticas: () => api.get('/relatorios/estatisticas')
  }
}
