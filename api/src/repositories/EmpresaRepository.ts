import type { Repository } from "typeorm"
import type { Empresa } from "../models/Empresa"

export class EmpresaRepository {
  constructor(private readonly repo: Repository<Empresa>) {}

 async listarTodos(): Promise<Empresa[]> {
  return this.repo.find({
    select: {
      id: true,
      nome: true,
      cnpj: true,
      email: true,
      telefone: true,
      area_atuacao: true,
      status: true,
      created_at: true,
    },
    order: { id: "ASC" }
  })
}

async buscarPorId(id: number): Promise<Empresa | undefined> {
  const row = await this.repo.findOne({
    where: { id },
    select: {
      id: true,
      nome: true,
      cnpj: true,
      email: true,
      senha: true, 
      telefone: true,
      area_atuacao: true,
      status: true,
      created_at: true,
    }
  })
  return row ?? undefined
}

async buscarPorEmailComSenha(email: string): Promise<Empresa | undefined> {
  const row = await this.repo.findOne({
    where: { email },
    select: {
      id: true,
      nome: true,
      email: true,
      senha: true,
      status: true,
    }
  })
  return row ?? undefined
}

  async buscarPorCnpj(cnpj: string): Promise<Empresa | undefined> {
    const row = await this.repo.findOne({ where: { cnpj } })
    return row ?? undefined
  }

  async criar(dados: Partial<Empresa>): Promise<Empresa> {
    const ent = this.repo.create(dados)
    return this.repo.save(ent)
  }

  async salvar(entidade: Empresa): Promise<Empresa> {
    return this.repo.save(entidade)
  }

  async remover(id: number): Promise<boolean> {
    const r = await this.repo.delete(id)
    return (r.affected ?? 0) > 0
  }
}