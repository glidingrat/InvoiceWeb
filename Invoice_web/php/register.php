<?php
// Připojení k databázi

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Získání údajů z formuláře
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashování hesla
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Vložení do databáze
    $sql = "INSERT INTO users (username, password, first_name, last_name, email) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $password, $first_name, $last_name, $email);

    if ($stmt->execute()) {
        echo "Registrace úspěšná. <a href='login.php'>Přihlásit se</a>";
    } else {
        echo "Chyba: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
