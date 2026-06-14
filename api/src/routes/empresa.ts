import { Router } from "express"
import { AppDataSource } from "../database/data-source"
import { Empresa } from "../models/Empresa"
import { EmpresaRepository } from "../repositories/EmpresaRepository"
import { EmpresaService } from "../services/EmpresaService"
import { EmpresaController } from "../controllers/EmpresaController"

const router = Router()

const empresaRepository = new EmpresaRepository(AppDataSource.getRepository(Empresa))
const empresaService = new EmpresaService(empresaRepository)
const empresaController = new EmpresaController(empresaService)

router.get("/", empresaController.listar)
router.post("/login", empresaController.login)
router.get("/:id", empresaController.buscarPorId)
router.post("/", empresaController.criar)
router.put("/:id", empresaController.atualizar)
router.delete("/:id", empresaController.remover)

export default router