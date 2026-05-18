<?php
echo "<h1>Test MongoDB PHP sur AlwaysData</h1>";
echo "<h2>Informations PHP</h2>";
echo "<p><strong>Version PHP :</strong> " . phpversion() . "</p>";
echo "<h2>Test extension MongoDB</h2>";
if (extension_loaded('mongodb')) {
    echo "<p style='color:green;'>OK - Extension mongodb CHARGEE - Version: " . phpversion('mongodb') . "</p>";
} else {
    echo "<p style='color:red;'>KO - Extension mongodb NON CHARGEE</p>";
}
echo "<h2>Test classe MongoDB Driver Manager</h2>";
if (class_exists('MongoDB\Driver\Manager')) {
    echo "<p style='color:green;'>OK - Classe disponible</p>";
} else {
    echo "<p style='color:red;'>KO - Classe NON disponible</p>";
}
echo "<hr><h2>Liste des extensions PHP chargees</h2><pre>";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    if (stripos($ext, 'mongo') !== false) {
        echo ">>> $ext <<<\n";
    } else {
        echo "$ext\n";
    }
}
echo "</pre>";
?>
