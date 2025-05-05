# Gestão de Alunos e Professores - Aplicação para Aulas de Idiomas

A **Gestão de Alunos e Professores** é uma aplicação web desenvolvida para gerenciar informações sobre alunos e professores de aulas de idiomas. A aplicação inclui funcionalidades como cadastro, agendamento de aulas e geração de relatórios. Foi criada utilizando Docker para containerizar o backend, frontend e o banco de dados PostgreSQL.

### Funcionalidades

- **Cadastro de Alunos**: Permite adicionar, editar e visualizar alunos.
- **Cadastro de Professores**: Permite adicionar, editar e visualizar professores.
- **Agendamento de Aulas**: Permite agendar aulas de idiomas entre alunos e professores.
- **Autenticação**: Implementação de login seguro utilizando JWT.
- **Relatórios**: Geração de relatórios em PDF para download.
- **Testes Automatizados**: Testes unitários com PHPUnit e testes automatizados com Robot Framework.

### Tecnologias

**Backend**

- **CakePHP 4** para gerenciar rotas, modelos e controladores.
- Dependências:
  - `dompdf/dompdf`: Para gerar documentos em PDF.
  - `firebase/php-jwt`: Para autenticação com JWT.
  - `mobiledetect/mobiledetectlib`: Para detectar dispositivos móveis.
  - `phpunit/phpunit`: Para testes unitários.
  - `phpstan/phpstan`: Para análise estática de código.

**Frontend**

- **React** com **TailwindCSS** para uma interface moderna e responsiva.
- Dependências:
  - `react-toastify`: Para exibir notificações.
  - `sweetalert2`: Para exibir alertas interativos.
  - `react-router-dom`: Para navegação entre páginas.
  - `@fortawesome/fontawesome-svg-core`, `@fortawesome/free-solid-svg-icons`, `@fortawesome/react-fontawesome`: Para ícones.

**Banco de Dados**

- **PostgreSQL** para armazenar dados de usuários, professores, alunos e agendamentos.

**Docker**

- **Docker Compose** para orquestrar os containers:
  - **Backend** (CakePHP 4)
  - **Frontend** (React)
  - **Banco de Dados** (PostgreSQL)

**Testes**

- **Testes Unitários** com PHPUnit e Xdebug.
- **Testes Automatizados** com Robot Framework.

### Estrutura de Pastas

- \*\*├── backend ├── frontend ├── robot ├── docker-compose.yml

### Como Rodar o Projeto

**Passo 1: Configuração Inicial**  
Certifique-se de ter o Docker e o Docker Compose instalados.

**Passo 2: Executando os Containers**  
No diretório raiz do projeto, execute o seguinte comando:

# Configuração do Projeto

Este arquivo README contém instruções para configurar e inicializar o projeto, incluindo a criação do usuário inicial no banco de dados PostgreSQL.

## Passo 1: Construir e Iniciar os Serviços

Utilize o Docker Compose para construir e iniciar os serviços do projeto. Execute o seguinte comando no terminal:

```bash
docker-compose up --build
Passo 2: Configuração do Banco de Dados
Após iniciar os serviços, configure o banco de dados PostgreSQL para o projeto.

Passo 3: Inserir o Usuário Inicial
Para inserir o usuário inicial no banco de dados PostgreSQL, execute o comando SQL abaixo. Certifique-se de que está conectado ao banco de dados antes de prosseguir.

sql
INSERT INTO users (id, first_name, last_name, email, password, created_at)
VALUES (1, 'Lucas', 'Heideric', 'admin@gmail.com', '$2y$10$mJ6NkfwIJ0VZgNedu
```

# Imagens do Projeto

| **Imagem**                                   | **Descrição**                                | **Link**                                                                     |
| -------------------------------------------- | -------------------------------------------- | ---------------------------------------------------------------------------- |
| **Login**                                    | Tela de login do sistema                     | ![Login](https://i.imgur.com/CpcXXmE.png)                                    |
| **Dashboard**                                | Dashboard com resumo das atividades          | ![Dashboard](https://i.imgur.com/XQ2ly2i.png)                                |
| **Lista de Alunos**                          | Visualização de todos os alunos cadastrados  | ![Lista de Alunos](https://i.imgur.com/6zsYJ1e.png)                          |
| **Modal de Alunos**                          | Modal para detalhes ou edição de alunos      | ![Modal de Alunos](https://i.imgur.com/2riBEx7.png)                          |
| **Lista de Professores**                     | Visualização de professores cadastrados      | ![Lista de Professores](https://i.imgur.com/00Ogmku.png)                     |
| **Modal de Professores**                     | Modal para detalhes ou edição de professores | ![Modal de Professores](https://i.imgur.com/AHlJ1AH.png)                     |
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
