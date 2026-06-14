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

if (!isset($_GET['vagaId'])) {
    header('Location: vagas.php');
    exit;
}

$vagaId = (int) $_GET['vagaId'];
$mensagem = null;

// Trata atualização de status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidaturaId = (int) $_POST['candidaturaId'];
    $novoStatus = $_POST['status'];

    $resultado = $service->atualizarStatusCandidatura($candidaturaId, $novoStatus);
    $mensagem = $resultado ? 'Status atualizado com sucesso!' : 'Erro ao atualizar status.';
}

$vaga = $service->buscarVaga($vagaId);
$candidatos = $service->listarCandidatos($vagaId);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos - Portal de Estágios UniALFA</title>
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
        .card-candidato {
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
        .badge-status {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand navbar-custom px-4">
        <span class="navbar-brand mb-0 h1">Portal de Estágios UniALFA</span>
        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="vagas.php" class="nav-link">Minhas Vagas</a>
            <span class="nav-link mb-0"><?= htmlspecialchars($empresaNome) ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light">Sair</a>
        </div>
    </nav>

    <div class="container py-5">
        <a href="vagas.php" class="text-decoration-none small">&larr; Voltar para Minhas Vagas</a>

        <h1 class="h3 fw-bold mt-2 mb-1">Candidatos</h1>
        <p class="text-muted mb-4">
            Vaga: <strong><?= htmlspecialchars($vaga->titulo ?? '') ?></strong>
        </p>

        <?php if ($mensagem): ?>
            <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <?php if (empty($candidatos)): ?>
            <p class="text-muted">Nenhum candidato se inscreveu nesta vaga ainda.</p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($candidatos as $candidatura): ?>
                    <div class="col-md-6">
                        <div class="card card-candidato">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">
                                    <?= htmlspecialchars($candidatura['aluno']['nome'] ?? 'Aluno') ?>
                                </h5>
                                <p class="card-text text-muted small mb-2">
                                    <?= htmlspecialchars($candidatura['aluno']['email'] ?? '') ?>
                                </p>

                                <?php if (!empty($candidatura['observacao'])): ?>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($candidatura['observacao'])) ?></p>
                                <?php endif; ?>

                                <span class="badge badge-status bg-secondary mb-3">
                                    <?= htmlspecialchars($candidatura['status']) ?>
                                </span>

                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="candidaturaId" value="<?= $candidatura['id'] ?>">
                                    <select name="status" class="form-select form-select-sm w-auto">
                                        <?php foreach (['pendente', 'aprovada', 'rejeitada'] as $opcao): ?>
                                            <option value="<?= $opcao ?>" <?= $candidatura['status'] === $opcao ? 'selected' : '' ?>>
                                                <?= ucfirst($opcao) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-dark-custom">Atualizar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
