<?php
include "config/db.php";
$q = mysqli_query($conn, "SHOW COLUMNS FROM projects");
while($r = mysqli_fetch_assoc($q)) {
    echo $r['Field'] . "\n";
}
