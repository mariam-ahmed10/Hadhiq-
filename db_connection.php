<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            $servername = "localhost";  
            $username = "root";         
            $password = "";         
            $dbname = "_____dhiq (3)";          

            $conn = mysqli_connect($servername, $username, $password, $dbname);

            if (!$conn) {
                die("Connection failed:" . mysqli_connect_error());
            }
        ?>

    </body>
</html>
