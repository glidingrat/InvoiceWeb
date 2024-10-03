<?php
session_start();

include 'db_connection.php'; // Zahrň připojení k databázi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ověření uživatelského jména
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) { // Kontrola, zda bylo příkazové pole úspěšně vytvořeno
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $username_db, $hashed_password);

        if ($stmt->fetch()) {
            // Ověření hesla
            if (password_verify($password, $hashed_password)) {
                $_SESSION['username'] = $username_db;
                header("Location: homepage.php"); // Přesměrování na hlavní stránku po přihlášení
                exit;
            } else {
                // Nesprávné heslo
                echo "<script>alert('Nesprávné heslo.');</script>";
            }
        } else {
            // Uživatel neexistuje
            echo "<script>alert('Uživatel neexistuje.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Došlo k chybě při přípravě dotazu.');</script>";
    }
}

$conn->close();
?>
