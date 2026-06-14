import type { EmpresaRepository } from "../repositories/EmpresaRepository"
import type { Empresa } from "../models/Empresa"
import AppError from "../utils/AppError"
import bcrypt from 'bcrypt'

export class EmpresaService {
  constructor(private readonly repository: EmpresaRepository) {}

  async listar(): Promise<Empresa[]> {
    return this.repository.listarTodos()
  }

  async buscarPorId(id: number): Promise<Empresa> {
    const empresa = await this.repository.buscarPorId(id)
    if (!empresa) throw new AppError("Empresa não encontrada", 404)
    return empresa
  }

  async criar(dados: Partial<Empresa>): Promise<Empresa> {
    const existente = await this.repository.buscarPorCnpj(dados.cnpj!)
    if (existente) throw new AppError("CNPJ já cadastrado", 409)

    dados.senha = await bcrypt.hash(dados.senha!, 10);

    return this.repository.criar(dados)
  }

  async atualizar(id: number, dados: Partial<Empresa>): Promise<Empresa> {
    const empresa = await this.repository.buscarPorId(id)
    if (!empresa) throw new AppError("Empresa não encontrada", 404)

    if (dados.nome !== undefined) empresa.nome = dados.nome
    if (dados.email !== undefined) empresa.email = dados.email
    if (dados.senha !== undefined) empresa.senha = dados.senha = await bcrypt.hash(dados.senha!, 10)
    if (dados.telefone !== undefined) empresa.telefone = dados.telefone
    if (dados.area_atuacao !== undefined) empresa.area_atuacao = dados.area_atuacao
    if (dados.status !== undefined) empresa.status = dados.status

    return this.repository.salvar(empresa)
  }

  async remover(id: number): Promise<void> {
    const ok = await this.repository.remover(id)
    if (!ok) throw new AppError("Empresa não encontrada", 404)
  }

  async login(email: string, senha: string): Promise<Partial<Empresa>> {
  const empresa = await this.repository.buscarPorEmailComSenha(email)
  if (!empresa) throw new AppError("Email ou senha incorretos", 401)

  const senhaCorreta = await bcrypt.compare(senha, empresa.senha)
  if (!senhaCorreta) throw new AppError("Email ou senha incorretos", 401)

  const { senha: _, ...empresaSemSenha } = empresa
  return empresaSemSenha  
}

}