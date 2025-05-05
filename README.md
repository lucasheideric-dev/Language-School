# 🎓 Gestão de Alunos e Professores - Sistema para Escola de Idiomas

Este projeto é uma aplicação web completa para **gestão de alunos, professores e agendamentos de aulas de idiomas**, com autenticação segura, geração de requisição e testes unitários. A solução é totalmente containerizada com **Docker** e utiliza tecnologias modernas no frontend e backend.

---

## 🚀 Funcionalidades

- 👤 **Cadastro de Alunos**: Adição, edição e visualização de alunos.
- 👨‍🏫 **Cadastro de Professores**: Gerenciamento de professores.
- 📅 **Agendamento de Aulas**: Vinculação de alunos e professores com horários definidos.
- 🔐 **Autenticação via JWT**: Login seguro com tokens.
- 🧾 **Relatórios em PDF**: Geração e download de requisição de agendamentos.
- 🧪 **Testes Automatizados**:
  - Testes unitários com PHPUnit e Xdebug.
  - Testes de interface com Robot Framework.

---

## 🛠️ Tecnologias Utilizadas

### 🔧 Backend (CakePHP 4)

- Framework MVC robusto com suporte para API REST.
- Principais pacotes:
  - `dompdf/dompdf` – Geração de arquivos PDF.
  - `firebase/php-jwt` – Implementação de JWT.
  - `mobiledetect/mobiledetectlib` – Detecção de dispositivos móveis.
  - `phpunit/phpunit` – Testes unitários.
  - `phpstan/phpstan` – Análise estática de código.

### 🎨 Frontend (React + TailwindCSS)

- Interface moderna e responsiva.
- Principais bibliotecas:
  - `react-toastify` – Notificações.
  - `sweetalert2` – Alertas interativos.
  - `react-router-dom` – Navegação entre rotas.
  - `@fortawesome` – Ícones personalizados.

### 🗄️ Banco de Dados

- **PostgreSQL** – Armazenamento de dados estruturados: usuários, agendamentos, professores e alunos.

### 🐳 Docker

- Containers orquestrados via `docker-compose`:
  - `backend` (CakePHP 4)
  - `frontend` (React)
  - `db` (PostgreSQL)

---

## 📁 Estrutura de Pastas

```plaintext
├── backend
├── frontend
├── docker-compose.yml
```

## Como Rodar o Projeto

### Passo 1: Configuração Inicial

Certifique-se de ter o **Docker** e o **Docker Compose** instalados em sua máquina.

### Passo 2: Executando os Containers

No diretório raiz do projeto, execute o seguinte comando para construir e iniciar os serviços:

```bash
docker-compose up --build
```

## Configuração do Banco de Dados

### Inserir Usuário Inicial

Conecte-se ao banco de dados PostgreSQL e execute o seguinte comando SQL para inserir o usuário inicial:

```bash
INSERT INTO users (id, first_name, last_name, email, password, created_at)
VALUES (
  1,
  'Nome',
  'Sobrenome',
  'admin@email.com',
  '$2y$10$mJ6NkfwIJ0VZgNedu...', -- 123456
  NOW()
);

```

# Imagens do Projeto

| **Imagem**                                   | **Descrição**                                | **Link**                                                                     |
| -------------------------------------------- | -------------------------------------------- | ---------------------------------------------------------------------------- |
| **Login**                                    | Tela de login do sistema                     | ![Login](https://i.imgur.com/CpcXXmE.png)                                    |
| **Dashboard**                                | Dashboard com resumo das atividades          | ![Dashboard](https://i.imgur.com/XQ2ly2i.png)                                |
| **Lista de Alunos**                          | Visualização de alunos cadastrados           | ![Lista de Alunos](https://i.imgur.com/6zsYJ1e.png)                          |
| **Modal de Alunos**                          | Modal para adição ou edição de alunos        | ![Modal de Alunos](https://i.imgur.com/2riBEx7.png)                          |
| **Lista de Professores**                     | Visualização de professores cadastrados      | ![Lista de Professores](https://i.imgur.com/00Ogmku.png)                     |
| **Modal de Professores**                     | Modal para adição ou edição de professores   | ![Modal de Professores](https://i.imgur.com/AHlJ1AH.png)                     |
| **Lista de Agendamentos**                    | Tela com todos os agendamentos realizados    | ![Lista de Agendamentos](https://i.imgur.com/WNqfrCG.png)                    |
| **Modal de Agendamento**                     | Modal para gerenciar detalhes de agendamento | ![Modal de Agendamento](https://i.imgur.com/fr6GL4a.png)                     |
| **Aula Agendada para Menor de 16 Anos**      | Tela indicando uma aula para menores         | ![Aula Agendada para Menor de 16 Anos](https://i.imgur.com/qDdytaY.png)      |
| **Tela de Requisição**                       | Visualização de requisições feitas           | ![Tela de Requisição](https://i.imgur.com/tGXrpu7.png)                       |
| **Arquivo Gerado**                           | Exemplo de arquivo gerado pelo sistema       | ![Arquivo Gerado](https://i.imgur.com/CJ1NIyA.png)                           |
| **Atalhos de Busca**                         | Menu com atalhos de busca                    | ![Atalhos de Busca](https://i.imgur.com/QRt7A8v.png)                         |
| **Validação de Token JWT com Time**          | Tela mostrando validação de JWT              | ![Validação de Token JWT com Time](https://i.imgur.com/qWEP3HU.png)          |
| **Teste de API**                             | Testes realizados na API                     | ![Teste de API](https://i.imgur.com/5eOnzoZ.png)                             |
| **Docker e Containers**                      | Configurações do ambiente Docker             | ![Docker e Containers](https://i.imgur.com/LjXSEIZ.png)                      |
| **Testes Unitários com Relatório do Xdebug** | Relatório gerado pelos testes unitários      | ![Testes Unitários com Relatório do Xdebug](https://i.imgur.com/TVv9GkC.png) |
| **Testes Automatizados com Robot Framework** | Relatório de testes automatizados            | ![Testes Automatizados com Robot Framework](https://i.imgur.com/VnP7qZE.png) |
