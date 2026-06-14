<?php
session_start();

require_once 'shared/ApiClient.php';
require_once 'shared/EmpresaService.php';


$erro = null;
$tipoAtivo = $_POST['tipo'] ?? 'aluno';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new ApiClient();

    if ($_POST['tipo'] === 'empresa') {
        $service = new EmpresaService($api);
        $empresa = $service->login($_POST['email'], $_POST['senha']);

        if ($empresa) {
            $_SESSION['empresaId'] = $empresa['id'];
            $_SESSION['empresaNome'] = $empresa['nome'];
            header('Location: empresa/index.php');
            exit;
        } else {
            $erro = 'Email ou senha inválidos';
        }
    } else {

        $erro = 'Login de aluno ainda não disponível';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Estágios UniALFA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f1ec;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
        }
        .nav-tabs .nav-link {
            font-weight: 600;
        }
        .nav-tabs .nav-link.active {
            background-color: #1a1a1a;
            color: #fff;
            border-color: #1a1a1a;
        }
        .btn-entrar {
            background-color: #1a1a1a;
            border-color: #1a1a1a;
        }
        .btn-entrar:hover {
            background-color: #333;
            border-color: #333;
        }
    </style>
</head>
<body>
    <div class="card login-card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <h1 class="h4 fw-bold mb-1">Bem-vindo de volta</h1>
            <p class="text-muted small mb-4">Escolha seu tipo de acesso para continuar</p>

            <?php if ($erro): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" id="loginForm">

                <ul class="nav nav-tabs nav-fill mb-4" id="tipoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button"
                                class="nav-link <?= $tipoAtivo === 'aluno' ? 'active' : '' ?>"
                                id="tab-aluno"
                                onclick="selecionarTipo('aluno')">
                            Aluno
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button"
                                class="nav-link <?= $tipoAtivo === 'empresa' ? 'active' : '' ?>"
                                id="tab-empresa"
                                onclick="selecionarTipo('empresa')">
                            Empresa
                        </button>
                    </li>
                </ul>

                <input type="hidden" name="tipo" id="tipoInput" value="<?= htmlspecialchars($tipoAtivo) ?>">

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control"
                           placeholder="seuemail@email.com" required>
                </div>

                <div class="mb-4">
                    <label for="senha" class="form-label fw-semibold small">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control"
                           placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-entrar w-100 text-white fw-bold">Entrar</button>
            </form>
        </div>
    </div>

    <script>
        function selecionarTipo(tipo) {
            document.getElementById('tipoInput').value = tipo;
            document.getElementById('tab-aluno').classList.toggle('active', tipo === 'aluno');
            document.getElementById('tab-empresa').classList.toggle('active', tipo === 'empresa');
        }
    </script>
</body>
</html>
