<?php

namespace SCS\AuthBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

// Documentacion
// http://symfony.com/doc/current/cookbook/security/custom_password_authenticator.html
class LdapAuthenticator implements SimpleFormAuthenticatorInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $keyProvider)
    {
        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException('El usuario no tiene permisos');
        }

        $passwordValid = true; //No hace falta validar la pass en DB
        if ($passwordValid) {
            $usuario = $user->getUsername();
            $clave = $token->getCredentials();

            // datos controlador de dominio
            $host = 'LZHGDC1';
            $puerto = 389;

            // comprobar usuario y contraseña
            if (isset($usuario)) {
                $validator = new LdapValidates();
                $user_ldap = $validator->validateUserSyntax($usuario);
                $clave = $validator->validatePasswordSyntax($clave);

                // conectar a controlador de dominio
                $conexion = ldap_connect($host, $puerto) or die('No ha sido posible conectarse al servidor de autentificaci&oacute;n');

                //version
                ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);

                if (@ldap_bind($conexion, $user_ldap . '@gerenciaLZ.canariasalud', $clave)) {
                    // obtener el nombre del usuario identificado
                    $res = ldap_search($conexion, 'ou=USUARIOS, ou=Hospital Dr jose Molina Orosa(USUARIOS), dc=gerenciaLZ, dc=canariasalud', 'sAMAccountName=' . $user_ldap, ['givenName', 'sn']);
                    if ($res) {
                        $sal = ldap_get_entries($conexion, $res);
                        $nombre = utf8_decode($sal[0]['givenname'][0]);
                        $apellidos = utf8_decode($sal[0]['sn'][0]);
                    } else {
                        ldap_unbind($conexion);
                        throw new CustomUserMessageAuthenticationException('Error leyendo datos de Ldap.');
                    }

                    // cerrar conexión con directorio activo
                    ldap_unbind($conexion);

                    //Acceso correcto
                    return new UsernamePasswordToken(
                        $user,
                        $user->getPassword(),
                        $keyProvider,
                        $user->getRoles()
                    );
                } else {
                    ldap_unbind($conexion);
                    throw new CustomUserMessageAuthenticationException('El usuario y/o la clave no son correctos.');
                }
            }
        }
        throw new CustomUserMessageAuthenticationException('Usuario o contraseña incorrecta.');
    }

    public function supportsToken(TokenInterface $token, $keyProvider)
    {
        return $token instanceof UsernamePasswordToken
            && $token->getProviderKey() === $keyProvider;
    }

    public function createToken(Request $request, $username, $password, $keyProvider)
    {
        return new UsernamePasswordToken($username, $password, $keyProvider);
    }
}
