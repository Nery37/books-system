# Sistema de Cadastro de Livros

Sistema Web completo para cadastro e gerenciamento de livros, autores e assuntos, desenvolvido com Laravel 10 (backend) e Vue 2 (frontend).

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Arquitetura](#arquitetura)
- [Pré-requisitos](#pré-requisitos)
- [Instalação e Configuração](#instalação-e-configuração)
- [Uso](#uso)
- [API Endpoints](#api-endpoints)
- [Testes](#testes)
- [Relatórios](#relatórios)
- [Contribuição](#contribuição)

## 🎯 Visão Geral

O Sistema de Cadastro de Livros é uma aplicação completa que permite:

- **Gerenciamento de Autores**: CRUD completo para autores
- **Gerenciamento de Assuntos**: CRUD completo para assuntos/categorias
- **Gerenciamento de Livros**: CRUD completo com relacionamentos M:N
- **Relatórios**: Geração de relatórios agrupados por autor em PDF
- **Busca e Filtros**: Sistema de busca por título, autor e assunto
- **Validações**: Validação robusta de dados e regras de negócio

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 10** - Framework PHP
- **l5-repository** - Padrão Repository
- **MySQL 8.0** - Banco de dados
- **Dompdf** - Geração de relatórios PDF
- **PHPUnit** - Testes automatizados
- **Docker** - Containerização

### Frontend
- **Vue 2** - Framework JavaScript
- **Bootstrap 5** - Framework CSS
- **Axios** - Cliente HTTP
- **Vue Router** - Roteamento SPA

### DevOps
- **Docker Compose** - Orquestração de containers
- **Nginx** - Servidor web
- **phpMyAdmin** - Interface de administração MySQL

## 🏗 Arquitetura

O projeto segue uma arquitetura em camadas bem definida:

```
📂 Backend (Laravel)
├── 🎮 Controllers (API)
├── 📝 Form Requests (Validação)
├── ⚙️ Services (Regras de Negócio)
├── 🗄️ Repositories (Acesso a Dados)
├── 🔄 Transformers (Apresentação)
├── 📊 Models (Eloquent)
└── 📋 Migrations & Seeds

📂 Frontend (Vue 2)
├── 🧩 Components
├── 📱 Views/Pages
├── 🔗 Services (API)
└── 🎨 Assets
```

### Banco de Dados

**Estrutura das Tabelas:**
- `autores` (CodAu, Nome)
- `assuntos` (codAs, Descricao)
- `livros` (Codl, Titulo, Editora, Edicao, AnoPublicacao, Valor)
- `livro_autor` (tabela pivot M:N)
- `livro_assunto` (tabela pivot M:N)
- `vw_relatorio_livros` (VIEW para relatórios)

## 📋 Pré-requisitos

- **Docker** e **Docker Compose**
- **Node.js** 16+ e **npm/yarn**
- **Git**
- **Make** (opcional, para comandos facilitados)

## ⚡ Instalação e Configuração

### 1. Clone o Repositório

```bash
git clone <repository-url>
cd livros-system
```

### 2. Instalação Rápida com Make

```bash
make install
```

Este comando executa automaticamente:
- Build dos containers Docker
- Instalação das dependências do Composer
- Execução das migrations
- População do banco com dados de exemplo

### 3. Instalação Manual (Alternativa)

```bash
# Build e inicialização dos containers
docker-compose build
docker-compose up -d

# Instalação das dependências
docker-compose exec app composer install

# Configuração do banco
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Geração da chave da aplicação
docker-compose exec app php artisan key:generate
```

### 4. Configuração do Frontend

```bash
cd frontend
npm install
npm run serve
```

## 🖥 Uso

### Acessos do Sistema

- **API Backend**: http://localhost:8000
- **Frontend Vue**: http://localhost:8080 (após `npm run serve`)
- **phpMyAdmin**: http://localhost:8080 (usuário: root, senha: root)

### Comandos Úteis com Make

```bash
# Subir containers
make up

# Parar containers  
make down

# Executar testes
make test

# Limpar cache
make clean

# Ver logs
make logs

# Acesso ao shell do container
make shell

# Backup do banco
make backup

# Restaurar backup
make restore FILE=backup_file.sql
```

## 📡 API Endpoints

### Autores
```http
GET    /api/v1/autores       # Listar autores
POST   /api/v1/autores       # Criar autor
GET    /api/v1/autores/{id}  # Buscar autor
PUT    /api/v1/autores/{id}  # Atualizar autor
DELETE /api/v1/autores/{id}  # Excluir autor
```

### Assuntos
```http
GET    /api/v1/assuntos       # Listar assuntos
POST   /api/v1/assuntos       # Criar assunto
GET    /api/v1/assuntos/{id}  # Buscar assunto
PUT    /api/v1/assuntos/{id}  # Atualizar assunto
DELETE /api/v1/assuntos/{id}  # Excluir assunto
```

### Livros
```http
GET    /api/v1/livros         # Listar livros (com filtros)
POST   /api/v1/livros         # Criar livro
GET    /api/v1/livros/{id}    # Buscar livro
PUT    /api/v1/livros/{id}    # Atualizar livro
DELETE /api/v1/livros/{id}    # Excluir livro

# Filtros de busca disponíveis:
GET /api/v1/livros?titulo=dom&autor=machado&assunto=romance
```

### Relatórios
```http
GET /api/v1/relatorios/livros-por-autor     # Dados do relatório
GET /api/v1/relatorios/livros-por-autor/pdf # PDF do relatório
GET /api/v1/relatorios/estatisticas         # Estatísticas gerais
```

### Exemplos de Requisições

**Criar Livro:**
```json
POST /api/v1/livros
{
  "Codl": 10,
  "Titulo": "Novo Livro",
  "Editora": "Editora Exemplo",
  "Edicao": 1,
  "AnoPublicacao": "2024",
  "Valor": 49.90,
  "autores": [1, 2],
  "assuntos": [1, 3]
}
```

**Resposta Padrão:**
```json
{
  "success": true,
  "data": { /* dados do objeto */ },
  "message": "Operação realizada com sucesso"
}
```

## 🧪 Testes

### Executar Testes

```bash
# Via Make
make test

# Via Docker
docker-compose exec app php artisan test

# Com coverage
docker-compose exec app php artisan test --coverage
```

### Estrutura de Testes

```
📂 tests/
├── 🔧 Feature/
│   ├── AutorTest.php
│   ├── AssuntoTest.php
│   ├── LivroTest.php
│   └── RelatorioTest.php
└── 🎯 Unit/
    ├── Services/
    └── Repositories/
```

Os testes cobrem:
- **Unit Tests**: Services e Repositories
- **Feature Tests**: Endpoints da API
- **Integration Tests**: Fluxos completos
- **Validation Tests**: Regras de validação

## 📊 Relatórios

### Relatório de Livros por Autor

O sistema gera relatórios baseados em uma **VIEW MySQL** que consolida:
- Informações dos livros
- Dados dos autores
- Assuntos relacionados
- Agrupamento por autor

**Funcionalidades:**
- Visualização web do relatório
- Download em PDF
- Ordenação por nome do autor
- Consolidação de múltiplos autores por livro

### Estatísticas

O sistema fornece estatísticas em tempo real:
- Total de livros, autores e assuntos
- Valor total do acervo
- Distribuição de livros por autor
- Análise temporal (anos de publicação)

## 🎨 Frontend (Vue 2)

### Estrutura do Frontend

```
📂 frontend/
├── 🧩 src/components/
│   ├── LivroForm.vue
│   ├── AutorList.vue
│   ├── AssuntoList.vue
│   └── RelatorioView.vue
├── 📱 src/views/
│   ├── Home.vue
│   ├── Livros.vue
│   ├── Autores.vue
│   └── Assuntos.vue
├── 🔗 src/services/
│   ├── api.js
│   ├── livroService.js
│   └── autorService.js
└── 🎨 src/assets/
```

### Funcionalidades do Frontend

- **Interface Responsiva**: Bootstrap 5 com design mobile-first
- **Formulários Dinâmicos**: Validação em tempo real
- **Busca e Filtros**: Interface intuitiva de pesquisa
- **Paginação**: Navegação eficiente de grandes datasets
- **Feedback Visual**: Mensagens de sucesso/erro
- **Formatação**: Máscaras para moeda e datas

## 🔧 Comandos de Desenvolvimento

### Backend
```bash
# Limpar cache
docker-compose exec app php artisan cache:clear

# Recriar banco (CUIDADO: apaga tudo!)
docker-compose exec app php artisan migrate:fresh --seed

# Gerar nova migration
docker-compose exec app php artisan make:migration nome_da_migration

# Gerar model com migration
docker-compose exec app php artisan make:model NomeModel -m

# Executar tinker (REPL)
docker-compose exec app php artisan tinker
```

### Frontend
```bash
cd frontend

# Desenvolvimento
npm run serve

# Build para produção
npm run build

# Lint e correção
npm run lint
```

## 🚀 Deploy em Produção

### 1. Configuração de Ambiente

```bash
# Copiar e ajustar variáveis de ambiente
cp backend/.env.example backend/.env.production

# Configurar:
APP_ENV=production
APP_DEBUG=false
DB_HOST=seu_host_mysql
# ... outras configurações
```

### 2. Build e Deploy

```bash
# Backend
docker-compose -f docker-compose.prod.yml up -d

# Frontend
cd frontend
npm run build
# Deploy dos arquivos da pasta dist/
```

## 🛠 Solução de Problemas

### Problemas Comuns

**1. Erro de conexão com banco:**
```bash
# Verificar se containers estão rodando
docker-compose ps

# Logs do MySQL
docker-compose logs db
```

**2. Erro de permissões:**
```bash
# Corrigir permissões Laravel
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

**3. Erro nas migrations:**
```bash
# Reset do banco
docker-compose exec app php artisan migrate:fresh --seed
```

## 🤝 Contribuição

### Diretrizes para Contribuição

1. **Fork** o projeto
2. Crie uma **branch** para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. **Commit** suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. **Push** para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um **Pull Request**

### Padrões de Código

- **PSR-12** para PHP
- **ESLint** para JavaScript
- **Commits semânticos** (feat, fix, docs, etc.)
- **Testes** obrigatórios para novas funcionalidades

## 📝 Licença

Este projeto está sob a licença [MIT](LICENSE).

## 👥 Autores

- **Seu Nome** - *Desenvolvimento inicial* - [seu-github](https://github.com/seu-usuario)

## 🔄 Versionamento

Utilizamos [SemVer](http://semver.org/) para versionamento. Para as versões disponíveis, veja as [tags neste repositório](https://github.com/seu-usuario/livros-system/tags).

## 📞 Suporte

Para suporte, entre em contato através:
- **Email**: seu-email@exemplo.com
- **Issues**: [GitHub Issues](https://github.com/seu-usuario/livros-system/issues)

---

⭐ **Desenvolvido com ❤️ usando Laravel + Vue.js**
