package gui;

import model.Empresa;
import model.StatusEmpresa;
import services.IEmpresaService;
import errorHandler.GerenciadorExcecoes;
import javax.swing.*;
import java.awt.*;
import java.util.List;

public class TelaEmpresa extends TelaBase implements PainelDefault{

    private final IEmpresaService service;
    private List<Empresa> empresas;

    public TelaEmpresa(IEmpresaService service) {
        super("Gestão de Empresas - UniALFA", 850, 500);
        this.service = service;
        carregarDados();
    }

    @Override
    protected String labelBusca() {
        return "Buscar por nome:";
    }

    @Override
    protected String[] colunas() {
        return new String[]{"ID", "Nome", "CNPJ", "Email", "Telefone", "Área", "Status"};
    }

    @Override
    protected void configurarColunas() {
        tabela.getColumnModel().getColumn(0).setPreferredWidth(40);
        tabela.getColumnModel().getColumn(2).setPreferredWidth(120);
        tabela.getColumnModel().getColumn(6).setPreferredWidth(80);
    }

    @Override
    protected void carregarDados() {
        empresas = service.listar();
        preencherTabela(empresas);
    }

    @Override
    protected void buscar(String termo) {
        if (termo.isBlank()) {
            carregarDados();
            return;
        }
        List<Empresa> filtradas = empresas.stream()
                .filter(e -> e.getNome().toLowerCase().contains(termo.toLowerCase()))
                .toList();
        preencherTabela(filtradas);
    }

    @Override
    protected JPanel montarRodape() {
        JButton btnNova    = new JButton("Nova Empresa");
        JButton btnEditar  = new JButton("Editar");
        JButton btnExcluir = new JButton("Excluir");
        JButton btnStatus  = new JButton("Alternar Status");

        btnNova   .addActionListener(e -> abrirFormulario(null));
        btnEditar .addActionListener(e -> { Empresa s = getEmpresa(); if (s != null) abrirFormulario(s); });
        btnExcluir.addActionListener(e -> excluir());
        btnStatus .addActionListener(e -> alternarStatus());

        JPanel rodape = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 8));
        rodape.add(btnNova);
        rodape.add(btnEditar);
        rodape.add(btnExcluir);
        rodape.add(btnStatus);
        return rodape;
    }

    private void preencherTabela(List<Empresa> lista) {
        modelo.setRowCount(0);
        for (Empresa e : lista) {
            modelo.addRow(new Object[]{
                    e.getId(),
                    e.getNome(),
                    e.getCnpj(),
                    e.getEmail(),
                    e.getTelefone(),
                    e.getAreaAtuacao(),
                    e.isAtiva() ? "✔ Ativa" : "✘ Inativa"
            });
        }
    }

    private Empresa getEmpresa() {
        int id = getIdSelecionado("uma empresa");
        if (id == -1) return null;
        return empresas.stream()
                .filter(e -> e.getId() == id)
                .findFirst()
                .orElse(null);
    }

    private void abrirFormulario(Empresa empresa) {
        boolean editando = empresa != null;

        JTextField     txtNome     = new JTextField(editando ? empresa.getNome()        : "", 20);
        JTextField     txtCnpj     = new JTextField(editando ? empresa.getCnpj()        : "", 20);
        JTextField     txtEmail    = new JTextField(editando ? empresa.getEmail()       : "", 20);
        JTextField     txtTelefone = new JTextField(editando ? empresa.getTelefone()    : "", 20);
        JTextField     txtArea     = new JTextField(editando ? empresa.getAreaAtuacao() : "", 20);
        JPasswordField txtSenha    = new JPasswordField(20); // nunca preenche com hash

        JPanel form = new JPanel(new GridBagLayout());
        painelAdd(form, new JLabel("Nome *:"),   0, 0); painelAdd(form, txtNome,     1, 0);
        painelAdd(form, new JLabel("CNPJ *:"),   0, 1); painelAdd(form, txtCnpj,     1, 1);
        painelAdd(form, new JLabel("Email:"),    0, 2); painelAdd(form, txtEmail,    1, 2);
        painelAdd(form, new JLabel("Telefone:"), 0, 3); painelAdd(form, txtTelefone, 1, 3);
        painelAdd(form, new JLabel("Área *:"),   0, 4); painelAdd(form, txtArea,     1, 4);
        painelAdd(form, new JLabel("Senha" + (editando ? " (deixe vazio para não alterar):" : " *:")), 0, 5);
        painelAdd(form, txtSenha, 1, 5);

        int res = JOptionPane.showConfirmDialog(this, form,
                editando ? "Editar Empresa" : "Nova Empresa",
                JOptionPane.OK_CANCEL_OPTION);

        if (res != JOptionPane.OK_OPTION) return;

        try {
            Empresa e = editando ? empresa : new Empresa();
            e.setNome(txtNome.getText().trim());
            e.setCnpj(txtCnpj.getText().trim());
            e.setEmail(txtEmail.getText().trim());
            e.setTelefone(txtTelefone.getText().trim());
            e.setAreaAtuacao(txtArea.getText().trim());

            String senhaDigitada = new String(txtSenha.getPassword()).trim();

            if (!editando) {
                if (senhaDigitada.isBlank())
                    throw new IllegalArgumentException("Senha é obrigatória para nova empresa.");
                e.setSenha(senhaDigitada);
            } else {
                if (!senhaDigitada.isBlank())
                    e.setSenha(senhaDigitada);
            }

            if (editando) service.editar(e);
            else          service.cadastrar(e);

            carregarDados();
            JOptionPane.showMessageDialog(this, editando ? "Empresa atualizada!" : "Empresa cadastrada!");
        } catch (Exception ex) {
            GerenciadorExcecoes.tratar(this, ex);
        }
    }

    private void excluir() {
        Empresa e = getEmpresa();
        if (e == null) return;

        int confirm = JOptionPane.showConfirmDialog(this,
                "Excluir a empresa \"" + e.getNome() + "\"?",
                "Confirmar", JOptionPane.YES_NO_OPTION);

        if (confirm == JOptionPane.YES_OPTION) {
            service.excluir(e.getId());
            carregarDados();
        }
    }

    private void alternarStatus() {
        Empresa e = getEmpresa();
        if (e == null) return;

        service.alternarStatus(e);
        carregarDados();
        JOptionPane.showMessageDialog(this,
                "Empresa marcada como " + (e.isAtiva() ? "ATIVA" : "INATIVA") + ".");
    }
}