<?php
require_once "main.php";

$id = limpiar_cadena($_POST['producto_id']);

# Verificar producto #
$check_producto = conexion();
$check_producto = $check_producto->query("SELECT * FROM producto WHERE id_producto = '$id'");

if ($check_producto->rowCount() <= 0) {
   echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El producto no existe en el sistema
            </div>
        ';
   exit();
} else {
   $datos = $check_producto->fetch();
}
$check_producto = null;

/*== Almacenando datos ==*/
$codigo = limpiar_cadena($_POST['producto_codigo']);
$nombre = limpiar_cadena($_POST['producto_nombre']);
$precio = limpiar_cadena($_POST['producto_precio']);
$stock = limpiar_cadena($_POST['producto_stock']);
$categoria = limpiar_cadena($_POST['producto_categoria']);

/*== Verificando campos obligatorios ==*/
if ($codigo == "" || $nombre == "" || $precio == "" || $stock == "" || $categoria == "") {
   echo '
          <div class="notification is-danger is-light">
              <strong>¡Ocurrio un error inesperado!</strong><br>
              No has llenado todos los campos que son obligatorios
          </div>
      ';
   exit();
}

/*== Verificando integridad de los datos ==*/
if (verificar_datos("[a-zA-Z0-9- ]{1,70}", $codigo)) {
   echo '
          <div class="notification is-danger is-light">
              <strong>¡Ocurrio un error inesperado!</strong><br>
              El CODIGO no coincide con el formato solicitado
          </div>
      ';
   exit();
}

if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}", $nombre)) {
   echo '
          <div class="notification is-danger is-light">
              <strong>¡Ocurrio un error inesperado!</strong><br>
              El NOMBRE no coincide con el formato solicitado
          </div>
      ';
   exit();
}

if (verificar_datos("[0-9.]{1,25}", $precio)) {
   echo '
          <div class="notification is-danger is-light">
              <strong>¡Ocurrio un error inesperado!</strong><br>
              El PRECIO no coincide con el formato solicitado
          </div>
      ';
   exit();
}

if (verificar_datos("[0-9]{1,25}", $stock)) {
   echo '
          <div class="notification is-danger is-light">
              <strong>¡Ocurrio un error inesperado!</strong><br>
              El STOCK no coincide con el formato solicitado
          </div>
      ';
   exit();
}

/*== Verificando código de barras ==*/
if ($codigo != $datos['codigo_producto']) {
   $check_codigo = conexion();
   $check_codigo = $check_codigo->query("SELECT codigo_producto FROM producto WHERE codigo_producto='$codigo'");
   if ($check_codigo->rowCount() > 0) {
      echo '
               <div class="notification is-danger is-light">
                   <strong>¡Ocurrio un error inesperado!</strong><br>
                   El CODIGO ingresado ya se encuentra registrado, por favor elija otro
               </div>
           ';
      exit();
   }
   $check_codigo = null;
}

/*== Verificando nombre ==*/
if ($nombre != $datos['nombre_producto']) {
   $check_nombre = conexion();
   $check_nombre = $check_nombre->query("SELECT nombre_producto FROM producto WHERE nombre_producto='$nombre'");
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

/*== Verificando categoría ==*/
if ($categoria != $datos['id_categoria']) {
   $check_categoria = conexion();
   $check_categoria = $check_categoria->query("SELECT id_categoria FROM categoria WHERE id_categoria='$categoria'");
   if ($check_categoria->rowCount() <= 0) {
      echo '
               <div class="notification is-danger is-light">
                   <strong>¡Ocurrio un error inesperado!</strong><br>
                   La CATEGORIA seleccionada no existe
               </div>
           ';
      exit();
   }
   $check_categoria = null;
}

# Actualizando datos #
$actualizar_producto = conexion();
$actualizar_producto = $actualizar_producto->prepare("UPDATE producto SET codigo_producto = :codigo, nombre_producto = :nombre, 
precio_producto = :precio, stock_producto = :stock, id_categoria = :categoria WHERE id_producto = :id");

$marcadores = [
   ":codigo" => $codigo,
   ":nombre" => $nombre,
   ":precio" => $precio,
   ":stock" => $stock,
   ":categoria" => $categoria,
   ":id" => $id
];

if ($actualizar_producto->execute($marcadores)) {
   echo '
              <div class="notification is-info is-light">
                  <strong>¡Producto Actualizado!</strong><br>
                  El producto se actualizó con éxito
              </div>
          ';
} else {
   echo '
      <div class="notification is-danger is-light">
         <strong>¡Opss!</strong><br>
         No se pudo actualizar el producto, por favor intenta de nuevo
      </div>';
}

$actualizar_producto = null;
