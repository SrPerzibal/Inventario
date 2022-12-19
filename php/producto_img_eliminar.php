<?php
require_once "main.php";

$product_id = limpiar_cadena($_POST['img_del_id']);

# Verificar producto #
$check_producto = conexion();
$check_producto = $check_producto->query("SELECT * FROM producto WHERE id_producto = '$product_id'");

if ($check_producto->rowCount() == 1) {
   $datos = $check_producto->fetch();
} else {
   echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La imagen del producto que intenta eliminar no existe en el sistema
            </div>
        ';
   exit();
}
$check_producto = null;

/*== Directorio de imágenes ==*/
$img_dir = "../img/producto/";

/* Cambiando permisos al directorio */
chmod($img_dir, 0777);

if (is_file($img_dir . $datos['foto_producto'])) {
   chmod($img_dir . $datos['foto_producto'], 0777);

   if (!unlink($img_dir . $datos['foto_producto'])) {
      echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                Error al intentar eliminar la imagen del producto, por favor intente nuevamente
            </div>
        ';
      exit();
   }
}

# Actualizando datos #
$actualizar_producto = conexion();
$actualizar_producto = $actualizar_producto->prepare("UPDATE producto SET foto_producto = :foto WHERE id_producto = :id");

$marcadores = [
   ":foto" => "",
   ":id" => $product_id
];

if ($actualizar_producto->execute($marcadores)) {
   echo '
            <div class="notification is-info is-light">
               <strong>¡Imagen o Foto Eliminada!</strong><br>
               La imagen del producto ha sido eliminada con éxito, pulse Aceptar para guardar los cambios.

            <p class="has-text-centered pt-5 pb-5" >
               <a href="index.php?vista=product_img&product_id_up=' . $product_id . '" class="button is-link is-rounded">Aceptar</a>
            </p>
              </div>
          ';
} else {
   echo '
      <div class="notification is-warning is-light">
         <strong>¡Imagen o Foto Eliminada!</strong><br>
         Ocurrieron algunos inconvenientes, sin embargo la imagen del producto ha sido eliminada, pulse Aceptar para guardar los cambios.

         <p class="has-text-centered pt-5 pb-5" >
            <a href="index.php?vista=product_img&product_id_up=' . $product_id . '" class="button is-link is-rounded">Aceptar</a>
         </p>
      </div>';
}

$actualizar_producto = null;
