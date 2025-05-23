<?php
use PHPUnit\Framework\TestCase;
use SCS\AuthBundle\Security\LdapValidates;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LdapValidatesTest extends TestCase
{
    private LdapValidates $validator;

    protected function setUp(): void
    {
        $this->validator = new LdapValidates();
    }

    /**
     * @Test
     */
    public function testValidateUserSyntaxValid()
    {
        $user = 'usuario123';
        $result = $this->validator->validateUserSyntax($user);
        $this->assertEquals('usuario123', $result);
    }

    /**
     * @Test
     */
    public function testValidateUserSyntaxEmpty()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('El Usuario incorrecto.');

        $this->validator->validateUserSyntax('');
    }

    /**
     * @Test
     */
    public function testValidateUserSyntaxInvalidChars()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('El Usuario contiene carácteres no validos.');

        $this->validator->validateUserSyntax('user$invalid');
    }

    /**
     * @Test
     */
    public function testValidatePasswordSyntaxEmpty()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('La Contraseña está vacia.');

        $this->validator->validatePasswordSyntax('');
    }

    /**
     * @Test
     */
    public function testValidatePasswordSyntaxTooShort()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('La Contraseña debe tener un mínimo de 12 carácteres.');

        $this->validator->validatePasswordSyntax('Short1!');
    }

    /**
     * @Test
     */
    public function testValidatePasswordSyntaxTooLong()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('La Contraseña no puede exceder el máximo de 64 carácteres.');

        $longPass = str_repeat('A1!', 22); // 66 caracteres
        $this->validator->validatePasswordSyntax($longPass);
    }

    /**
     * @Test
     */
    public function testValidatePasswordSyntaxValid()
    {
        $pass = 'ValidPass123!';
        $result = $this->validator->validatePasswordSyntax($pass);
        $this->assertEquals($pass, $result);
    }

    /**
     * @Test
     */
    public function testValidatePasswordSyntaxContainsUser()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('La Contraseña no puede contener datos personales ni parecidos al de otros campos (nombre, email, usuario...)');

        $user = 'usuario';
        $pass = 'passwordConUsuario123!usuario';
        $this->validator->validatePasswordSyntax($pass, $user);
    }
}
