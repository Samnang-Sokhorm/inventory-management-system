<?php
// inc/navigation.php - NAVIGATION ONLY, NO FUNCTION DECLARATION

// Only include language.php if t() function doesn't exist
if (!function_exists('t')) {
    require_once(__DIR__ . '/language.php');
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Language switcher styles */
        .language-switcher {
            display: flex;
            gap: 5px;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px;
            border-radius: 5px;
        }
        
        .language-switcher .btn-lang {
            padding: 5px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 14px;
        }
        
        .language-switcher .btn-lang.active {
            background: #007bff;
            color: white;
        }
        
        .language-switcher .btn-lang:not(.active) {
            background: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
        }
        
        .language-switcher .btn-lang:not(.active):hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }
        
        /* Navbar item alignment */
        .nav-item.lang-item {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <!-- Logo Image -->
            <img src="https://numer.digital/public/template/university/images/logo/num.png" 
                 width="30" height="30" class="d-inline-block align-top" alt="Logo">
            <?php echo t('inventory_system'); ?>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" 
                data-target="#navbarResponsive" aria-controls="navbarResponsive" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <!-- Language Switcher -->
                <li class="nav-item lang-item">
                    <div class="language-switcher">
                        <a href="?lang=en" class="btn-lang <?php echo ($_SESSION['lang'] ?? 'en') == 'en' ? 'active' : ''; ?>">
                            EN
                        </a>
                        <a href="?lang=km" class="btn-lang <?php echo ($_SESSION['lang'] ?? 'en') == 'km' ? 'active' : ''; ?>">
                            ខ្មែរ
                        </a>
                    </div>
                </li>
                
                <li class="nav-item">
                    <span class="nav-link"> | </span>
                </li>
                
                <!-- Welcome Message -->
                <li class="nav-item">
                    <span class="nav-link">
                        <?php echo t('welcome'); ?> <?php echo htmlspecialchars($_SESSION['fullName'] ?? 'User'); ?>
                    </span>
                </li>
                
                <li class="nav-item">
                    <span class="nav-link"> | </span>
                </li>
                
                <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link" href="model/login/logout.php">
                        <?php echo t('logout'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>