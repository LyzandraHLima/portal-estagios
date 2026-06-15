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

$vagas = $service->listarVagas();
$naoLidas = count(array_filter($service->listarNotificacoes($alunoId), fn($n) => !$n['lida']));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vagas Disponíveis - Portal de Estágios UniALFA</title>
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
        .card-vaga {
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
        <h1 class="h3 fw-bold mb-1">Vagas Disponíveis</h1>
        <p class="text-muted mb-4">Confira as oportunidades de estágio abertas</p>

        <?php if (empty($vagas)): ?>
            <p class="text-muted">Nenhuma vaga disponível no momento.</p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($vagas as $vaga): ?>
                    <div class="col-md-6">
                        <div class="card card-vaga">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($vaga->titulo) ?></h5>
                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars($vaga->area) ?> ·
                                    <?= htmlspecialchars($vaga->modalidade) ?> ·
                                    <?= htmlspecialchars((string) $vaga->carga_horaria) ?>h
                                </p>
                                <p class="card-text"><?= nl2br(htmlspecialchars($vaga->descricao)) ?></p>

                                <span class="badge bg-secondary mb-3"><?= htmlspecialchars($vaga->status) ?></span>

                                <div>
                                    <a href="candidatar.php?vagaId=<?= $vaga->id ?>" class="btn btn-sm btn-dark-custom">Candidatar-se</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>