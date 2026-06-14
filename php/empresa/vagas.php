<?php
session_start();

if (!isset($_SESSION['empresaId'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../shared/ApiClient.php';
require_once '../shared/EmpresaService.php';

$api = new ApiClient();
$service = new EmpresaService($api);

$empresaId = $_SESSION['empresaId'];
$empresaNome = $_SESSION['empresaNome'];

$mensagem = null;

if (isset($_GET['excluir'])) {
    $vagaId = (int) $_GET['excluir'];
    $sucesso = $service->deletarVaga($vagaId);
    $mensagem = $sucesso ? 'Vaga removida com sucesso!' : 'Erro ao remover a vaga.';
}

$vagas = $service->listarVagas($empresaId);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Vagas - Portal de Estágios UniALFA</title>
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand navbar-custom px-4">
        <span class="navbar-brand mb-0 h1">Portal de Estágios UniALFA</span>
        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="index.php" class="nav-link">Dashboard</a>
            <span class="nav-link mb-0"><?= htmlspecialchars($empresaNome) ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light">Sair</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0">Minhas Vagas</h1>
            <a href="vaga_form.php" class="btn btn-dark-custom">+ Nova Vaga</a>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <?php if (empty($vagas)): ?>
            <p class="text-muted">Nenhuma vaga cadastrada ainda.</p>
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

                                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($vaga->status) ?></span>

                                <div class="d-flex gap-2 mt-2">
                                    <a href="vaga_form.php?id=<?= $vaga->id ?>" class="btn btn-sm btn-outline-dark">Editar</a>
                                    <a href="candidatos.php?vagaId=<?= $vaga->id ?>" class="btn btn-sm btn-outline-dark">Candidatos</a>
                                    <a href="vagas.php?excluir=<?= $vaga->id ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Tem certeza que deseja remover esta vaga?')">Excluir</a>
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