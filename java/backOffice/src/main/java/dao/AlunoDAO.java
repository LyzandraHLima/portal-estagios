package dao;

import model.Aluno;
import model.StatusAluno;
import java.sql.*;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

public class AlunoDAO implements IGenericDAO<Aluno> {

    private final Connection connection;

    public AlunoDAO(Connection connection) {
        this.connection = connection;
    }

    @Override
    public List<Aluno> listarTodos() throws SQLException {
        List<Aluno> lista = new ArrayList<>();
        String sql = "SELECT * FROM aluno ORDER BY nome";

        try (PreparedStatement ps = connection.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                lista.add(mapear(rs));
            }
        }
        return lista;
    }

    public List<Aluno> buscarPorNome(String termo) throws SQLException {
        List<Aluno> lista = new ArrayList<>();
        String sql = "SELECT * FROM aluno WHERE LOWER(nome) LIKE ? ORDER BY nome";

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setString(1, "%" + termo + "%");
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    lista.add(mapear(rs));
                }
            }
        }
        return lista;
    }

    @Override
    public Optional<Aluno> buscarPorId(int id) throws SQLException {
        String sql = "SELECT * FROM aluno WHERE id = ?";

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setInt(1, id);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) return Optional.of(mapear(rs));
            }
        }
        return Optional.empty();
    }

    @Override
    public void inserir(Aluno aluno) throws SQLException {
        String sql = """
                INSERT INTO aluno (nome, email, cpf, matricula, curso, periodo, apto, status, senha, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                """;

        try (PreparedStatement ps = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            preencherStatement(ps, aluno);
            ps.setString(9, aluno.getSenha());               // senha na posição 9
            ps.setTimestamp(10, Timestamp.valueOf(LocalDateTime.now())); // created_at na posição 10
            ps.executeUpdate();

            try (ResultSet keys = ps.getGeneratedKeys()) {
                if (keys.next()) aluno.setId(keys.getInt(1));
            }
        }
    }

    @Override
    public void atualizar(Aluno aluno) throws SQLException {
        String sql = """
                UPDATE aluno
                SET nome = ?, email = ?, cpf = ?, matricula = ?, curso = ?, periodo = ?, apto = ?, status = ?, senha = ?
                WHERE id = ?
                """;

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            preencherStatement(ps, aluno);
            ps.setString(9, aluno.getSenha()); // senha na posição 9
            ps.setInt(10, aluno.getId());      // id na posição 10
            ps.executeUpdate();
        }
    }

    @Override
    public void excluir(int id) throws SQLException {
        String sql = "DELETE FROM aluno WHERE id = ?";

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setInt(1, id);
            ps.executeUpdate();
        }
    }

    // — helpers —

    private void preencherStatement(PreparedStatement ps, Aluno a) throws SQLException {
        ps.setString(1, a.getNome());
        ps.setString(2, a.getEmail());
        ps.setString(3, a.getCpf());
        ps.setString(4, a.getMatricula());
        ps.setString(5, a.getCurso());
        ps.setInt   (6, a.getPeriodo());
        ps.setBoolean(7, a.isApto());
        ps.setString(8, a.getStatus().name());
        // posições 9 e 10 são preenchidas fora daqui (variam por método)
    }

    private Aluno mapear(ResultSet rs) throws SQLException {
        return Aluno.reconstituir(
                rs.getInt("id"),
                rs.getString("nome"),
                rs.getString("email"),
                rs.getString("cpf"),
                rs.getString("matricula"),
                rs.getString("curso"),
                rs.getInt("periodo"),
                rs.getBoolean("apto"),
                StatusAluno.valueOf(rs.getString("status")),
                rs.getTimestamp("created_at").toLocalDateTime(),
                rs.getString("senha") // minúsculo — igual ao nome da coluna no banco
        );
    }
}