<?php

require_once "../inc/session_start.php";

require_once "main.php";

$id = limpiar_cadena($_POST['usuario_id']);

# Verificar usuario #
$check_usuario = conexion();
$check_usuario = $check_usuario->query("SELECT * FROM usuario WHERE id_usuario = '$id'");

if ($check_usuario->rowCount() <= 0) {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El usuario no existe en el sistema
            </div>
        ';
    exit();
} else {
    $datos = $check_usuario->fetch();
}
$check_usuario = null;

$admin_usuario = limpiar_cadena($_POST['administrador_usuario']);
$admin_clave = limpiar_cadena($_POST['administrador_clave']);

/*== Verificando campos obligatorios ==*/
if ($admin_usuario == "" || $admin_clave == "") {
    echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               No has llenado todos los campos que son obligatorios
           </div>
       ';
    exit();
}

/*== Verificando integridad de los datos ==*/
if (verificar_datos("[a-zA-Z0-9]{4,20}", $admin_usuario)) {
    echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               El USUARIO no coincide con el formato solicitado
           </div>
       ';
    exit();
}

if (verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $admin_clave)) {
    echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               Su CLAVE no coincide con el formato solicitado
           </div>
       ';
    exit();
}

# Verificando administrador #
$check_admin = conexion();
$check_admin = $check_admin->query("SELECT usuario_usuario, clave_usuario FROM usuario WHERE usuario_usuario = '$admin_usuario' 
AND id_usuario = '" . $_SESSION['id'] . "'");

if ($check_admin->rowCount() == 1) {
    $check_admin = $check_admin->fetch();

    if ($check_admin['usuario_usuario'] != $admin_usuario || password_verify($admin_clave, $check_admin['clave_usuario'])) {
        echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               USUARIO y/o CLAVE de administrador incorrectos
           </div>
       ';
        exit();
    }
} else {
    echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               USUARIO y/o CLAVE de administrador incorrectos
           </div>
       ';
    exit();
}

$check_admin = null;

# Almacenando datos #
$nombre = limpiar_cadena($_POST['usuario_nombre']);
$apellido = limpiar_cadena($_POST['usuario_apellido']);

$usuario = limpiar_cadena($_POST['usuario_usuario']);
$email = limpiar_cadena($_POST['usuario_email']);

$clave_1 = limpiar_cadena($_POST['usuario_clave_1']);
$clave_2 = limpiar_cadena($_POST['usuario_clave_2']);

/*== Verificando campos obligatorios ==*/
if ($nombre == "" || $apellido == "" || $usuario == "") {
    echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               No has llenado todos los campos que son obligatorios
           </div>
       ';
    exit();
}

/*== Verificando integridad de los datos ==*/
if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
    echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               El NOMBRE no coincide con el formato solicitado
           </div>
       ';
    exit();
}

if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)) {
    echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               El APELLIDO no coincide con el formato solicitado
           </div>
       ';
    exit();
}

if (verificar_datos("[a-zA-Z0-9]{4,20}", $usuario)) {
    echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               El USUARIO no coincide con el formato solicitado
           </div>
       ';
    exit();
}

/*== Verificando email ==*/
if ($email != "" && $email != $datos['email_usuario']) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $check_email = conexion();
        $check_email = $check_email->query("SELECT email_usuario FROM usuario WHERE email_usuario='$email'");
        if ($check_email->rowCount() > 0) {
            echo '
                   <div class="notification is-danger is-light">
                       <strong>¡Ocurrio un error inesperado!</strong><br>
                       El correo electrónico ingresado ya se encuentra registrado, por favor elija otro
                   </div>
               ';
            exit();
        }
        $check_email = null;
    } else {
        echo '
               <div class="notification is-danger is-light">
                   <strong>¡Ocurrio un error inesperado!</strong><br>
                   Ha ingresado un correo electrónico no valido
               </div>
           ';
        exit();
    }
}

/*== Verificando usuario ==*/
if ($usuario != $datos['usuario_usuario']) {
    $check_usuario = conexion();
    $check_usuario = $check_usuario->query("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
    if ($check_usuario->rowCount() > 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El USUARIO ingresado ya se encuentra registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_usuario = null;
}

/*== Verificando claves ==*/
if ($clave_1 != "" || $clave_2 != "") {
    if (verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $clave_1) || verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $clave_2)) {
        echo '
              <div class="notification is-danger is-light">
                  <strong>¡Ocurrio un error inesperado!</strong><br>
                  Las CLAVES no coinciden con el formato solicitado
              </div>
          ';
        exit();
    } else {
        if ($clave_1 != $clave_2) {
            echo '
              <div class="notification is-danger is-light">
                  <strong>¡Ocurrio un error inesperado!</strong><br>
                  Las CLAVES que ha ingresado no coinciden
              </div>
          ';
            exit();
        } else {
            $clave = password_hash($clave_1, PASSWORD_BCRYPT, ["cost" => 10]);
        }
    }
} else {
    $clave = $datos['clave_usuario'];
}

# Actualizando datos #
$actualizar_usuario = conexion();
$actualizar_usuario = $actualizar_usuario->prepare("UPDATE usuario SET nombre_usuario = :nombre, apellido_usuario = :apellido, 
usuario_usuario = :usuario, clave_usuario = :clave, email_usuario = :email WHERE id_usuario = :id");

$marcadores = [
    ":nombre" => $nombre,
    ":apellido" => $apellido,
    ":usuario" => $usuario,
    ":clave" => $clave,
    ":email" => $email,
    ":id" => $id
];

if ($actualizar_usuario->execute($marcadores)) {
    echo '
              <div class="notification is-info is-light">
                  <strong>¡Usuario Actualizado!</strong><br>
                  El usuario se actualizó con éxito
              </div>
          ';
} else {
    echo '
      <div class="notification is-danger is-light">
         <strong>¡Opss!</strong><br>
         No se pudo actualizar el usuario, por favor intenta de nuevo
      </div>';
}

$actualizar_usuario = null;
