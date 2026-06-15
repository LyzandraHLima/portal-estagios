<?php
session_start();

if (!isset($_SESSION['alunoId'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../shared/ApiClient.php';
require_once '../shared/AlunoService.php';

$api = new ApiClient();
$service = new AlunoService($api);

$alunoId = $_SESSION['alunoId'];
$alunoNome = $_SESSION['alunoNome'];

if (!isset($_GET['vagaId'])) {
    header('Location: index.php');
    exit;
}

$vagaId = (int) $_GET['vagaId'];
$erro = null;
$sucesso = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'aluno_id'   => $alunoId,
        'vaga_id'    => $vagaId,
        'observacao' => $_POST['observacao'],
    ];

    $resultado = $service->candidatar($dados);

    if ($resultado) {
        $sucesso = 'Candidatura enviada com sucesso!';
    } else {
        $erro = 'Erro ao enviar candidatura. Tente novamente.';
    }
}

$vaga = $service->buscarVaga($vagaId);
$naoLidas = count(array_filter($service->listarNotificacoes($alunoId), fn($n) => !$n['lida']));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatar-se - Portal de Estágios UniALFA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f3f1ec;
        }
        .navbar-custom {
            background: #1a1a1a;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #fff !important;
        }
        .card-form {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .btn-dark-custom {
            background-color: #1a1a1a;
            border-color: #1a1a1a;
            color: #fff;
        }
        .btn-dark-custom:hover {
            background-color: #333;
            border-color: #333;
            color: #fff;
        }
        .badge-sino {
            position: relative;
            top: -8px;
            left: -6px;
            font-size: 0.65rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand navbar-custom px-4">
        <span class="navbar-brand mb-0 h1">Portal de Estágios UniALFA</span>
        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="index.php" class="nav-link">Vagas</a>
            <a href="minhas_candidaturas.php" class="nav-link">Minhas Candidaturas</a>
            <a href="notificacoes.php" class="nav-link position-relative">
                🔔
                <?php if ($naoLidas > 0): ?>
                    <span class="badge bg-danger badge-sino"><?= $naoLidas ?></span>
                <?php endif; ?>
            </a>
            <span class="nav-link mb-0"><?= htmlspecialchars($alunoNome) ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light">Sair</a>
        </div>
    </nav>

    <div class="container py-5">
        <a href="index.php" class="text-decoration-none small">&larr; Voltar para Vagas</a>

        <h1 class="h3 fw-bold mt-2 mb-4">Candidatar-se</h1>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <?php if ($vaga): ?>
            <div class="card card-form mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($vaga->titulo) ?></h5>
                    <p class="card-text text-muted small">
                        <?= htmlspecialchars($vaga->area) ?> ·
                        <?= htmlspecialchars($vaga->modalidade) ?> ·
                        <?= htmlspecialchars((string) $vaga->carga_horaria) ?>h
                    </p>
                    <p class="card-text"><?= nl2br(htmlspecialchars($vaga->descricao)) ?></p>
                    <p class="card-text"><strong>Requisitos:</strong> <?= nl2br(htmlspecialchars($vaga->requisitos)) ?></p>
                </div>
            </div>

            <?php if (!$sucesso): ?>
                <div class="card card-form">
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Mensagem para a empresa (opcional)</label>
                                <textarea name="observacao" class="form-control" rows="4" placeholder="Conte um pouco sobre seu interesse nessa vaga..."></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-dark-custom">Enviar Candidatura</button>
                                <a href="index.php" class="btn btn-outline-dark">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-muted">Vaga não encontrada.</p>
        <?php endif; ?>
    </div>
</body>
</html>