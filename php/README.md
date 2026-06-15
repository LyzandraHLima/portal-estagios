# php — Frontend Web

Interface web do Portal de Estágios UniALFA, desenvolvida em PHP puro com Bootstrap 5. Consome a API REST do projeto para oferecer acesso ao sistema tanto para alunos quanto para empresas.

---

## Tecnologias

- PHP 8+
- Bootstrap 5.3.3
- API REST (Node.js + TypeScript) em `http://localhost:3000`

---

## Estrutura de pastas

    php/
    index.php               Página de login (Aluno / Empresa)
    shared/
        ApiClient.php       Cliente HTTP para consumo da API
        AlunoService.php    Serviços do aluno
        EmpresaService.php  Serviços da empresa
        Vaga.php            Model de vaga
        Candidatura.php     Model de candidatura
    aluno/
        index.php               Lista de vagas disponíveis
        candidatar.php          Formulário de candidatura
        minhas_candidaturas.php Candidaturas do aluno
        notificacoes.php        Notificações do aluno
        logout.php              Encerramento de sessão
    empresa/
        index.php       Dashboard da empresa
        vagas.php       Lista e exclusão de vagas
        vaga_form.php   Criação e edição de vagas
        candidatos.php  Lista de candidatos por vaga
        logout.php      Encerramento de sessão

---

## Funcionalidades

### Aluno
- Login com email e senha
- Visualizar vagas de estágio disponíveis
- Se candidatar a uma vaga com mensagem opcional
- Acompanhar o status das candidaturas (pendente, aprovada, rejeitada)
- Receber e marcar notificações como lidas

### Empresa
- Login com email e senha
- Dashboard com total de vagas publicadas
- Criar, editar e excluir vagas
- Visualizar candidatos de cada vaga
- Aprovar ou rejeitar candidaturas

---

## Como executar

**Pré-requisito:** a API REST deve estar rodando em `http://localhost:3000`.

1. Coloque a pasta `php/` em um servidor com suporte a PHP (ex: XAMPP, Laragon, ou PHP built-in server)
2. Acesse via navegador:

```
http://localhost/php/
```

Ou, usando o servidor embutido do PHP:

```bash
cd php
php -S localhost:8080
```

Depois acesse `http://localhost:8080`.

---

## Endpoints consumidos

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | /api/alunos/login | Login do aluno |
| POST | /api/empresas/login | Login da empresa |
| GET | /api/vagas | Listar todas as vagas |
| GET | /api/vagas?empresaId= | Listar vagas de uma empresa |
| GET | /api/vagas/:id | Buscar vaga por ID |
| POST | /api/vagas | Criar vaga |
| PUT | /api/vagas/:id | Editar vaga |
| DELETE | /api/vagas/:id | Excluir vaga |
| POST | /api/candidaturas | Enviar candidatura |
| GET | /api/candidaturas | Listar candidaturas |
| GET | /api/candidaturas?vagaId= | Listar candidatos de uma vaga |
| PUT | /api/candidaturas/:id/status | Atualizar status de candidatura |
| GET | /api/notificacoes/:alunoId | Listar notificações do aluno |
| PUT | /api/notificacoes/:id/lida | Marcar notificação como lida |

---

## Parte do projeto

Este frontend é um dos três componentes do Portal de Estágios UniALFA:

- `api/` — API REST em Node.js + TypeScript (backend principal)
- `java/` — Back-office desktop em Java Swing (uso administrativo)
- `php/` — Interface web (este módulo)
