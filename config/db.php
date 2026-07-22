<?php
$conn = mysqli_connect(
"localhost",
"root",
"",
"fee_management"
);

if(!$conn)
{
die("Database Connection Failed");
}
?>