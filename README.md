# Portal de Estágios — UniALFA

Portal para conectar alunos e empresas no processo de estágio da UniALFA. O sistema é dividido em três partes que trabalham juntas: uma API central em Node.js, um portal web em PHP para alunos e empresas, e um back-office desktop em Java para a administração da instituição.

---

## O problema e a solução

Sem um sistema centralizado, a divulgação de vagas e o controle de candidaturas acaba sendo feito de forma manual e fragmentada — ruim pra todo mundo envolvido.

O portal resolve isso em três frentes:

**Portal Web (PHP)** — onde alunos visualizam vagas, se candidatam e acompanham o status das candidaturas com notificações. Empresas publicam e gerenciam suas vagas e podem aprovar ou rejeitar candidatos diretamente pela interface.

**API REST (Node.js + TypeScript)** — é o núcleo do sistema. Centraliza toda a lógica de negócio e é consumida tanto pelo portal PHP quanto pelo back-office Java. Quando uma candidatura é aprovada ou rejeitada, a API automaticamente gera uma notificação pro aluno.

**Back-office Desktop (Java Swing)** — ferramenta para os administradores da instituição. Permite gerenciar alunos, empresas, vagas e candidaturas, e gerar relatórios exportáveis em `.txt`.

---

## Objetivos

- Centralizar a divulgação de vagas e o processo de candidatura a estágios
- Dar às empresas autonomia para publicar e gerenciar suas vagas
- Dar à instituição uma ferramenta de supervisão e relatórios
- Integrar múltiplas linguagens e camadas numa mesma solução (API, web, desktop)
- Aplicar na prática conceitos de OOP, padrão DAO, arquitetura em camadas e MVC

---

## Tecnologias utilizadas

**API**

| Tecnologia | Uso |
|---|---|
| Node.js + TypeScript | Runtime e tipagem |
| Express 5 | Framework HTTP |
| TypeORM | ORM / migrations |
| MySQL | Banco de dados |
| Zod | Validação de entrada |
| bcryptjs | Hash de senhas |
| tsx | Execução em desenvolvimento |

**Portal Web**

| Tecnologia | Uso |
|---|---|
| PHP 8+ | Lógica server-side |
| Bootstrap 5.3 | Estilização e responsividade |
| HTML5 / CSS3 | Estrutura e estilos |

**Back-office**

| Tecnologia | Uso |
|---|---|
| Java 21 | Linguagem principal |
| Java Swing | Interface gráfica |
| Maven | Gerenciamento de dependências |
| MySQL Connector/J 8.3 | Conexão JDBC |
| bcrypt (favre.lib) | Hash de senhas |

---

## Arquitetura

```
┌──────────────────────┐     HTTP REST      ┌──────────────────────┐
│   Portal Web (PHP)   │ ──────────────────► │   API Node.js/TS     │
│   (Aluno / Empresa)  │                     │   (Express + TypeORM)│
└──────────────────────┘                     └────────┬─────────────┘
                                                      │ SQL
┌──────────────────────┐     SQL direto              ▼
│  Back-office (Java)  │ ──────────────────► ┌──────────────────────┐
│  (Administração)     │                     │   MySQL — db_estagio │
└──────────────────────┘                     └──────────────────────┘
```

---

## Como rodar localmente

### Pré-requisitos

- Node.js 18+
- PHP 8+
- Java 21 + Maven
- MySQL 8+
- Um servidor HTTP para PHP (XAMPP, WAMP ou o servidor embutido do PHP)

---

### 1. Banco de dados

```sql
CREATE DATABASE db_estagio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 2. API

```bash
cd api
npm install
```

Crie o arquivo `.env` na raiz de `/api`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASS=sua_senha
DB_NAME=db_estagio
PORT=3000
```

Rode as migrations e popule o banco:

```bash
npx typeorm-ts-node-commonjs migration:run -d src/database/data-source.ts
npm run seed
```

Suba o servidor:

```bash
npm run dev
```

API disponível em `http://localhost:3000`.

> Para resetar os dados: `npm run seed:reset`

---

### 3. Portal Web (PHP)

Com a API rodando, sirva a pasta `/php`:

```bash
cd php
php -S localhost:8080
```

Ou copie a pasta `php` para o `htdocs` (XAMPP) / `www` (WAMP) e acesse via `http://localhost/php`.

> O `ApiClient.php` aponta pra `http://localhost:3000` por padrão. Ajuste se necessário.

---

### 4. Back-office Java

```bash
cd java/backOffice
mvn clean install
mvn exec:java -Dexec.mainClass="Main"
```

Ou abra no IntelliJ e execute `Main.java` direto.

> As credenciais do banco ficam em `src/main/java/dao/DatabaseConnection.java`.

---

## Endpoints da API

### Empresas
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/empresas` | Lista todas as empresas |
| GET | `/api/empresas/:id` | Busca por ID |
| POST | `/api/empresas` | Cadastra empresa |
| PUT | `/api/empresas/:id` | Atualiza empresa |
| DELETE | `/api/empresas/:id` | Remove empresa |

### Alunos
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/alunos` | Lista todos os alunos |
| GET | `/api/alunos/:id` | Busca por ID |
| POST | `/api/alunos` | Cadastra aluno |
| PUT | `/api/alunos/:id` | Atualiza aluno |
| DELETE | `/api/alunos/:id` | Remove aluno |

