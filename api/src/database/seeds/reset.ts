import "reflect-metadata"
import "dotenv/config"
import { AppDataSource } from "../data-source"

async function reset() {
  await AppDataSource.initialize()

  await AppDataSource.query("SET FOREIGN_KEY_CHECKS = 0")
  await AppDataSource.query("TRUNCATE TABLE notificacao")
  await AppDataSource.query("TRUNCATE TABLE candidatura")
  await AppDataSource.query("TRUNCATE TABLE vaga")
  await AppDataSource.query("TRUNCATE TABLE aluno")
  await AppDataSource.query("TRUNCATE TABLE empresa")
  await AppDataSource.query("SET FOREIGN_KEY_CHECKS = 1")

  console.log("Banco limpo!")
  await AppDataSource.destroy()
}

reset().catch(async (err) => {
  console.error(err)
  if (AppDataSource.isInitialized) await AppDataSource.destroy()
  process.exit(1)
})