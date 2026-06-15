package gui;

import model.Aluno;
import services.IAlunoService;
import errorHandler.GerenciadorExcecoes;
import javax.swing.*;
import java.awt.*;
import java.util.List;

public class TelaAlunos extends TelaBase implements PainelDefault {

    private final IAlunoService service;
    private List<Aluno> alunos;

    public TelaAlunos(IAlunoService service) {
        super("Gestão de Alunos - UniALFA", 800, 500);
        this.service = service;
        carregarDados();
    }

    @Override
    protected String labelBusca() {
        return "Buscar por nome:";
    }

    @Override
    protected String[] colunas() {
        return new String[]{"ID", "Nome", "RA", "CPF","Email", "Curso", "Apto"};
    }

    @Override
    protected void configurarColunas() {
        tabela.getColumnModel().getColumn(0).setPreferredWidth(40);
        tabela.getColumnModel().getColumn(6).setPreferredWidth(50);
    }

    @Override
    protected void carregarDados() {
        alunos = service.listar();
        preencherTabela(alunos);
    }

    @Override
    protected void buscar(String termo) {
        if (termo.isBlank()) {
            carregarDados();
            return;
        }
        List<Aluno> filtrados = alunos.stream()
                .filter(a -> a.getNome().toLowerCase().contains(termo.toLowerCase()))
                .toList();
        preencherTabela(filtrados);
    }

