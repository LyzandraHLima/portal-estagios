package services;

import dao.AlunoDAO;
import model.Aluno;
import utils.SenhaUtil;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.sql.SQLException;
import java.util.List;

public class AlunoService implements IAlunoService {

    private final AlunoDAO dao;

    public AlunoService(AlunoDAO dao) {
        this.dao = dao;
    }

    @Override
    public List<Aluno> listar() {
        try {
            return dao.listarTodos();
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao listar alunos.", e);
        }
    }

    @Override
    public List<Aluno> buscar(String termo) {
        if (termo == null || termo.isBlank()) return listar();
        try {
            return dao.buscarPorNome(termo.trim().toLowerCase());
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao buscar alunos.", e);
        }
    }

    @Override
    public Aluno buscarPorId(int id) {
        try {
            return dao.buscarPorId(id)
                    .orElseThrow(() -> new IllegalArgumentException("Aluno não encontrado: " + id));
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao buscar aluno.", e);
        }
    }

    @Override
    public void cadastrar(Aluno aluno) {
        validar(aluno);
        if (aluno.getSenha() == null || aluno.getSenha().isBlank())
            throw new IllegalArgumentException("Senha é obrigatória.");
        aluno.setSenha(SenhaUtil.criptografar(aluno.getSenha()));
        try {
            dao.inserir(aluno);
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao cadastrar aluno.", e);
        }
    }

    @Override
    public void editar(Aluno aluno) {
        validar(aluno);
        if (aluno.getSenha() !=null && !aluno.getSenha().startsWith("$2")){
            aluno.setSenha(SenhaUtil.criptografar((aluno.getSenha())));
        }
        try {
            dao.atualizar(aluno);
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao editar aluno.", e);
        }
    }

    @Override
    public void excluir(int id) {
        try {
            dao.excluir(id);
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao excluir aluno.", e);
        }
    }

    @Override
    public void bloquear(Aluno aluno) {
        aluno.bloquear();
        editar(aluno);
    }

    @Override
    public void ativar(Aluno aluno) {
        aluno.ativar();
        editar(aluno);
    }

    @Override
    public void alternarAptidao(Aluno aluno) {
        aluno.alternarAptidao(); // comportamento no próprio modelo — SRP
        editar(aluno);
    }

    @Override
    public int importarDeTxt(String caminho) throws IOException {
        int contador = 0;
        try (BufferedReader br = new BufferedReader(new FileReader(caminho))) {
            String linha;
            while ((linha = br.readLine()) != null) {
                linha = linha.trim();
                if (linha.isBlank()) continue;

                String[] partes = linha.split(";");
                if (partes.length < 3) continue;

                Aluno a = new Aluno();
                a.setNome(partes[0].trim());
                a.setMatricula(partes[1].trim());
                a.setCurso(partes[2].trim());
                if (partes.length > 3) a.setPeriodo(Integer.parseInt(partes[3].trim()));
                if (partes.length > 4) a.setEmail(partes[4].trim());
                if (partes.length > 5) a.setSenha(partes[5].trim());
                else                   a.setSenha(partes[1].trim());

                cadastrar(a);
                contador++;
            }
        }
        return contador;
    }

    private void validar(Aluno a) {
        if (a.getNome() == null || a.getNome().isBlank())
            throw new IllegalArgumentException("Nome é obrigatório.");
        if (a.getMatricula() == null || a.getMatricula().isBlank())
            throw new IllegalArgumentException("Matrícula é obrigatória.");
    }
}