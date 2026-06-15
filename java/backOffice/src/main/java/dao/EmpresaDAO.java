package dao;

import model.Empresa;
import model.StatusEmpresa;
import java.sql.*;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

public class EmpresaDAO implements IGenericDAO<Empresa> {

    private final Connection connection;

    public EmpresaDAO(Connection connection) {
        this.connection = connection;
    }

    @Override
    public List<Empresa> listarTodos() throws SQLException {
        List<Empresa> lista = new ArrayList<>();
        String sql = "SELECT * FROM empresa ORDER BY nome";

        try (PreparedStatement ps = connection.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) lista.add(mapear(rs));
        }
        return lista;
    }

    public List<Empresa> buscarPorNome(String termo) throws SQLException {
        List<Empresa> lista = new ArrayList<>();
        String sql = "SELECT * FROM empresa WHERE LOWER(nome) LIKE ? ORDER BY nome";

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setString(1, "%" + termo + "%");
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) lista.add(mapear(rs));
            }
        }
        return lista;
    }

    @Override
    public Optional<Empresa> buscarPorId(int id) throws SQLException {
        String sql = "SELECT * FROM empresa WHERE id = ?";

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setInt(1, id);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) return Optional.of(mapear(rs));
            }
        }
        return Optional.empty();
    }

    @Override
    public void inserir(Empresa empresa) throws SQLException {
        String sql = """
                INSERT INTO empresa (nome, cnpj, email, telefone, area_atuacao, status, senha, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                """;

        try (PreparedStatement ps = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            preencherStatement(ps, empresa);
            ps.setString(7, empresa.getSenha());                        // senha posição 7
            ps.setTimestamp(8, Timestamp.valueOf(LocalDateTime.now())); // created_at posição 8
            ps.executeUpdate();

            try (ResultSet keys = ps.getGeneratedKeys()) {
                if (keys.next()) empresa.setId(keys.getInt(1));
            }
        }
    }

    @Override
    public void atualizar(Empresa empresa) throws SQLException {
        String sql = """
                UPDATE empresa
                SET nome = ?, cnpj = ?, email = ?, telefone = ?, area_atuacao = ?, status = ?, senha = ?
                WHERE id = ?
                """;

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            preencherStatement(ps, empresa);
            ps.setString(7, empresa.getSenha()); // senha posição 7
            ps.setInt(8, empresa.getId());       // id posição 8
            ps.executeUpdate();
        }
    }

    @Override
    public void excluir(int id) throws SQLException {
        String sql = "DELETE FROM empresa WHERE id = ?";

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setInt(1, id);
            ps.executeUpdate();
        }
    }

    private void preencherStatement(PreparedStatement ps, Empresa e) throws SQLException {
        ps.setString(1, e.getNome());
        ps.setString(2, e.getCnpj());
        ps.setString(3, e.getEmail());
        ps.setString(4, e.getTelefone());
        ps.setString(5, e.getAreaAtuacao());
        ps.setString(6, e.getStatus().name());
        // posições 7 e 8 preenchidas fora daqui
    }

    private Empresa mapear(ResultSet rs) throws SQLException {
        return Empresa.reconstituir(
                rs.getInt("id"),
                rs.getString("nome"),
                rs.getString("cnpj"),
                rs.getString("email"),
                rs.getString("telefone"),
                rs.getString("area_atuacao"),
                StatusEmpresa.valueOf(rs.getString("status")),
                rs.getTimestamp("created_at").toLocalDateTime(),
                rs.getString("senha") // 👈 novo
        );
    }
}