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

$vagas = $service->listarVagas($empresaId);
$totalVagas = is_array($vagas) ? count($vagas) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Empresa - Portal de Estágios UniALFA</title>
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
        .card-stat {
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
            <span class="nav-link mb-0"><?= htmlspecialchars($empresaNome) ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light">Sair</a>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="h3 fw-bold mb-1">Olá, <?= htmlspecialchars($empresaNome) ?> </h1>
        <p class="text-muted mb-4">Aqui está um resumo do seu painel</p>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-stat">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Vagas publicadas</p>
                        <h2 class="fw-bold mb-0"><?= $totalVagas ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="vagas.php" class="btn btn-dark-custom">Minhas Vagas</a>
            <a href="vaga_form.php" class="btn btn-outline-dark">Nova Vaga</a>
        </div>
    </div>
</body>
</html>
