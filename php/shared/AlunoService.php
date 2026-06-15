<?php

require_once 'ApiClient.php';
require_once 'Vaga.php';
require_once 'Candidatura.php';

class AlunoService {

    private ApiClient $api;

    public function __construct(ApiClient $api) {
        $this->api = $api;
    }

    public function login(string $email, string $senha): ?array {
        $dados = ['email' => $email, 'senha' => $senha];
        $resposta = $this->api->post('/api/alunos/login', $dados);

        return $resposta['aluno'] ?? null;
    }

    public function listarVagas(): array {
    $resposta = $this->api->get('/api/vagas');
    $vagas = [];

    foreach ($resposta['vagas'] as $v) {
        $vagas[] = new Vaga(
            $v['id'],
            $v['empresa']['id'],
            $v['titulo'],
            $v['descricao'],
            $v['area'],
            $v['requisitos'],
            $v['carga_horaria'],
            $v['modalidade'],
            $v['status'],
            $v['created_at']
        );
    }

    return $vagas;
}

public function buscarVaga(int $vagaId): ?Vaga {
    $resposta = $this->api->get('/api/vagas/' . $vagaId);
    $v = $resposta['vaga'] ?? null;
    if (!$v) return null;

    return new Vaga(
        $v['id'],
        $v['empresa']['id'],
        $v['titulo'],
        $v['descricao'],
        $v['area'],
        $v['requisitos'],
        $v['carga_horaria'],
        $v['modalidade'],
        $v['status'],
        $v['created_at']
    );
}

public function candidatar(array $dados): array {
    return $this->api->post('/api/candidaturas', $dados);
}

public function minhasCandidaturas(int $alunoId): array {
    $resposta = $this->api->get('/api/candidaturas');
    $candidaturas = [];

    foreach ($resposta['candidaturas'] as $c) {
        if ($c['aluno']['id'] == $alunoId) {
            $candidaturas[] = $c;
        }
    }

    return $candidaturas;
}

public function listarNotificacoes(int $alunoId): array {
    $resposta = $this->api->get('/api/notificacoes/' . $alunoId);
    return $resposta['notificacoes'] ?? [];
}

public function marcarNotificacaoComoLida(int $notificacaoId): array {
    return $this->api->put('/api/notificacoes/' . $notificacaoId . '/lida', []);
}


}