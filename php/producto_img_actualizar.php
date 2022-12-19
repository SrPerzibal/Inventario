<?php
require_once "main.php";

$product_id = limpiar_cadena($_POST['img_up_id']);

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

/*== Comprobar si se seleccionó imagen ==*/
if ($_FILES['producto_foto']['name'] == "" || $_FILES['producto_foto']['size'] == 0) {
   echo '
   <div class="notification is-danger is-light">
       <strong>¡Ocurrio un error inesperado!</strong><br>
       No ha seleccionado una imagen válida
   </div>
';
   exit();
}

/*== Creando directorio ==*/
if (!file_exists($img_dir)) {
   if (!mkdir($img_dir, 0777)) {
      echo '
       <div class="notification is-danger is-light">
           <strong>¡Ocurrio un error inesperado!</strong><br>
           Error al crear el directorio
       </div>
   ';
      exit();
   }
}

/* Cambiando permisos al directorio */
chmod($img_dir, 0777);

/*== Verificando formato de imágenes ==*/
if (
   mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/jpeg" &&
   mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/png"
) {
   echo '
       <div class="notification is-danger is-light">
           <strong>¡Ocurrio un error inesperado!</strong><br>
           La imagen que ha seleccionado no tiene el formato permitido
       </div>
   ';
   exit();
}

/*== Verificando peso de imágenes ==*/
if (($_FILES['producto_foto']['size'] / 1024) > 3072) {
   echo '
       <div class="notification is-danger is-light">
           <strong>¡Ocurrio un error inesperado!</strong><br>
           La imagen que ha seleccionado supera el peso permitido
       </div>
   ';
   exit();
}

/*== Extensión de la imagen ==*/
switch (mime_content_type($_FILES['producto_foto']['tmp_name'])) {
   case 'image/jpeg':
      $img_ext = ".jpg";
      break;
   case 'image/png':
      $img_ext = ".png";
      break;
}

/* Nombre de la imagen */
$img_nombre = renombrar_fotos($datos['nombre_producto']);

/* Nombre final de la imagen */
$foto = $img_nombre . $img_ext;

/*== Moviendo imagen al directorio ==*/
if (!move_uploaded_file($_FILES['producto_foto']['tmp_name'], $img_dir . $foto)) {
   echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No podemos cargar la imagen al sistema
        </div>
    ';
   exit();
}

if (is_file($img_dir . $datos['foto_producto']) && $datos['foto_producto'] != $foto) {
   chmod($img_dir . $datos['foto_producto'], 0777);
   unlink($img_dir . $datos['foto_producto']);
}

# Actualizando datos #
$actualizar_producto = conexion();
$actualizar_producto = $actualizar_producto->prepare("UPDATE producto SET foto_producto = :foto WHERE id_producto = :id");

$marcadores = [
   ":foto" => $foto,
   ":id" => $product_id
];

if ($actualizar_producto->execute($marcadores)) {
   echo '
            <div class="notification is-info is-light">
               <strong>¡Imagen o Foto Actualizada!</strong><br>
               La imagen del producto ha sido Actualizada con éxito, pulse Aceptar para guardar los cambios.

            <p class="has-text-centered pt-5 pb-5" >
               <a href="index.php?vista=product_img&product_id_up=' . $product_id . '" class="button is-link is-rounded">Aceptar</a>
            </p>
              </div>
          ';
} else {
   if (is_file($img_dir . $foto)) {
      chmod($img_dir . $foto['foto_producto'], 0777);
      unlink($img_dir . $foto);
   }
   echo '
      <div class="notification is-warning is-light">
         <strong>¡Opss!</strong><br>
         No podemos subir la imagen, por favor intenta nuevamente
      </div>';
}

$actualizar_producto = null;
