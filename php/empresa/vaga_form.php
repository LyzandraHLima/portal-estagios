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

$editando = isset($_GET['id']);
$vaga = null;
$erro = null;

if ($editando) {
    $vaga = $service->buscarVaga((int) $_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'empresa_id'     => $empresaId,
        'titulo'        => $_POST['titulo'],
        'descricao'     => $_POST['descricao'],
        'area'          => $_POST['area'],
        'requisitos'    => $_POST['requisitos'],
        'carga_horaria' => (float) $_POST['carga_horaria'],
        'modalidade'    => $_POST['modalidade'],
    ];

    if ($editando) {
        $resultado = $service->editarVaga((int) $_POST['id'], $dados);
    } else {
        $resultado = $service->criarVaga($dados);
    }

    if ($resultado) {
        header('Location: vagas.php');
        exit;
    } else {
        $erro = 'Erro ao salvar a vaga. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editando ? 'Editar Vaga' : 'Nova Vaga' ?> - Portal de Estágios UniALFA</title>
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
        <h1 class="h3 fw-bold mb-4"><?= $editando ? 'Editar Vaga' : 'Nova Vaga' ?></h1>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div class="card card-form">
            <div class="card-body p-4">
                <form method="POST">
                    <?php if ($editando && $vaga): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars((string) $vaga->id) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título</label>
                        <input type="text" name="titulo" class="form-control"
                               value="<?= htmlspecialchars($vaga->titulo ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="4" required><?= htmlspecialchars($vaga->descricao ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Área</label>
                            <input type="text" name="area" class="form-control"
                                   value="<?= htmlspecialchars($vaga->area ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Modalidade</label>
                            <select name="modalidade" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php foreach (['presencial', 'remoto', 'híbrido'] as $opcao): ?>
                                    <option value="<?= $opcao ?>" <?= (isset($vaga->modalidade) && $vaga->modalidade === $opcao) ? 'selected' : '' ?>>
                                        <?= ucfirst($opcao) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Requisitos</label>
                        <textarea name="requisitos" class="form-control" rows="3" required><?= htmlspecialchars($vaga->requisitos ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Carga Horária (semanal, em horas)</label>
                        <input type="number" step="0.5" name="carga_horaria" class="form-control"
                               value="<?= htmlspecialchars((string) ($vaga->carga_horaria ?? '')) ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark-custom">
                            <?= $editando ? 'Salvar Alterações' : 'Criar Vaga' ?>
                        </button>
                        <a href="vagas.php" class="btn btn-outline-dark">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>