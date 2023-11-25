<?php
if(!$is_login) die('<script>location.replace("?")</script>');
unset($_SESSION['lshop_username']);
die('<script>location.replace("?")</script>');
