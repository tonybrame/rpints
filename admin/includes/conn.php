<?php 
   $host="localhost"; // Host name
   $username="root"; // Mysql username
   $password=""; // Mysql password
   $db_name="raspberrypints"; // Database name
   $tbl_name="users";
   //show/hide SQL statements in errors
   //$showSqlState = true;
   //Connect to server and select databse.
   $mysqli = new mysqli("$host", "$username", "$password", "$db_name")or die("cannot connect to server");
?>