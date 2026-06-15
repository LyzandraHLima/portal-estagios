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

if (isset($_GET['lida'])) {
    $service->marcarNotificacaoComoLida((int) $_GET['lida']);
}

$notificacoes = $service->listarNotificacoes($alunoId);
$naoLidas = count(array_filter($notificacoes, fn($n) => !$n['lida']));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificações - Portal de Estágios UniALFA</title>
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
        .card-notif {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .card-notif.nao-lida {
            border-left: 4px solid #1a1a1a;
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
        <h1 class="h3 fw-bold mb-4">Notificações</h1>

        <?php if (empty($notificacoes)): ?>
            <p class="text-muted">Você não tem notificações.</p>
        <?php else: ?>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($notificacoes as $notificacao): ?>
                    <?php $naoLida = !$notificacao['lida']; ?>
                    <div class="card card-notif <?= $naoLida ? 'nao-lida' : '' ?>">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <p class="card-text mb-0"><?= htmlspecialchars($notificacao['mensagem']) ?></p>
                            <?php if ($naoLida): ?>
                                <a href="notificacoes.php?lida=<?= $notificacao['id'] ?>" class="btn btn-sm btn-outline-dark">Marcar como lida</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>