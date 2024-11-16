<?php
// Začátek session pro načítání uživatelských dat
session_start();

// Předpokládejme, že po přihlášení uložíš uživatelské jméno do session
// například: $_SESSION['username'] = 'Jan Novak';

// Kontrola, zda je uživatel přihlášen
if (!isset($_SESSION['username'])) {
    // Pokud není přihlášen, přesměruj ho na login page
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username']; // Získání jména přihlášeného uživatele
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--
    <link rel="stylesheet" href="../css/style_Detektiv.css">
    <link rel="stylesheet" href="../css/response.css">
-->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" href="data:,">
    <title>InvoiceNow</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body id="About">
    <header>
        <span class="logo">InvoiceNow</span>
        <nav>
            <ul class="nav_links">
                <li><a href="../php/homepage.php">Faktury</a></li>
                <li><a href="../php/settings.php">Nastavení</a></li>
            </ul>
        </nav>
        
        <div class="user-info">
            <span id="username"><strong><?php echo htmlspecialchars($username); ?></strong></span>
            <a href="logout.php" class="btn-logout">Odhlásit se</a>
        </div>
      
    </header>

    <main>

    
    </main>

</body>
</html>
