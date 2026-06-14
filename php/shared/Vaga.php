<?php

class Vaga {
    public int $id;
    public int $empresa_id;
    public string $titulo;
    public string $descricao;
    public string $area;
    public string $requisitos;
    public float $carga_horaria;
    public string $modalidade;
    public string $status;
    public ?string $createdAt;

    public function __construct(int $id, int $empresa_id, string $titulo, string $descricao, string $area, string $requisitos, float $carga_horaria, string $modalidade, string $status, ?string $createdAt) {
        $this->id = $id;
        $this->empresa_id = $empresa_id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->area = $area;
        $this->requisitos = $requisitos;
        $this->carga_horaria = $carga_horaria;
        $this->modalidade = $modalidade;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }
}