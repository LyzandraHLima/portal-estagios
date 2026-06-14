import type { NextFunction, Request, Response } from "express"
import { z } from "zod"
import type { AlunoService } from "../services/AlunoService"
import AppError from "../utils/AppError"

export class AlunoController {
  constructor(private readonly service: AlunoService) {}

  private schemaCriar = z.object({
    nome: z.string({ message: "Nome obrigatório" }).trim().min(1),
    email: z.string({ message: "Email obrigatório" }).email("Email inválido"),
    senha: z.string({ message: "Senha obrigatória" }),
    cpf: z.string({ message: "CPF obrigatório" }).min(11, "CPF inválido"),
    matricula: z.string({ message: "Matrícula obrigatória" }).min(1),
    curso: z.string().optional(),
    periodo: z.number({ message: "Período obrigatório" }),
    apto: z.number().optional(),
    status: z.string().optional(),
  })

  private schemaAtualizar = z.object({
    nome: z.string().trim().min(1).optional(),
    email: z.string().email().optional(),
    senha: z.string().optional(),
    curso: z.string().optional(),
    periodo: z.number().optional(),
    apto: z.number().optional(),
    status: z.string().optional(),
  })

  private schemaLogin = z.object({
  email: z.string().email("Email inválido"),
  senha: z.string().min(1, "Senha obrigatória"),
  })

  login = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { email, senha } = this.schemaLogin.parse(req.body)
      const aluno = await this.service.login(email, senha)
      res.json({ aluno })
    } catch (e) {
      next(e)
    }
  }

  listar = async (_req: Request, res: Response, next: NextFunction) => {
    try {
      const alunos = await this.service.listar()
      res.json({ alunos })
    } catch (e) {
      next(e)
    }
  }

  buscarPorId = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id)
      if (!Number.isInteger(id) || id < 1) throw new AppError("Parâmetro id inválido", 400)
      const aluno = await this.service.buscarPorId(id)
      res.json({ aluno })
    } catch (e) {
      next(e)
    }
  }

  criar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const body = this.schemaCriar.parse(req.body)
      const aluno = await this.service.criar(body)
      res.status(201).json({ aluno })
    } catch (e) {
      next(e)
    }
  }

  atualizar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id)
      if (!Number.isInteger(id) || id < 1) throw new AppError("Parâmetro id inválido", 400)
      const body = this.schemaAtualizar.parse(req.body)
      const aluno = await this.service.atualizar(id, body)
      res.json({ message: "Aluno atualizado", aluno })
    } catch (e) {
      next(e)
    }
  }

  remover = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id)
      if (!Number.isInteger(id) || id < 1) throw new AppError("Parâmetro id inválido", 400)
      await this.service.remover(id)
      res.json({ message: "Aluno removido" })
    } catch (e) {
      next(e)
    }
  }
}