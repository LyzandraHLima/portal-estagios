import { Router } from "express"
import { AppDataSource } from "../database/data-source"
import { Aluno } from "../models/Aluno"
import { AlunoRepository } from "../repositories/AlunoRepository"
import { AlunoService } from "../services/AlunoService"
import { AlunoController } from "../controllers/AlunoController"

const router = Router()

const alunoRepository = new AlunoRepository(AppDataSource.getRepository(Aluno))
const alunoService = new AlunoService(alunoRepository)
const alunoController = new AlunoController(alunoService)

router.get("/", alunoController.listar)
router.post("/login", alunoController.login)
router.get("/:id", alunoController.buscarPorId)
router.post("/", alunoController.criar)
router.put("/:id", alunoController.atualizar)
router.delete("/:id", alunoController.remover)

export default router