### Vagas
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/vagas` | Lista todas as vagas |
| GET | `/api/vagas/:id` | Busca por ID |
| POST | `/api/vagas` | Cria vaga |
| PUT | `/api/vagas/:id` | Atualiza vaga |
| DELETE | `/api/vagas/:id` | Remove vaga |

### Candidaturas
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/candidaturas` | Lista todas as candidaturas |
| GET | `/api/candidaturas?vagaId=1` | Filtra por vaga |
| GET | `/api/candidaturas/:id` | Busca por ID |
| POST | `/api/candidaturas` | Cria candidatura |
| PUT | `/api/candidaturas/:id/status` | Atualiza status (`pendente`, `aprovada`, `rejeitada`) |
| DELETE | `/api/candidaturas/:id` | Remove candidatura |

### Notificações
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/notificacoes/:alunoId` | Lista notificações do aluno |
| PUT | `/api/notificacoes/:id/lida` | Marca como lida |

> Sempre que uma candidatura é aprovada ou rejeitada, a API cria automaticamente uma notificação para o aluno.

---

## Estrutura do projeto

```
portal-estagios/
│
├── api/                            # API REST (Node.js + TypeScript)
│   ├── src/
│   │   ├── controllers/            # Controladores HTTP
│   │   ├── database/
│   │   │   ├── data-source.ts      # Configuração do TypeORM
│   │   │   ├── migrations/         # Migrations
│   │   │   └── seeds/              # Dados iniciais
│   │   ├── models/                 # Entidades TypeORM
│   │   ├── repositories/           # Repositórios
│   │   ├── routes/                 # Rotas Express
│   │   ├── services/               # Regras de negócio
│   │   ├── utils/                  # AppError
│   │   └── server.ts
│   ├── .env
│   ├── package.json
│   └── tsconfig.json
│
├── php/                            # Portal Web (PHP + Bootstrap)
│   ├── index.php                   # Tela de login
│   ├── aluno/
│   │   ├── index.php               # Vagas disponíveis
│   │   ├── candidatar.php          # Candidatura a uma vaga
│   │   ├── minhas_candidaturas.php # Histórico de candidaturas
│   │   ├── notificacoes.php        # Central de notificações
│   │   └── logout.php
│   ├── empresa/
│   │   ├── index.php               # Dashboard da empresa
│   │   ├── vagas.php               # Vagas publicadas
│   │   ├── vaga_form.php           # Criar / editar vaga
│   │   ├── candidatos.php          # Candidatos por vaga
│   │   └── logout.php
│   └── shared/
│       ├── ApiClient.php           # Cliente HTTP
│       ├── AlunoService.php
│       ├── EmpresaService.php
│       ├── Vaga.php
│       └── Candidatura.php
│
└── java/                           # Back-office Desktop (Java Swing)
    └── backOffice/
        ├── pom.xml
        └── src/main/java/
            ├── Main.java
            ├── dao/                # JDBC (AlunoDAO, EmpresaDAO, VagaDAO...)
            ├── model/              # Modelos de domínio
            ├── services/           # Serviços com interfaces
            ├── gui/                # Telas Swing
            │   ├── TelaLogin.java
            │   ├── TelaPrincipal.java
            │   ├── TelaAlunos.java
            │   ├── TelaEmpresa.java
            │   ├── TelaVagas.java
            │   ├── TelaCandidaturas.java
            │   └── TelaRelatorios.java
            ├── errorHandler/
            └── utils/
```

---

## Funcionalidades implementadas

**Portal Web — Aluno**
- Login com autenticação via API
- Listagem de vagas abertas
- Candidatura com controle de duplicidade
- Acompanhamento das candidaturas e seus status
- Notificações com contador de não lidas
- Logout

**Portal Web — Empresa**
- Login com autenticação via API
- Dashboard com resumo de vagas
- Criação, edição e encerramento de vagas
- Listagem de candidatos por vaga
- Aprovar ou rejeitar candidatos (notificação automática para o aluno)
- Logout

**API**
- CRUD completo de Empresas, Alunos, Vagas e Candidaturas
- Login com verificação de hash bcrypt
- Validação de dados com Zod
- Notificação automática ao mudar status de candidatura
- Seeds e reset de banco

**Back-office Java**
- Login administrativo
- CRUD de alunos, empresas, vagas e candidaturas via Swing
- Relatórios filtráveis por tipo (alunos, empresas, vagas, candidaturas)
- Exportação de relatório em `.txt`
- Padrão DAO com interface genérica
- Tratamento centralizado de exceções

---

## Evidências de testes

Os testes foram feitos de forma manual cobrindo os fluxos principais:

**Aluno**
- Login com credenciais válidas e inválidas
- Visualização e candidatura a vagas
- Verificação do status das candidaturas após ação da empresa
- Recebimento e leitura de notificações

**Empresa**
- Login e acesso ao dashboard
- Criação, edição e encerramento de vagas
- Aprovação e rejeição de candidatos com atualização imediata de status

**API**
- Todos os endpoints testados via portal PHP e back-office Java
- Validação de campos obrigatórios com retorno de erro descritivo
- Geração correta de notificações ao mudar status
- Migrations e seeds executando sem erros

**Back-office**
- Login administrativo
- CRUD funcional em todas as telas
- Exportação de relatório em `.txt`

---

## Equipe

| Integrante | Contribuições |
|---|---|
| **Gislaine** | Back-office desktop em Java — todas as telas Swing, camada DAO, serviços e relatórios |
| **Lucas** | API REST em Node.js/TypeScript — rotas, controllers, services, migrations, seeds — e os protótipos do sistema |
| **Lyzandra** | Portal Web PHP — painel do aluno e da empresa, ApiClient, integração com a API — e gerenciamento do repositório (revisão e merge de PRs) |