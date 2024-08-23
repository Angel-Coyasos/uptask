<?php

namespace Model;

class Usuario extends ActiveRecord {

    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $token;
    public $confirmado;

    public function __construct( $args = [] )
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;

        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->confirmar_password_nuevo = $args['confirmar_password_nuevo'] ?? '';
    }

    // Validacion para cunetas nuevas
    public function validarNuevaCuenta() {
        if (!$this->nombre) {
           self::$alertas['error'][] = ' El nombre del usuario es Obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El Correo  es Obligatorio';
        }

        if (!$this->password) {
            self::$alertas['error'][] = 'La  Contraseña es Obligatoria';
        }

        if ( strlen($this->password) < 8  || !preg_match('/[A-Z]/', $this->password) || !preg_match('/[a-z]/', $this->password) || !preg_match('/[0-9]/', $this->password) || !preg_match('/[\W]/', $this->password) ) {
            self::$alertas['error'][] = 'La  Contraseña debe contener al menos 8 caracteres, entre minusculas, mayusculas, numeros y simbolos';
        }

        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Las  Contraseñas no Coinciden';
        }

        return self::$alertas;
    }

    // Valdiar Email
    public function validarEmail()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Correo  es Obligatorio';
        }

        if ( !filter_var($this->email, FILTER_VALIDATE_EMAIL) ) {
            self::$alertas['error'][] = 'El Correo No es Valido';
        }


        return self::$alertas;
    }

    // Validar Password
    public function validarPassword()
    {
        if (!$this->password) {
            self::$alertas['error'][] = 'La  Contraseña es Obligatoria';
        }

        if ( strlen($this->password) < 8  || !preg_match('/[A-Z]/', $this->password) || !preg_match('/[a-z]/', $this->password) || !preg_match('/[0-9]/', $this->password) || !preg_match('/[\W]/', $this->password) ) {
            self::$alertas['error'][] = 'La  Contraseña debe contener al menos 8 caracteres, entre minusculas, mayusculas, numeros y simbolos';
        }

        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Las  Contraseñas no Coinciden';
        }

        return self::$alertas;
    }

    // Validar Login
    public function validarLogin()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Correo  es Obligatorio';
        }

        if ( !filter_var($this->email, FILTER_VALIDATE_EMAIL) ) {
            self::$alertas['error'][] = 'El Correo No es Valido';
        }

        if (!$this->password) {
            self::$alertas['error'][] = 'La  Contraseña es Obligatoria';
        }

        return self::$alertas;
    }

    // Validar perfil
    public function validar_perfil()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = ' El nombre del usuario es Obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El Correo  es Obligatorio';
        }
        if ( !filter_var($this->email, FILTER_VALIDATE_EMAIL) ) {
            self::$alertas['error'][] = 'El Correo No es Valido';
        }

        return self::$alertas;
    }

    // Validar nuevo password
    public function nuevo_password() : array
    {
        if (!$this->password_actual) {
            self::$alertas['error'][] = 'Debe Ingresar la Contraseña actual';
        }

        if (!$this->password_nuevo) {
            self::$alertas['error'][] = 'Debe Ingresar la nueva Contraseña';
        }

        if (!$this->confirmar_password_nuevo) {
            self::$alertas['error'][] = 'Debe Comfirmar la nueva Contraseña actual';
        }

        if ( strlen($this->password_nuevo) < 8  || !preg_match('/[A-Z]/', $this->password_nuevo) || !preg_match('/[a-z]/', $this->password_nuevo) || !preg_match('/[0-9]/', $this->password_nuevo) || !preg_match('/[\W]/', $this->password_nuevo) ) {
            self::$alertas['error'][] = 'La Nueva Contraseña debe contener al menos 8 caracteres, entre minusculas, mayusculas, numeros y simbolos';
        }

        if ( $this->confirmar_password_nuevo && $this->password_nuevo !== $this->confirmar_password_nuevo) {
            self::$alertas['error'][] = 'Las Contraseñas no Coinciden';
        }

        return self::$alertas;
    }

    // Comprobar password
    public function comprobar_password() : bool
    {
        return password_verify($this->password_actual, $this->password);
    }

    // Hashea el password
    public function hashPassword() : void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Generar token
    public function crearToken() : void  {
        /* $this->token = md5( uniqid() ); */
        $this->token = uniqid();
    }

}