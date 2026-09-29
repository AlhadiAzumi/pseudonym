<?php

require 'config/database.php';

try {

    getDB();

    echo "✅ Database Connected";

} catch (Exception $e) {

    echo $e->getMessage();

}