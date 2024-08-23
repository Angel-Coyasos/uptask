<?php

namespace Controllers;

use Model\Usuario;
use MVC\models\Proyecto;
use MVC\Router;

class DashboardController
{

    public static function index(Router $router) {

        session_start();

        isAuth();

        $proyectos = Proyecto::belongsTo('propietarioId', $_SESSION['id']);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);

    }

    public static function crear_proyecto(Router $router) {

        session_start();

        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $proyecto = new Proyecto($_POST);
            $alertas = $proyecto->validar();

            if ( empty($alertas) ) {

                // Generar una URL unica
                $hash = md5( uniqid() ); // md5 se genra con una cadena, uniqid se generas egun la fecha, md5 es inseguro para password o tarjetas de credito
                $proyecto->url = $hash;

                //  Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                // Guardar el proyecto
                $proyecto->guardar();

                // Redireccionar
                header('Location: /proyecto?id='.$proyecto->url);

            }

        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Poryectos',
            'alertas' => $alertas
        ]);

    }

    public static function proyecto(Router $router)
    {
        session_start();
        isAuth();

        $token = $_GET['id'];
        if (!$token) header('Location: /dashboard');

        // Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if ($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

       /* debuguear($proyecto);*/

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router) {

        session_start();
        isAuth();

        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

           $usuario->sincronizar($_POST);

           $alertas = $usuario->validar_perfil();

           if ( empty($alertas) ) {

               // Verificar email
               $existeUsuario = $usuario::where('email', $usuario->email);

               if($existeUsuario && $existeUsuario->id !== $usuario->id )  {

                   // Manejo de error
                   Usuario::setAlerta('error', 'El Email no valido, ya pertenece a otra cuenta');
               } else {

                   // Guardar usuario
                   $usuario->guardar();

                   // Actualizar datos d elas session
                   $_SESSION['nombre'] = $usuario->nombre;
                   $_SESSION['email'] = $usuario->email;

                   // Almacenar mensaje en memoria
                   Usuario::setAlerta('exito', 'Usuario Actualizado Correctamente');
               }

           }

        }

        // Imprimir alertas en pantalla
        $alertas = Usuario::getAlertas();

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);

    }

    public static function cambiar_password(Router $router)
    {
        session_start();
        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = Usuario::find($_SESSION['id']);

            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password(); //  llamar metodo para valdiar alertar

            if ( empty($alertas) ) {

                $resultado = $usuario->comprobar_password();

                if ($resultado) {

                    // Asignar el nuevo password
                    $usuario->password = $usuario->password_nuevo;

                    // limpiar propiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password2);
                    unset($usuario->password_nuevo);
                    unset($usuario->confirmar_password_nuevo);

                    // Hasshear password
                    $usuario->hashPassword();

                    // Guardar
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        Usuario::setAlerta('exito', 'Contraseña Guardada Correctamente');
                    }

                } else {
                    Usuario::setAlerta('error', 'Contraseña Incorrecta');
                }

            }

            $alertas = $usuario->getAlertas();

        }

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    }

}