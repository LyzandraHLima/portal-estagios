package model;
import utils.Validador;
import java.time.LocalDateTime;

public class Empresa {

    private int id;
    private String nome;
    private String cnpj;
    private String email;
    private String telefone;
    private String areaAtuacao;
    private StatusEmpresa status;
    private final LocalDateTime createdAt;
    private String senha;

    public Empresa() {
        this.createdAt = LocalDateTime.now();
        this.status    = StatusEmpresa.ATIVA;
    }

    private Empresa(int id, String nome, String cnpj, String email,
                    String telefone, String areaAtuacao,
                    StatusEmpresa status, LocalDateTime createdAt, String senha) {
        this.id          = id;
        this.nome        = nome;
        this.cnpj        = cnpj;
        this.email       = email;
        this.telefone    = telefone;
        this.areaAtuacao = areaAtuacao;
        this.status      = status;
        this.createdAt   = createdAt;
    }

    public static Empresa reconstituir(int id, String nome, String cnpj, String email,
                                       String telefone, String areaAtuacao,
                                       StatusEmpresa status, LocalDateTime createdAt, String senha) {
        return new Empresa(id, nome, cnpj, email, telefone, areaAtuacao, status, createdAt, senha);
    }

    public void ativar() {
        this.status = StatusEmpresa.ATIVA;
    }

    public void inativar() {
        this.status = StatusEmpresa.INATIVA;
    }

    public void alternarStatus() {
        this.status = isAtiva() ? StatusEmpresa.INATIVA : StatusEmpresa.ATIVA;
    }

    public boolean isAtiva() {
        return this.status == StatusEmpresa.ATIVA;
    }


    public int getId()              { return id; }
    public String getNome()         { return nome; }
    public String getCnpj()         { return cnpj; }
    public String getEmail()        { return email; }
    public String getTelefone()     { return telefone; }
    public String getAreaAtuacao()  { return areaAtuacao; }
    public StatusEmpresa getStatus(){ return status; }
    public LocalDateTime getCreatedAt() { return createdAt; }
    public String getSenha() { return senha;}

    public void setId(int id)           { this.id = id; }
    public void setNome(String nome) {
        Validador.exigirNaoVazio(nome, "Nome");
        this.nome = nome.trim();
    }

    public void setCnpj(String cnpj) {
        Validador.exigirNaoVazio(cnpj, "CNPJ");
        this.cnpj = cnpj.trim();
    }

    public void setEmail(String email) {
        if (email != null && !email.isBlank())
            Validador.exigirEmail(email);
        this.email = email;
    }

    public void setTelefone(String telefone) {
        this.telefone = telefone;
    }

    public void setAreaAtuacao(String areaAtuacao) {
        Validador.exigirNaoVazio(areaAtuacao, "Área de atuação");
        this.areaAtuacao = areaAtuacao.trim();
    }

    public void setStatus(StatusEmpresa status) {
        if (status == null) throw new IllegalArgumentException("Status não pode ser nulo.");
        this.status = status;
    }

    public void setSenha(String senha) {
        Validador.exigirNaoVazio(senha, "Senha");
        this.senha = senha;
    }

    @Override
    public String toString() {
        return String.format(
                "Empresa{id=%d, nome='%s', cnpj='%s', status=%s}",
                id, nome, cnpj, status);
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof Empresa)) return false;
        Empresa outro = (Empresa) o;
        return cnpj != null && cnpj.equals(outro.cnpj);
    }

    @Override
    public int hashCode() {
        return cnpj != null ? cnpj.hashCode() : 0;
    }
}