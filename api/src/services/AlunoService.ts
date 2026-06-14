import type { AlunoRepository } from "../repositories/AlunoRepository"
import type { Aluno } from "../models/Aluno"
import AppError from "../utils/AppError"
import bcrypt from 'bcrypt'


export class AlunoService {
  constructor(private readonly repository: AlunoRepository) {}

  async listar(): Promise<Aluno[]> {
    return this.repository.listarTodos()
  }

  async buscarPorId(id: number): Promise<Aluno> {
    const aluno = await this.repository.buscarPorId(id)
    if (!aluno) throw new AppError("Aluno não encontrado", 404)
    return aluno
  }

  async criar(dados: Partial<Aluno>): Promise<Aluno> {
    const emailExistente = await this.repository.buscarPorEmail(dados.email!)
    if (emailExistente) throw new AppError("Email já cadastrado", 409)

    const matriculaExistente = await this.repository.buscarPorMatricula(dados.matricula!)
    if (matriculaExistente) throw new AppError("Matrícula já cadastrada", 409)

    dados.senha = await bcrypt.hash(dados.senha!, 10);
    
    return this.repository.criar(dados)
  }

  async atualizar(id: number, dados: Partial<Aluno>): Promise<Aluno> {
    const aluno = await this.repository.buscarPorId(id)
    if (!aluno) throw new AppError("Aluno não encontrado", 404)

    if (dados.nome !== undefined) aluno.nome = dados.nome
    if (dados.email !== undefined) aluno.email = dados.email
    if (dados.senha !== undefined) aluno.senha = dados.senha = await bcrypt.hash(dados.senha!, 10)
    if (dados.curso !== undefined) aluno.curso = dados.curso
    if (dados.periodo !== undefined) aluno.periodo = dados.periodo
    if (dados.apto !== undefined) aluno.apto = dados.apto
    if (dados.status !== undefined) aluno.status = dados.status

    return this.repository.salvar(aluno)
  }

  async remover(id: number): Promise<void> {
    const ok = await this.repository.remover(id)
    if (!ok) throw new AppError("Aluno não encontrado", 404)
  }

  async login(email: string, senha: string): Promise<Partial<Aluno>> {
  const aluno = await this.repository.buscarPorEmailComSenha(email)
  if (!aluno) throw new AppError("Email ou senha incorretos", 401)

  const senhaCorreta = await bcrypt.compare(senha, aluno.senha)
  if (!senhaCorreta) throw new AppError("Email ou senha incorretos", 401)

  const { senha: _, ...alunoSemSenha } = aluno
  return alunoSemSenha
}
}