package services;

import dao.EmpresaDAO;
import model.Empresa;
import utils.SenhaUtil;
import java.sql.SQLException;
import java.util.List;

public class EmpresaService implements IEmpresaService {

    private final EmpresaDAO dao;

    public EmpresaService(EmpresaDAO dao) {
        this.dao = dao;
    }

    @Override
    public List<Empresa> listar() {
        try {
            return dao.listarTodos();
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao listar empresas.", e);
        }
    }

    @Override
    public List<Empresa> buscar(String termo) {
        if (termo == null || termo.isBlank()) return listar();
        try {
            return dao.buscarPorNome(termo.trim().toLowerCase());
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao buscar empresas.", e);
        }
    }

    @Override
    public Empresa buscarPorId(int id) {
        try {
            return dao.buscarPorId(id)
                    .orElseThrow(() -> new IllegalArgumentException("Empresa não encontrada: " + id));
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao buscar empresa.", e);
        }
    }

    @Override
    public void cadastrar(Empresa empresa) {
        validar(empresa);
        if (empresa.getSenha() == null || empresa.getSenha().isBlank())
            throw new IllegalArgumentException("Senha é obrigatória.");
        empresa.setSenha(SenhaUtil.criptografar(empresa.getSenha())); // 👈 hash aqui
        try {
            dao.inserir(empresa);
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao cadastrar empresa.", e);
        }
    }

    @Override
    public void editar(Empresa empresa) {
        validar(empresa);
        if (empresa.getSenha() != null && !empresa.getSenha().startsWith("$2")) {
            empresa.setSenha(SenhaUtil.criptografar(empresa.getSenha())); // 👈 só recriptografa se for senha nova
        }
        try {
            dao.atualizar(empresa);
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao editar empresa.", e);
        }
    }

    @Override
    public void excluir(int id) {
        try {
            dao.excluir(id);
        } catch (SQLException e) {
            throw new RuntimeException("Erro ao excluir empresa.", e);
        }
    }

    @Override
    public void alternarStatus(Empresa empresa) {
        empresa.alternarStatus();
        editar(empresa);
    }

    private void validar(Empresa e) {
        if (e.getNome() == null || e.getNome().isBlank())
            throw new IllegalArgumentException("Nome é obrigatório.");
        if (e.getCnpj() == null || e.getCnpj().isBlank())
            throw new IllegalArgumentException("CNPJ é obrigatório.");
        if (e.getAreaAtuacao() == null || e.getAreaAtuacao().isBlank())
            throw new IllegalArgumentException("Área de atuação é obrigatória.");
    }
}