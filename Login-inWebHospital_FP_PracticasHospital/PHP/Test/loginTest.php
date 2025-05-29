<?php /* Indica/Marca el inicio del código "PHP". */


// Indica que estos tests están en el espacio "Tests\Security".
namespace Tests\Security;
// namespace SCS\AuthBundle\Security;


use PHPUnit\Framework\TestCase; /* Permite usar "PHPUnit" para escribir pruebas. */
use Symfony\Component\Security\Core\Exception\AuthenticationException; /* Permite lanzar excepciones de autentificación, como errores de contraseña. */


// Incluye tus clases reales
require_once __DIR__ . '/../../src/A.php'; // Clase con authenticateToken
require_once __DIR__ . '/../../src/B.php'; // Clase con validaciones (LdapValidates)

class ATest extends TestCase
{
    /**
     * @test
     * Valida que una contraseña válida no lance excepción y se devuelva correctamente.
     */
    public function validatePasswordSyntax_valida_password_correcta()/* También podemos obviar el atributo o uso del "@Test" por test_ + nombre de la función del test. Ejemplo: "text_validatePasswordSyntax_valida_password_correcta". */
    {
        $validator = new LdapValidates();
        $password = 'Passw0rd!@# áÑ';
        $result = $validator->validatePasswordSyntax($password);
        $this->assertEquals(trim($password), $result);
    }

    /**
     * @test
     * Valida que una contraseña vacía lance excepción.
     */
    public function validatePasswordSyntax_password_vacia()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Contraseña vacia');
        $validator = new LdapValidates();
        $validator->validatePasswordSyntax('');
    }

    /**
     * @test
     * Valida que una contraseña con caracteres no válidos lance excepción.
     */
    public function validatePasswordSyntax_password_caracteres_invalidos()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('La contraseña contiene caracteres no validos');
        $validator = new LdapValidates();
        $validator->validatePasswordSyntax("passw0rd<>");
    }

    /**
     * @test
     * Valida que una contraseña con caracter nulo lance excepción.
     */
    public function validatePasswordSyntax_password_con_nulo()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('La contraseña contiene caracteres no validos');
        $validator = new LdapValidates();
        $validator->validatePasswordSyntax("passw0rd\x00");
    }
}