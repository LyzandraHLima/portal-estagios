package utils;
import at.favre.lib.crypto.bcrypt.BCrypt;

public class SenhaUtil {
    public static String criptografar(String senhaPlana) {
        return BCrypt.withDefaults().hashToString(12, senhaPlana.toCharArray());
    }
    public static boolean verificar (String senhaPlana, String hashDoBanco){
        return BCrypt.verifyer()
                .verify(senhaPlana.toCharArray(), hashDoBanco).verified;
    }

}
