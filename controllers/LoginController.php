<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;
use Classes\Email;

class LoginController {

    public static function login(Router $router) {

        $alertas = [];

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if ( empty($alertas) ) {

                // Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El Usuario No Existe o No esta confirmado.');
                } else {
                    // El usuario existe
                    if ( password_verify($_POST['password'], $usuario->password) ) {

                        // Iniciar la sesion del usuario
                        session_start();

                        // Llenar datos de session
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionar
                        header('Location: /dashboard');


                    } else {
                        Usuario::setAlerta('error', 'Password Incorrecto.');
                    }

                }

            }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);

    }

    
    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function crear(Router $router) {

        $usuario = new Usuario; // crear objecto vacio
        $alertas = [];

        
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
           $usuario->sincronizar($_POST); // sincronizar valores
           $alertas = $usuario->validarNuevaCuenta(); //  llamar metodo para valdiar alertar 

           if (empty($alertas)) {

                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El Usuario ya esta registrado');
                    $alertas = Usuario::getAlertas(); // para volver a guardas la alerta
                } else {
                    // hashear el password
                    $usuario->hashPassword();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Generar token
                    $usuario->crearToken();

                    // Crear nuevo usuario
                    $resultado = $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);

                    $email->enviarConfirmacion();

                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
 
           }
          
           /* debuguear($existeUsuario); */
        }

        $router->render('auth/crear', [
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);

    }

    public static function forgot(Router $router) {

        $alertas = [];

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarEmail();

            if ( empty($alertas) ) {

                // Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if ($usuario && $usuario->confirmado) {
                    // Encontre el usuario y esta confirmado

                    // Generar nuevo token
                    $usuario->crearToken();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Actualizar el usuario
                    $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();


                    // Imprimir alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email.');

                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado.');
                }

                $alertas = Usuario::getAlertas();

            }

        }

        $router->render('auth/forgot', [
            'titulo' => 'Olvide Password',
            'alertas' => $alertas
        ]);

    }

    public static function reset(Router $router) {

        $token = s( $_GET['token'] );
        $mostrar = true; // para mostrar u ocultar el formulario

        if (!$token) header('Location: /');

        // Identificar el usuario con este token
        $usuario = Usuario::where('token', $token);

        if ( empty($usuario) ) {
            Usuario::setAlerta('error', 'Token No Valido');
            $mostrar = false;
        }

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

            // agregar nuevo password
            $usuario->sincronizar($_POST);

            // Validar el password
            $alertas = $usuario->validarPassword();

            if ( empty($alertas) ) {

                // Hashear el password
                $usuario->hashPassword();

                // Eliminar password2
                unset($usuario->password2);

                // Eliminar el token
                $usuario->token = null;

                // Guardar el usuario en la DB
                $resultado = $usuario->guardar();

                // Redirecionar
                if ($resultado) {
                    header('Location: /');
                }

            }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/reset', [
            'titulo' => 'Recuperar Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);

    }

    public static function mensaje(Router $router) {

        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada Exitosamente',
        ]);

    }

    public static function confirmar(Router $router) {

        $token = s($_GET['token']);

        if (!$token) header('Location: /');

        // Encontrar al usuario con el token
        $usuario = Usuario::where('token', $token);

        if ( empty($usuario) ) {
            // No se encontro usuario con el token
            Usuario::setAlerta('error', 'Token No Valido');
        } else {
            // confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);

            // Guardar en la Base de Datos
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar Tu Cuenta UpTask',
            'alertas' => $alertas
        ]);

    }

}