    @Override
    protected JPanel montarRodape() {
        JButton btnNovo     = new JButton("Novo Aluno");
        JButton btnEditar   = new JButton("Editar");
        JButton btnExcluir  = new JButton("Excluir");
        JButton btnApto     = new JButton("Alternar Aptidão");
        JButton btnBloquear = new JButton("Bloquear");
        JButton btnAtivar   = new JButton("Ativar");
        JButton btnImportar = new JButton("Importar .txt");

        btnNovo    .addActionListener(e -> abrirFormulario(null));
        btnEditar  .addActionListener(e -> { Aluno s = getAlunoSelecionado(); if (s != null) abrirFormulario(s); });
        btnExcluir .addActionListener(e -> excluir());
        btnApto    .addActionListener(e -> alternarAptidao());
        btnImportar.addActionListener(e -> importarTxt());

        btnBloquear.addActionListener(e -> {
            Aluno a = getAlunoSelecionado();
            if (a == null) return;
            service.bloquear(a);
            carregarDados();
            JOptionPane.showMessageDialog(this, "Aluno bloqueado.");
        });

        btnAtivar.addActionListener(e -> {
            Aluno a = getAlunoSelecionado();
            if (a == null) return;
            service.ativar(a);
            carregarDados();
            JOptionPane.showMessageDialog(this, "Aluno ativado.");
        });

        JPanel rodape = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 8));
        rodape.add(btnNovo);
        rodape.add(btnEditar);
        rodape.add(btnExcluir);
        rodape.add(btnApto);
        rodape.add(btnBloquear);
        rodape.add(btnAtivar);
        rodape.add(btnImportar);
        return rodape;
    }

    private void preencherTabela(List<Aluno> lista) {
        modelo.setRowCount(0);
        for (Aluno a : lista) {
            modelo.addRow(new Object[]{
                    a.getId(),
                    a.getNome(),
                    a.getMatricula(),
                    a.getCpf(),
                    a.getEmail(),
                    a.getCurso(),
                    a.isApto() ? "✔ Sim" : "✘ Não"
            });
        }
    }

    private Aluno getAlunoSelecionado() {
        int id = getIdSelecionado("um aluno");
        if (id == -1) return null;
        return alunos.stream()
                .filter(a -> a.getId() == id)
                .findFirst()
                .orElse(null);
    }

    private void abrirFormulario(Aluno aluno) {
        boolean editando = aluno != null;

        JTextField     txtNome      = new JTextField(editando ? aluno.getNome()                    : "", 20);
        JTextField     txtCpf       = new JTextField(editando ? aluno.getCpf()                     : "", 20);
        JTextField     txtMatricula = new JTextField(editando ? aluno.getMatricula()               : "", 20);
        JTextField     txtEmail     = new JTextField(editando ? aluno.getEmail()                   : "", 20);
        JTextField     txtCurso     = new JTextField(editando ? aluno.getCurso()                   : "", 20);
        JTextField     txtPeriodo   = new JTextField(editando ? String.valueOf(aluno.getPeriodo()) : "1", 5);
        JPasswordField txtSenha     = new JPasswordField(20); // nunca preenche com hash

        JPanel form = new JPanel(new GridBagLayout());
        painelAdd(form, new JLabel("Nome *:"),         0, 0); painelAdd(form, txtNome,      1, 0);
        painelAdd(form, new JLabel("CPF:"),            0, 1); painelAdd(form, txtCpf,       1, 1);
        painelAdd(form, new JLabel("Matrícula *:"),    0, 2); painelAdd(form, txtMatricula, 1, 2);
        painelAdd(form, new JLabel("Email:"),          0, 3); painelAdd(form, txtEmail,     1, 3);
        painelAdd(form, new JLabel("Curso:"),          0, 4); painelAdd(form, txtCurso,     1, 4);
        painelAdd(form, new JLabel("Período (1-12):"), 0, 5); painelAdd(form, txtPeriodo,   1, 5);
        painelAdd(form, new JLabel("Senha" + (editando ? " (deixe vazio para não alterar):" : " *:")), 0, 6);
        painelAdd(form, txtSenha, 1, 6);

        int res = JOptionPane.showConfirmDialog(
                this, form,
                editando ? "Editar Aluno" : "Novo Aluno",
                JOptionPane.OK_CANCEL_OPTION);

        if (res != JOptionPane.OK_OPTION) return;

        try {
            Aluno a = editando ? aluno : new Aluno();
            a.setNome(txtNome.getText().trim());
            a.setCpf(txtCpf.getText().trim());
            a.setMatricula(txtMatricula.getText().trim());
            a.setEmail(txtEmail.getText().trim());
            a.setCurso(txtCurso.getText().trim());
            a.setPeriodo(Integer.parseInt(txtPeriodo.getText().trim()));

            String senhaDigitada = new String(txtSenha.getPassword()).trim();

            if (!editando) {
                if (senhaDigitada.isBlank())
                    throw new IllegalArgumentException("Senha é obrigatória para novo aluno.");
                a.setSenha(senhaDigitada);
            } else {
                if (!senhaDigitada.isBlank())
                    a.setSenha(senhaDigitada);
            }

            if (editando) service.editar(a);
            else          service.cadastrar(a);

            carregarDados();
            JOptionPane.showMessageDialog(this, editando ? "Aluno atualizado!" : "Aluno cadastrado!");
        } catch (Exception ex) {
            GerenciadorExcecoes.tratar(this, ex);
        }
    }

    private void excluir() {
        Aluno a = getAlunoSelecionado();
        if (a == null) return;

        int confirm = JOptionPane.showConfirmDialog(
                this,
                "Excluir o aluno \"" + a.getNome() + "\"?",
                "Confirmar",
                JOptionPane.YES_NO_OPTION);

        if (confirm == JOptionPane.YES_OPTION) {
            service.excluir(a.getId());
            carregarDados();
        }
    }

    private void alternarAptidao() {
        Aluno a = getAlunoSelecionado();
        if (a == null) return;

        boolean novoEstado = !a.isApto();
        service.alternarAptidao(a);
        carregarDados();

        JOptionPane.showMessageDialog(this,
                "Aluno marcado como " + (novoEstado ? "APTO" : "INAPTO") + ".");
    }

    private void importarTxt() {
        JFileChooser chooser = new JFileChooser();
        if (chooser.showOpenDialog(this) != JFileChooser.APPROVE_OPTION) return;

        try {
            int qtd = service.importarDeTxt(chooser.getSelectedFile().getAbsolutePath());
            carregarDados();
            JOptionPane.showMessageDialog(this, qtd + " aluno(s) importado(s)!");
        } catch (Exception ex) {
            GerenciadorExcecoes.tratar(this, ex);
        }
    }
}