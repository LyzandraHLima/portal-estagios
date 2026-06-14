import type { Repository } from "typeorm"
import type { Aluno } from "../models/Aluno"

export class AlunoRepository {
  constructor(private readonly repo: Repository<Aluno>) {}

  async listarTodos(): Promise<Aluno[]> {
    return this.repo.find({
      select: {
        id: true,
        nome: true,
        email: true,
        cpf: true,
        matricula: true,
        curso: true,
        periodo: true,
        apto: true,
        status: true,
        created_at: true,
      },
      order: { id: "ASC" }
    })
  }

  async buscarPorId(id: number): Promise<Aluno | undefined> {
    const row = await this.repo.findOne({
      where: { id },
      select: {
        id: true,
        nome: true,
        email: true,
        senha: true,
        cpf: true,
        matricula: true,
        curso: true,
        periodo: true,
        apto: true,
        status: true,
        created_at: true,
      }
    })
    return row ?? undefined
  }

  async buscarPorEmail(email: string): Promise<Aluno | undefined> {
    const row = await this.repo.findOne({ where: { email } })
    return row ?? undefined
  }

  async buscarPorEmailComSenha(email: string): Promise<Aluno | undefined> {
  const row = await this.repo.findOne({
    where: { email },
    select: {
      id: true,
      nome: true,
      email: true,
      senha: true,
      status: true,
      apto: true,
    }
  })
  return row ?? undefined
}

  async buscarPorMatricula(matricula: string): Promise<Aluno | undefined> {
    const row = await this.repo.findOne({ where: { matricula } })
    return row ?? undefined
  }

  async criar(dados: Partial<Aluno>): Promise<Aluno> {
    const ent = this.repo.create(dados)
    return this.repo.save(ent)
  }

  async salvar(entidade: Aluno): Promise<Aluno> {
    return this.repo.save(entidade)
  }

  async remover(id: number): Promise<boolean> {
    const r = await this.repo.delete(id)
    return (r.affected ?? 0) > 0
  }
}