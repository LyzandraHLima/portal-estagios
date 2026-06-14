import type { NextFunction, Request, Response } from "express"
import { z } from "zod"
import type { EmpresaService } from "../services/EmpresaService"
import AppError from "../utils/AppError"

export class EmpresaController {
  constructor(private readonly service: EmpresaService) { }

  private schemaCriar = z.object({
    nome: z.string({ message: "Nome obrigatório" }).trim().min(1),
    cnpj: z.string({ message: "CNPJ obrigatório" }).min(14, "CNPJ inválido"),
    email: z.string({ message: "Email obrigatório" }).email("Email inválido"),
    senha: z.string({ message: "Senha obrigatória" }),
    telefone: z.string().optional(),
    area_atuacao: z.string().optional(),
    status: z.string().optional(),
  })

  private schemaAtualizar = z.object({
    nome: z.string().trim().min(1).optional(),
    email: z.string().email().optional(),
    senha: z.string().optional(),
    telefone: z.string().optional(),
    area_atuacao: z.string().optional(),
    status: z.string().optional(),
  })

  private schemaLogin = z.object({
    email: z.string().email("Email inválido"),
    senha: z.string().min(1, "Senha obrigatória"),
  })

  login = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { email, senha } = this.schemaLogin.parse(req.body)
      const empresa = await this.service.login(email, senha)
      res.json({ empresa })
    } catch (e) {
      next(e)
    }
  }

  listar = async (_req: Request, res: Response, next: NextFunction) => {
    try {
      const empresas = await this.service.listar()
      res.json({ empresas })
    } catch (e) {
      next(e)
    }
  }

  buscarPorId = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id)
      if (!Number.isInteger(id) || id < 1) throw new AppError("Parâmetro id inválido", 400)
      const empresa = await this.service.buscarPorId(id)
      res.json({ empresa })
    } catch (e) {
      next(e)
    }
  }

  criar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const body = this.schemaCriar.parse(req.body)
      const empresa = await this.service.criar(body)
      res.status(201).json({ empresa })
    } catch (e) {
      next(e)
    }
  }

  atualizar = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id)
      if (!Number.isInteger(id) || id < 1) throw new AppError("Parâmetro id inválido", 400)
      const body = this.schemaAtualizar.parse(req.body)
      const empresa = await this.service.atualizar(id, body)
      res.json({ message: "Empresa atualizada", empresa })
    } catch (e) {
      next(e)
    }
  }

  remover = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const id = Number(req.params.id)
      if (!Number.isInteger(id) || id < 1) throw new AppError("Parâmetro id inválido", 400)
      await this.service.remover(id)
      res.json({ message: "Empresa removida" })
    } catch (e) {
      next(e)
    }
  }
}