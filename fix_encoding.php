<?php
// Fix double-encoded UTF-8 in database
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);
require_once ROOT . 'app' . DS . 'config.php';

$pdo = new PDO(
    sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME, DB_USER, DB_PASS),
    DB_USER,
    DB_PASS
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function fixDoubleEncoding($str) {
    if (empty($str)) return $str;
    // Intentar corregir doble codificación
    $decoded = @iconv('UTF-8', 'ISO-8859-1//IGNORE', $str);
    if ($decoded !== false) {
        $reencoded = @iconv('ISO-8859-1', 'UTF-8', $decoded);
        if ($reencoded !== false && strlen($reencoded) < strlen($str)) {
            return $reencoded;
        }
    }
    // Si no es UTF-8 válido, convertir desde ISO-8859-1
    if (!mb_check_encoding($str, 'UTF-8')) {
        return mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
    }
    return $str;
}

// Tablas y campos a corregir (tabla => [campos, clave_primaria])
$fixes = [
    'socios' => ['campos' => ['nombre', 'apellido', 'domicilio', 'email'], 'pk' => 'id_socio'],
    'parientes' => ['campos' => ['nombre', 'apellido'], 'pk' => 'id_pariente'],
    'categorias' => ['campos' => ['nombre'], 'pk' => 'id_categoria'],
];

$updated = 0;
foreach ($fixes as $table => $config) {
    $fields = $config['campos'];
    $pk = $config['pk'];
    foreach ($fields as $field) {
        $stmt = $pdo->query("SELECT `$pk`, `$field` FROM `$table` WHERE `$field` IS NOT NULL AND `$field` != ''");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($rows as $row) {
            $original = $row[$field];
            $fixed = fixDoubleEncoding($original);
            
            if ($fixed !== $original) {
                $update = $pdo->prepare("UPDATE `$table` SET `$field` = ? WHERE `$pk` = ?");
                $update->execute([$fixed, $row[$pk]]);
                echo "[$table.$field] {$pk} {$row[$pk]}: '$original' -> '$fixed'\n";
                $updated++;
            }
        }
    }
}

echo "\nTotal corregidos: $updated\n";
