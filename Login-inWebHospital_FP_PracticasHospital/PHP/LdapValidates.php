<?php

namespace SCS\AuthBundle\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
// Documentacion
// http://symfony.com/doc/current/cookbook/security/custom_password_authenticator.html


class LdapValidates{
    public function validateUserSyntax($user){
        $usuario = trim($user);
        if (!strlen($user)){
            throw new AuthenticationException('El Usuario incorrecto.');
        }
        if(preg_match('/[^a-zA-Z0-9]/', $usuario) or
            preg_match('/\x00/',$usuario))
        {
            throw new AuthenticationException('El Usuario contiene carácteres no validos.');
        }
        return $usuario;
    }

    public function validatePasswordSyntax($password, $user = ''){
        $pass = trim($password);

        // Contraseña vacia.
        if (!strlen($pass)){
            throw new AuthenticationException(message: 'La Contraseña está vacia.');

        // Contraseña con -12 caracteres.
        } else if (strlen(string: $pass) < 12){
            throw new AuthenticationException(message: 'La Contraseña debe tener un mínimo de 12 carácteres.');

        // Contraseña con +64 caracteres.
        } else if (strlen(string: $pass) >64){
            throw new AuthenticationException(message: 'La Contraseña no puede exceder el máximo de 64 carácteres.');

        // Contraseña con mayor permisibilidad al tener +16 caracteres.
        } else if (strlen($pass) >= 16){
            return $pass;

        // Contraseña con mayores restricciones al tener -16 caracteres
        } else{
            // Contraseña con caracteres no validos (espacios).
            if (preg_match('/\x00/', $pass)){
                throw new AuthenticationException('La Contraseña contiene carácteres no validos.');
            }

            // Validar contraseña con caracteres permitidos (evita letras con tildes, diéresis, ñ, etc.)
            if (!preg_match('/^[A-Za-z0-9!@#$%\*\(\)_\+=:,\.?]+$/', $pass)){
                throw new AuthenticationException('La Contraseña contiene carácteres no permitidos.');
            }

            // Requisitos por categorías.
                // Al menos una letra mayúscula.
                if (!preg_match('/[A-Z]/', $pass) || preg_match('/Ñ|Á|É|Í|Ó|Ú|À|È|Ì|Ò|Ù|Â|Ê|Î|Ô|Û|Ä|Ë|Ï|Ö|Ü/', $pass)){
                    throw new AuthenticationException('La Contraseña debe incluir al menos una letra mayúscula válida (sin Ñ ni vocales con tilde).');
                }

                // Al menos una letra minúscula.
                if (!preg_match('/[a-z]/', $pass) || preg_match('/ñ|á|é|í|ó|ú|à|è|ì|ò|ù|â|ê|î|ô|û|ä|ë|ï|ö|ü/', $pass)){
                    throw new AuthenticationException('La Contraseña debe incluir al menos una letra minúscula válida (sin ñ ni vocales con tilde).');
                }

                // Al menos un dígito numérico.
                if (!preg_match('/[0-9]/', $pass)){
                    throw new AuthenticationException('La Contraseña debe incluir al menos un dígito numérico.');
                }

                // Al menos un carácter especial.
                if (!preg_match('/[!@#$%\*\(\)_\+=:,\.?]/', $pass)){
                    throw new AuthenticationException('La Contraseña debe incluir al menos un carácter especial permitido ([!@#$%\*\(\)_\+=:,\.?]).');
                }

                // Diferenciación con parecidos a datos personales (nombre, email, usuario...).
                if (!empty($user) && stripos($pass, $user) !== false){
                    throw new AuthenticationException('La Contraseña no puede contener datos personales ni parecidos al de otros campos (nombre, email, usuario...)');
                }

            return $pass;
        }
    }

    public function conectaLdap($host, $puerto, $user_ldap, $clave){
        $conexion =ldap_connect($host,$puerto) or die("Error");
        //version
        ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
        if(@ldap_bind($conexion, $user_ldap . "@gerenciaLZ.canariasalud", $clave))
        {
            $res = ldap_search($conexion, "ou=USUARIOS, ou=Hospital Dr jose Molina Orosa(USUARIOS), dc=gerenciaLZ, dc=canariasalud", "sAMAccountName=" . $user_ldap, array("givenName", "sn"));
            if($res)
            {
                $sal = ldap_get_entries($conexion, $res);
                $nombre = utf8_decode($sal[0]['givenname'][0]);
                $apellidos = utf8_decode($sal[0]['sn'][0]);
            }
            else
            {
                ldap_unbind($conexion);
                throw new AuthenticationException('Error leyendo datos de Ldap.');
            }
            ldap_unbind($conexion);
            return true;
        }
        else
        {
            ldap_unbind($conexion);
            throw new AuthenticationException('Validación en LDAP incorrecta');
        }
    }
}
