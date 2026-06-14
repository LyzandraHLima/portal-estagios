package model;

import utils.Validador;
import java.time.LocalDateTime;

public class Aluno {


    private static final int PERIODO_MIN =  1;
    private static final int PERIODO_MAX = 12;


    private int id;
    private String nome;
    private String email;
    private String cpf;
    private String matricula;
    private String curso;
    private int periodo;
    private boolean apto;
    private StatusAluno status;
    private LocalDateTime createdAt;
    private String senha;


    public Aluno() {
        this.createdAt = LocalDateTime.now();
        this.status    = StatusAluno.ATIVO;
        this.apto      = true;
    }
    public static Aluno reconstituir(int id, String nome, String email, String cpf,
                                     String matricula, String curso, int periodo,
                                     boolean apto, StatusAluno status,
                                     LocalDateTime createdAt, String senha) {
        Aluno a    = new Aluno();
        a.id       = id;
        a.nome     = nome;
        a.email    = email;
        a.cpf      = cpf;
        a.matricula = matricula;
        a.curso    = curso;
        a.periodo  = periodo;
        a.apto     = apto;
        a.status   = status;
        a.createdAt = createdAt;
        a.senha = senha;
        return a;
    }

    public void alternarAptidao() {
        this.apto = !this.apto;
    }

    public void bloquear() {
        this.status = StatusAluno.INATIVO;
        this.apto   = false;
    }

    public void ativar() {
        this.status = StatusAluno.ATIVO;
    }

    public boolean isAtivo() {
        return this.status == StatusAluno.ATIVO;
    }

    public int getId()              { return id; }
    public String getNome()         { return nome; }
    public String getEmail()        { return email; }
    public String getCpf()          { return cpf; }
    public String getMatricula()    { return matricula; }
    public String getCurso()        { return curso; }
    public int getPeriodo()         { return periodo; }
    public boolean isApto()         { return apto; }
    public StatusAluno getStatus()  { return status; }
    public LocalDateTime getCreatedAt() { return createdAt; }
    public String getSenha()        {return senha;}

    public void setId(int id)           { this.id = id; }
    public void setNome(String nome) {
        Validador.exigirNaoVazio(nome, "Nome");
        this.nome = nome.trim();
    }

    public void setEmail(String email) {
        if (email != null && !email.isBlank())
            Validador.exigirEmail(email);
        this.email = email;
    }

    public void setCpf(String cpf) {
        this.cpf = cpf;
    }

    public void setMatricula(String matricula) {
        Validador.exigirNaoVazio(matricula, "Matrícula");
        this.matricula = matricula.trim();
    }

    public void setCurso(String curso) {
        this.curso = curso;
    }

    public void setPeriodo(int periodo) {
        Validador.exigirIntervalo(periodo, PERIODO_MIN, PERIODO_MAX, "Período");
        this.periodo = periodo;
    }

    public void setApto(boolean apto)        { this.apto = apto; }

    public void setStatus(StatusAluno status) {
        Validador.exigirNaoVazio(status != null ? status.name() : null, "Status");
        this.status = status;
    }
    public void setSenha(String senha){
        Validador.exigirNaoVazio(senha, "senha");
        this.senha = senha;
    }

    @Override
    public String toString() {
        return String.format(
                "Aluno{id=%d, nome='%s', matricula='%s', curso='%s', status=%s, apto=%b}",
                id, nome, matricula, curso, status, apto);
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof Aluno)) return false;
        Aluno outro = (Aluno) o;
        return matricula != null && matricula.equals(outro.matricula);
    }
    @Override
    public int hashCode() {
        return matricula != null ? matricula.hashCode() : 0;
    }
}