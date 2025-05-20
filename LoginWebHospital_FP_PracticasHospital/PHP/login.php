<?php
namespace SCS\AuthBundle\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
// Documentacion
// http://symfony.com/doc/current/cookbook/security/custom_password_authenticator.html
class LdapValidates
{
    public function validateUserSyntax($user)
    {
        $usuario = trim($user);
        if (!strlen($user)){
            throw new AuthenticationException('Usuario incorrecto');
        }
        if(preg_match('/[^a-zA-Z0-9]/', $usuario) or
            preg_match('/\x00/',$usuario))
        {
            throw new AuthenticationException('Usuario contiene caracteres no validos');
        }
        return $usuario;
    }
    
    public function validatePasswordSyntax($password)
    {
        $pass = trim($password);
        if (!strlen($pass)){
            throw new AuthenticationException('Contraseña vacia');
        }
        /*
        * Suponiendo que por defecto el archivo ".php" trabaja bajo expresiones regulaes "UTF-8".
        * 
        * "\p{L}"           permite cualquier carácter no numérico, letras y carácteres especiales, incluyendo ('ñ' y carácteres con tildes, 'á', etc.).
        * "\p{N}"           permite cualquier carácter numérico ('0', '7', etc.).
        * "\x20"            permite espacios.
        * "!@#$%^&*().,_\-" permite carácteres específicos ('@', '&', etc.).
        * "u"               indica que la cadena está trabajando en UTF-8.
        *
        * "\x00"            no permite/bloquea los carácteres nulos.
        */
        if(preg_match('/[^\p{L}\p{N}!@#$%^&*().,_\-]/u', $pass) or preg_match('/\x00/',$pass)){
            throw new AuthenticationException('La contraseña contiene caracteres no validos');
        }
        return $pass;
    }

    public function conectaLdap($host, $puerto, $user_ldap, $clave)
    {
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