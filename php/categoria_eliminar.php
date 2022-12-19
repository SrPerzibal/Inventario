<?php
$category_id_del = limpiar_cadena($_GET['category_id_del']);

# Verificando categoría #
$check_categoria = conexion();
$check_categoria = $check_categoria->query("SELECT id_categoria FROM categoria WHERE id_categoria = '$category_id_del'");

if ($check_categoria->rowCount() == 1) {

    # Verificando productos #
    $check_productos = conexion();
    $check_productos = $check_productos->query("SELECT id_categoria FROM producto WHERE id_categoria = '$category_id_del' LIMIT 1");

    if ($check_productos->rowCount() <= 0) {
        $eliminar_categoria = conexion();
        $eliminar_categoria = $eliminar_categoria->prepare("DELETE FROM categoria WHERE id_categoria = :id");

        $eliminar_categoria->execute([":id" => $category_id_del]);

        if ($eliminar_categoria->rowCount() == 1) {
            echo '
            <div class="notification is-info is-light">
                <strong>¡Categoría eliminada!</strong><br>
                Los datos de la categoría han sido eliminados
            </div>
        ';
        } else {
            echo '
            <div class="notification is-danger is-light">
                <strong>¡Opss!</strong><br>
                No se pudo eliminar la categoría, intenta nuevamente
            </div>
        ';
        }

        $eliminar_categoria = null;
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Opss!</strong><br>
                No se pudo eliminar la categoria ya que tiene productos registrados
            </div>
        ';
    }

    $check_productos = null;
} else {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Opss!</strong><br>
                La CATEGORIA que intenta eliminar no existe
            </div>
        ';
}

$check_categoria = null;
