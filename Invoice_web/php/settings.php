<?php
session_start();
include 'db_connection.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Get user information
$username = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT first_name, last_name, email, password FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Get company details
$company_stmt = $pdo->prepare("SELECT company_name, street_address, postal_city, phone FROM company_details WHERE user_id = (SELECT id FROM users WHERE username = ?)");
$company_stmt->execute([$username]);
$company = $company_stmt->fetch();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // User information
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $new_username = $_POST['username']; // New username
    $current_password = $_POST['current_password']; // Current password
    $new_password = $_POST['new_password']; // New password
    $company_update_password = $_POST['company_update_password']; // Password for company info update

    // User info update
    if ($_POST['form_type'] === 'user_info') {
        // Verify current password
        if (password_verify($current_password, $user['password'])) {
            // Update user information
            $update_data = [$first_name, $last_name, $email, $new_username, $username];
            
            // If new password is provided, hash it and include it in the update
            if (!empty($new_password)) {
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_data[3] = $hashed_new_password; // Update the password in the array
                $updateStmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, username = ?, password = ? WHERE username = ?");
                $updateStmt->execute($update_data);
            } else {
                $updateStmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, username = ? WHERE username = ?");
                $updateStmt->execute([$first_name, $last_name, $email, $new_username, $username]);
            }

            // Update session
            $_SESSION['username'] = $new_username; // Save new username in session

            // Redirect back to settings page with success message
            header("Location: settings.php?success_user=Údaje o uživateli byly úspěšně aktualizovány.");
            exit();
        } else {
            $error_message = "Aktuální heslo je nesprávné.";
        }
    }

    // Company information update
    if ($_POST['form_type'] === 'company_info') {
        if (password_verify($company_update_password, $user['password'])) {
            $company_name = $_POST['company_name'];
            $street_address = $_POST['street_address'];
            $postal_city = $_POST['postal_city'];
            $phone = $_POST['phone'];

            // Update company information
            if ($company) {
                $updateCompanyStmt = $pdo->prepare("UPDATE company_details SET company_name = ?, street_address = ?, postal_city = ?, phone = ? WHERE user_id = (SELECT id FROM users WHERE username = ?)");
                $updateCompanyStmt->execute([$company_name, $street_address, $postal_city, $phone, $username]);
            } else {
                $insertCompanyStmt = $pdo->prepare("INSERT INTO company_details (user_id, company_name, street_address, postal_city, phone) VALUES ((SELECT id FROM users WHERE username = ?), ?, ?, ?, ?)");
                $insertCompanyStmt->execute([$username, $company_name, $street_address, $postal_city, $phone]);
            }

            header("Location: settings.php?success_company=Údaje o společnosti byly úspěšně aktualizovány.");
            exit();
        } else {
            $company_error_message = "Aktuální heslo je nesprávné pro změnu údajů o společnosti.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nastavení uživatelského účtu</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
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

    <div class="container">
        <div class="form-box">
            <h1>Nastavení uživatelského účtu</h1>

            <?php if (isset($_GET['success_user'])): ?>
                <p style="color: green;"><?php echo htmlspecialchars($_GET['success_user']); ?></p>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form action="settings.php" method="post">
                <input type="hidden" name="form_type" value="user_info">
                <div class="input-group">
                    <label for="username">Uživatelské jméno</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div class="input-group">
                    <label for="first_name">Jméno</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="last_name">Příjmení</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <h3>Změna hesla</h3>
                <div class="input-group">
                    <label for="current_password">Aktuální heslo / potvrzení</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="input-group">
                    <label for="new_password">Nové heslo</label>
                    <input type="password" id="new_password" name="new_password">
                </div>
                <button type="submit" class="btn">Uložit změny</button>
            </form>
        </div>

        <div class="form-box">
            <h1>Údaje o společnosti</h1>

            <?php if (isset($_GET['success_company'])): ?>
                <p style="color: green;"><?php echo htmlspecialchars($_GET['success_company']); ?></p>
            <?php endif; ?>

            <?php if (isset($company_error_message)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($company_error_message); ?></p>
            <?php endif; ?>
            
            <form action="settings.php" method="post">
                <input type="hidden" name="form_type" value="company_info">
                <div class="input-group">
                    <label for="company_name">Název společnosti</label>
                    <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($company['company_name'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="street_address">Ulice a číslo domu</label>
                    <input type="text" id="street_address" name="street_address" value="<?php echo htmlspecialchars($company['street_address'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="postal_city">PSČ Město</label>
                    <input type="text" id="postal_city" name="postal_city" value="<?php echo htmlspecialchars($company['postal_city'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="phone">Telefon</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($company['phone'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="company_update_password">Heslo pro potvrzení</label>
                    <input type="password" id="company_update_password" name="company_update_password" required>
                </div>
                <button type="submit" class="btn">Uložit změny</button>
            </form>
        </div>
    </div>
</body>
</html>
