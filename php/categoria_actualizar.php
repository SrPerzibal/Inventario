<?php
require_once "main.php";

$id = limpiar_cadena($_POST['categoria_id']);

# Verificar categoria #
$check_categoria = conexion();
$check_categoria = $check_categoria->query("SELECT * FROM categoria WHERE id_categoria = '$id'");

if ($check_categoria->rowCount() <= 0) {
   echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La categoría no existe en el sistema
            </div>
        ';
   exit();
} else {
   $datos = $check_categoria->fetch();
}
$check_categoria = null;

/*== Almacenando datos ==*/
$nombre = limpiar_cadena($_POST['categoria_nombre']);
$ubicacion = limpiar_cadena($_POST['categoria_ubicacion']);

/*== Verificando campos obligatorios ==*/
if ($nombre == "") {
   echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               No has llenado todos los campos que son obligatorios
           </div>
       ';
   exit();
}

/*== Verificando integridad de los datos ==*/
if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}", $nombre)) {
   echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               El NOMBRE no coincide con el formato solicitado
           </div>
       ';
   exit();
}

if ($ubicacion != "") {
   if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}", $ubicacion)) {
      echo '
              <div class="notification is-danger is-light">
                  <strong>¡Ocurrio un error inesperado!</strong><br>
                  La UBICACION no coincide con el formato solicitado
              </div>
          ';
      exit();
   }
}

/*== Verificando nombre ==*/
if ($nombre != $datos['nombre_categoria']) {
   $check_nombre = conexion();
   $check_nombre = $check_nombre->query("SELECT nombre_categoria FROM categoria WHERE nombre_categoria='$nombre'");
   if ($check_nombre->rowCount() > 0) {
      echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE ingresado ya se encuentra registrado, por favor elija otro
            </div>
        ';
      exit();
   }
   $check_nombre = null;
}

# Actualizando datos #
$actualizar_categoria = conexion();
$actualizar_categoria = $actualizar_categoria->prepare("UPDATE categoria SET nombre_categoria = :nombre, ubicacion_categoria = :ubicacion WHERE id_categoria = :id");

$marcadores = [
   ":nombre" => $nombre,
   ":ubicacion" => $ubicacion,
   ":id" => $id
];

if ($actualizar_categoria->execute($marcadores)) {
   echo '
              <div class="notification is-info is-light">
                  <strong>¡Categoría Actualizada!</strong><br>
                  La categoría se actualizó con éxito
              </div>
          ';
} else {
   echo '
      <div class="notification is-danger is-light">
         <strong>¡Opss!</strong><br>
         No se pudo actualizar la categoría, por favor intenta de nuevo
      </div>';
}

$actualizar_categoria = null;
