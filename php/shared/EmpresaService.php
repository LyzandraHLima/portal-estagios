<?php

    require_once 'ApiClient.php';
    require_once 'Vaga.php';
    require_once 'Candidatura.php';

class EmpresaService{

    private ApiClient $api;

    public function __construct(ApiClient $api){
        $this->api = $api;
    }

   public function listarVagas(int $empresaId): array {
        $resposta = $this->api->get('/api/vagas?empresaId=' . $empresaId);
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

    public function criarVaga(array $dados): array{
        return $this->api->post('/api/vagas', $dados);
    }
    
    public function editarVaga(int $vagaId, array $dados): array{
        return $this->api->put('/api/vagas/'. $vagaId, $dados);
    }

    public function deletarVaga(int $vagaId): bool{
        return $this->api->delete('/api/vagas/' . $vagaId);
    }

    public function listarCandidatos(int $vagaId): array{
        return $this->api->get('/api/candidaturas?vagaId=' . $vagaId);
    }

    public function atualizarStatusCandidatura(int $id, string $status): array{
        $dados = ['status' => $status];
        return $this->api->put('/api/candidaturas/' . $id . '/status', $dados);
    }

    public function login(string $email, string $senha): ?array {
    $dados = ['email' => $email, 'senha' => $senha];
    $resposta = $this->api->post('/api/empresas/login', $dados);
    
    return $resposta['empresa'] ?? null;
}
    
}



