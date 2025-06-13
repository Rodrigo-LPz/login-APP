<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller{
    /**
    * @Route("/admin/crear", name="adminCrear")
    */
    
    public function adminCrear(Request $request){
        // Solicitud de tipo 'POST'.
        if ($request->isMethod('POST')){
            $dni = $request->request->get('username');
            $nuevoRol = $request->request->get('rol');
            
            // Ejecución de al menos un intento para la actualización y/o creación del usuario posterior a su validación.
            try{
                // Se obtiene el "entityManager" de 'Doctrine ORM' para poder realizar las operaciones e interactuar con la base de datos.
                $entityManager = $this->getDoctrine()->getManager();

                // Validación de los datos recibidos (busca al usuario por su 'username', es decir, por su 'DNI/NIE/NIF').
                  /**
                  * "$entityManager": Realiza una función de puente o conector entre 'Doctrine' y la base de datos.
                  * "getRepository('User::class')": Función con la que decirle a 'Doctrine' que me devuelva el repositorio con dominio/entidad "User", es decir, está accediendo a la tabla "User".
                  * "findOneBy([...])": Función que realiza una búsqueda específica, un único resultado (si hay más de uno te devuelve el primero), coincidente con el parámetro pasado entre paréntesis.
                  * "['username' => $dni]": Parámetro o condición pasada como filtro de búsqueda para la función "findOneBy([...])".
                  */
                $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $dni]);
                
                // Codicional de tipo "if-else" para la comprobación de la existencia del usuario.
                if ($user){
                    // Si existe, actualizar únicamente el rol del usuario (ya existente).
                    $user->setRoles([$nuevoRol]);

                    // Muestreo de un mensaje de éxito
                    $this->addFlash('success', 'El \'Rol\' del usuario {' .$user->getUsername(). '} ha sido actualizado correctamente a {' .$nuevoRol. '}.');
                } else{
                    // Si no existe, crearlo y ponerle/añadirle un rol.
                    $user = new User();
                    $user->setUsername($dni);
                    $user->setRoles([$nuevoRol]);

                    // Muestreo de un mensaje de éxito
                    $this->addFlash('success', 'El \'Usuario\' {' .$user->getUsername(). '} ha sido creado o modificado correctamente con su nuevo rol {' .$nuevoRol. '}.');
                }

                // Persistencia/Guardado de los datos y cambios realizados en la base de datos, es decir, una vez modificado el rol del usuario, éste es guardado dentro de la base de datos con el último estado o valor dado (Ambas líneas permitirán el muestreo de los "$this->addFlash(...)").
                $entityManager->persist($user); /* "persist": Función de 'Doctrine' con la que indicar que el objeto "$user" gestionado por "entityManager" debe ser actualizado y guardado en la base de datos. */
                $entityManager->flush(); /* "flush": Función de 'Doctrine' con la que indicar que los cambios relizados sobre el objeto "$user" deben ser aplicados y guardados en la base de datos. */

            // Lanzamiento/Captura del error/excepción.
            } catch (\Exception $e){
                // Muestreo de un mensaje de error.
                $this->addFlash('error', 'Ha ocurrido un error: ' .$e->getMessage());
            }

            // Redirección a la misma ruta para evitar el reenvío del formulario al recargar la página después de haber procesado el formulario.
            return $this->redirectToRoute('adminCrear'); /* Esto es una buena práctica para evitar el problema del reenvío del formulario (POST/REDIRECT/GET). */
        }

        // Renderizado (lectura e interpretación por parte del navegador de código tipo 'HTML', 'CSS', 'JavaScript', etc.)de la vista del formulario de creación/actualización de usuarios.
        return $this->render(
            'admin/crear.html.twig',[
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,]
        );
    }
}
