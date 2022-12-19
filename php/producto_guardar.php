<?php
require_once "../inc/session_start.php";

require_once "main.php";

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

/*== Verificando nombre ==*/
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

/*== Verificando categoría ==*/
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

/*== Directorio de imágenes ==*/
$img_dir = "../img/producto/";

/*== Comprobar si se seleccionó imagen ==*/

if ($_FILES['producto_foto']['name'] != "" && $_FILES['producto_foto']['size'] > 0) {

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

    /* Cambiando permisos al directorio */
    chmod($img_dir, 0777);

    /* Nombre de la imagen */
    $img_nombre = renombrar_fotos($nombre);

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
} else {
    $foto = "";
}

/*== Guardando datos ==*/

$guardar_producto = conexion();
$guardar_producto = $guardar_producto->prepare("INSERT INTO producto(codigo_producto, nombre_producto, precio_producto, stock_producto, foto_producto, id_categoria, id_usuario) 
   VALUES(:codigo, :nombre, :precio, :stock, :foto, :categoria, :usuario)");

$marcadores = [
    ":codigo" => $codigo,
    ":nombre" => $nombre,
    ":precio" => $precio,
    ":stock" => $stock,
    ":foto" => $foto,
    ":categoria" => $categoria,
    ":usuario" => $_SESSION['id']
];

$guardar_producto->execute($marcadores);

if ($guardar_producto->rowCount() == 1) {
    echo '
           <div class="notification is-info is-light">
               <strong>¡PRODUCTO REGISTRADO!</strong><br>
               El producto se registro con exito
           </div>
       ';
} else {

    if (is_file($img_dir . $foto)) {
        chmod($img_dir . $foto, 0777);
        unlink($img_dir . $foto);
    }
    echo '
           <div class="notification is-danger is-light">
               <strong>¡Ocurrio un error inesperado!</strong><br>
               No se pudo registrar el producto, por favor intente nuevamente
           </div>
       ';
}
$guardar_producto = null;
