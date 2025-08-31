<template>
  <div class="container mt-4">
    <h2 class="mb-4">Relatórios</h2>

    <!-- Filtros do Relatório -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Relatório de Livros por Autor</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="gerarRelatorio">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="autorId" class="form-label">Filtrar por Autor (opcional):</label>
              <select v-model="filtros.autorId" class="form-select">
                <option value="">Todos os autores</option>
                <option v-for="autor in autores" :key="autor.id" :value="autor.id">
                  {{ autor.nome }}
                </option>
              </select>
            </div>
            <div class="col-md-6 mb-3 d-flex align-items-end">
              <div class="d-flex gap-2 w-100">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-file-earmark-text me-2"></i>
                  Gerar Relatório
                </button>
                <button 
                  type="button" 
                  @click="limparFiltros" 
                  class="btn btn-secondary" 
                  :disabled="loading"
                >
                  <i class="bi bi-arrow-clockwise me-2"></i>
                  Limpar
                </button>
                <button 
                  type="button" 
                  @click="gerarRelatorioPDF" 
                  class="btn btn-danger" 
                  :disabled="loading || relatorioData.length === 0"
                >
                  <i class="bi bi-file-earmark-pdf me-2"></i>
                  Gerar PDF
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center my-4">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
    </div>

    <!-- Resultados do Relatório -->
    <div v-if="!loading && relatorioData.length > 0" class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Resultado do Relatório</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead class="table-dark">
              <tr>
                <th>Autor</th>
                <th>Total de Livros</th>
                <th>Livros</th>
                <th>Valor Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in relatorioData" :key="item.autor_id">
                <td>{{ item.autor_nome }}</td>
                <td>{{ item.total_livros }}</td>
                <td>
                  <ul class="list-unstyled mb-0">
                    <li v-for="livro in item.livros" :key="livro.id" class="small">
                      {{ livro.titulo }} - {{ formatarMoeda(livro.valor) }}
                    </li>
                  </ul>
                </td>
                <td class="fw-bold">{{ formatarMoeda(item.valor_total) }}</td>
              </tr>
            </tbody>
            <tfoot v-if="totais.totalGeral > 0">
              <tr class="table-info">
                <th>TOTAL GERAL</th>
                <th>{{ totais.totalLivros }}</th>
                <th>-</th>
                <th class="fw-bold">{{ formatarMoeda(totais.totalGeral) }}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
    
    <!-- Mensagem quando não há dados -->
    <div v-if="!loading && relatorioData.length === 0" class="card mb-4">
      <div class="card-body text-center">
        <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
        <h5 class="mt-3 text-muted">Nenhum dado encontrado</h5>
        <p class="text-muted">
          {{ filtros.autorId ? 'Não há livros para o autor selecionado.' : 'Não há livros cadastrados no sistema.' }}
        </p>
      </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div v-if="estatisticas" class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Estatísticas Gerais do Sistema</h5>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-md-3">
                <div class="border-end">
                  <h4 class="text-primary">{{ estatisticas.total_livros }}</h4>
                  <p class="mb-0">Total de Livros</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="border-end">
                  <h4 class="text-success">{{ estatisticas.total_autores }}</h4>
                  <p class="mb-0">Total de Autores</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="border-end">
                  <h4 class="text-info">{{ estatisticas.total_assuntos }}</h4>
                  <p class="mb-0">Total de Assuntos</p>
                </div>
              </div>
              <div class="col-md-3">
                <h4 class="text-warning">{{ formatarMoeda(estatisticas.valor_total_acervo) }}</h4>
                <p class="mb-0">Valor Total do Acervo</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
  name: 'Relatorios',
  data() {
    return {
      autores: [],
      loading: false,
      filtros: {
        autorId: ''
      },
      relatorioData: [],
      estatisticas: null,
      totais: {
        totalLivros: 0,
        totalGeral: 0
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
        
        // Carregar autores
        const autoresResponse = await api.autores.getAll()
        this.autores = (autoresResponse.data.data.data || [])
        
        // Tentar carregar estatísticas do endpoint específico
        try {
          const estatisticasResponse = await api.relatorios.estatisticas()
          this.estatisticas = estatisticasResponse.data.data || {}
        } catch (error) {
          console.log('Endpoint de estatísticas não disponível, calculando manualmente...')
          
          // Carregar dados para calcular estatísticas manualmente
          const livrosResponse = await api.livros.getAll({ include: 'autores,assuntos' })
          const livros = (livrosResponse.data.data.data || [])
          
          const assuntosResponse = await api.assuntos.getAll()
          const assuntos = (assuntosResponse.data.data.data || [])
          
          // Calcular estatísticas reais
          const valorTotalAcervo = livros.reduce((total, livro) => total + parseFloat(livro.valor || 0), 0)
          
          this.estatisticas = {
            total_livros: livros.length,
            total_autores: this.autores.length,
            total_assuntos: assuntos.length,
            valor_total_acervo: valorTotalAcervo
          }
        }
        
        // Gerar relatório inicial com todos os dados
        await this.gerarRelatorio()
        
      } catch (error) {
        console.error('Erro ao carregar dados:', error)
        alert('Erro ao carregar dados: ' + (error.response?.data?.message || error.message))
      } finally {
        this.loading = false
      }
    },
    
    async gerarRelatorio() {
      try {
        this.loading = true
        
        // Tentar usar o endpoint específico de relatórios primeiro
        try {
          const params = {}
          if (this.filtros.autorId) {
            params.autor_id = this.filtros.autorId
          }
          
          const relatorioResponse = await api.relatorios.livrosPorAutor(params)
          this.relatorioData = relatorioResponse.data.data || []
          this.calcularTotais()
          
        } catch (error) {
          console.log('Endpoint de relatórios não disponível, gerando manualmente...')
          
          // Fallback: buscar livros com autores incluídos e processar manualmente
          const livrosResponse = await api.livros.getAll({ include: 'autores,assuntos' })
          const livros = (livrosResponse.data.data.data || [])
          
          console.log('Livros recebidos:', livros)
          
          // Processar livros e extrair autores da estrutura retornada
          const relatorio = {}
          
          livros.forEach(livro => {
            // Os autores vêm dentro de livro.autores.data
            const autoresDoLivro = livro.autores && livro.autores.data ? livro.autores.data : []
            
            console.log(`Livro: ${livro.titulo}, Autores:`, autoresDoLivro)
            
            if (autoresDoLivro.length > 0) {
              autoresDoLivro.forEach(autor => {
                // Verificar filtro por autor
                if (!this.filtros.autorId || autor.id == this.filtros.autorId) {
                  if (!relatorio[autor.id]) {
                    relatorio[autor.id] = {
                      autor_id: autor.id,
                      autor_nome: autor.nome,
                      livros: [],
                      total_livros: 0,
                      valor_total: 0
                    }
                  }
                  
                  relatorio[autor.id].livros.push({
                    id: livro.id,
                    titulo: livro.titulo,
                    valor: parseFloat(livro.valor || 0)
                  })
                  relatorio[autor.id].total_livros++
                  relatorio[autor.id].valor_total += parseFloat(livro.valor || 0)
                }
              })
            }
          })
          
          // Converter para array e ordenar por nome do autor
          this.relatorioData = Object.values(relatorio).sort((a, b) => 
            a.autor_nome.localeCompare(b.autor_nome)
          )
          
          this.calcularTotais()
        }
        
      } catch (error) {
        console.error('Erro ao gerar relatório:', error)
        alert('Erro ao gerar relatório: ' + (error.response?.data?.message || error.message))
      } finally {
        this.loading = false
      }
    },
    
    async gerarRelatorioPDF() {
      try {
        this.loading = true
        
        if (this.relatorioData.length === 0) {
          alert('Nenhum dado para gerar o relatório!')
          return
        }
        
        // Tentar usar endpoint de PDF primeiro
        try {
          const params = {}
          if (this.filtros.autorId) {
            params.autor_id = this.filtros.autorId
          }
          
          const response = await api.relatorios.livrosPorAutorPDF(params)
          
          // Criar link para download do PDF
          const blob = new Blob([response.data], { type: 'application/pdf' })
          const url = window.URL.createObjectURL(blob)
          const link = document.createElement('a')
          link.href = url
          
          const dataAtual = new Date().toISOString().split('T')[0]
          const nomeArquivo = this.filtros.autorId 
            ? `relatorio-livros-autor-${this.filtros.autorId}-${dataAtual}.pdf`
            : `relatorio-livros-geral-${dataAtual}.pdf`
            
          link.download = nomeArquivo
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
          window.URL.revokeObjectURL(url)
          
          alert('Relatório PDF gerado com sucesso!')
          
        } catch (pdfError) {
          console.log('Endpoint de PDF não disponível, gerando PDF localmente...')
          
          // Gerar PDF usando jsPDF
          this.gerarPDFLocal()
        }
        
      } catch (error) {
        console.error('Erro ao gerar PDF:', error)
        alert('Erro ao gerar relatório: ' + (error.response?.data?.message || error.message))
      } finally {
        this.loading = false
      }
    },
    
    gerarPDFLocal() {
      const doc = new jsPDF()
      
      // Configurações iniciais
      const pageWidth = doc.internal.pageSize.width
      const margin = 20
      
      // Título do relatório
      doc.setFontSize(18)
      doc.setFont('helvetica', 'bold')
      doc.text('SISTEMA DE GERENCIAMENTO DE LIVROS', pageWidth / 2, 20, { align: 'center' })
      
      doc.setFontSize(14)
      doc.text('Relatório de Livros por Autor', pageWidth / 2, 30, { align: 'center' })
      
      // Data e hora
      const dataAtual = new Date().toLocaleString('pt-BR')
      doc.setFontSize(10)
      doc.setFont('helvetica', 'normal')
      doc.text(`Gerado em: ${dataAtual}`, margin, 45)
      
      // Filtro aplicado
      if (this.filtros.autorId) {
        const autorSelecionado = this.autores.find(a => a.id == this.filtros.autorId)
        doc.text(`Filtro: ${autorSelecionado ? autorSelecionado.nome : 'Autor não encontrado'}`, margin, 52)
      } else {
        doc.text('Filtro: Todos os autores', margin, 52)
      }
      
      // Preparar dados para a tabela
      const headers = [['Autor', 'Qtd. Livros', 'Livros', 'Valor Total']]
      const data = []
      
      this.relatorioData.forEach(item => {
        const livrosList = item.livros.map(livro => 
          `${livro.titulo} (${this.formatarMoeda(livro.valor)})`
        ).join('\n')
        
        data.push([
          item.autor_nome,
          item.total_livros.toString(),
          livrosList,
          this.formatarMoeda(item.valor_total)
        ])
      })
      
      // Adicionar totais
      data.push([
        'TOTAL GERAL',
        this.totais.totalLivros.toString(),
        '-',
        this.formatarMoeda(this.totais.totalGeral)
      ])
      
      // Gerar tabela
      doc.autoTable({
        head: headers,
        body: data,
        startY: 60,
        styles: {
          fontSize: 8,
          cellPadding: 3,
        },
        headStyles: {
          fillColor: [52, 58, 64],
          textColor: 255,
          fontStyle: 'bold'
        },
        alternateRowStyles: {
          fillColor: [248, 249, 250]
        },
        columnStyles: {
          0: { cellWidth: 40 },
          1: { cellWidth: 20, halign: 'center' },
          2: { cellWidth: 80 },
          3: { cellWidth: 30, halign: 'right' }
        },
        footStyles: {
          fillColor: [23, 162, 184],
          textColor: 255,
          fontStyle: 'bold'
        }
      })
      
      // Rodapé
      const finalY = doc.lastAutoTable.finalY + 20
      doc.setFontSize(8)
      doc.text(`Página 1 - Sistema de Gerenciamento de Livros`, pageWidth / 2, finalY, { align: 'center' })
      
      // Salvar arquivo
      const dataAtual2 = new Date().toISOString().split('T')[0]
      const nomeArquivo = this.filtros.autorId 
        ? `relatorio-livros-autor-${this.filtros.autorId}-${dataAtual2}.pdf`
        : `relatorio-livros-geral-${dataAtual2}.pdf`
        
      doc.save(nomeArquivo)
      
      alert('Relatório PDF gerado com sucesso!')
    },
    
    gerarConteudoPDF() {
      const dataAtual = new Date().toLocaleDateString('pt-BR')
      const horaAtual = new Date().toLocaleTimeString('pt-BR')
      
      let conteudo = 'SISTEMA DE GERENCIAMENTO DE LIVROS\n'
      conteudo += '=====================================\n'
      conteudo += 'RELATÓRIO DE LIVROS POR AUTOR\n'
      conteudo += `Data/Hora: ${dataAtual} ${horaAtual}\n\n`
      
      if (this.filtros.autorId) {
        const autorSelecionado = this.autores.find(a => a.id == this.filtros.autorId)
        conteudo += `Filtro: ${autorSelecionado ? autorSelecionado.nome : 'Autor não encontrado'}\n\n`
      } else {
        conteudo += 'Filtro: Todos os autores\n\n'
      }
      
      conteudo += '=====================================\n\n'
      
      this.relatorioData.forEach((item, index) => {
        conteudo += `${index + 1}. AUTOR: ${item.autor_nome}\n`
        conteudo += `   Total de Livros: ${item.total_livros}\n`
        conteudo += `   Valor Total: ${this.formatarMoeda(item.valor_total)}\n`
        conteudo += `   Livros:\n`
        
        item.livros.forEach((livro, livroIndex) => {
          conteudo += `     ${livroIndex + 1}. ${livro.titulo} - ${this.formatarMoeda(livro.valor)}\n`
        })
        
        conteudo += '\n'
      })
      
      conteudo += '=====================================\n'
      conteudo += 'TOTAIS GERAIS:\n'
      conteudo += `Total de Livros: ${this.totais.totalLivros}\n`
      conteudo += `Valor Total: ${this.formatarMoeda(this.totais.totalGeral)}\n`
      conteudo += '=====================================\n'
      
      return conteudo
    },
    
    calcularTotais() {
      this.totais.totalLivros = this.relatorioData.reduce((total, item) => total + item.total_livros, 0)
      this.totais.totalGeral = this.relatorioData.reduce((total, item) => total + parseFloat(item.valor_total), 0)
    },
    
    async limparFiltros() {
      this.filtros.autorId = ''
      await this.gerarRelatorio()
    },
    
    formatarMoeda(valor) {
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(valor)
    }
  }
}
</script>
