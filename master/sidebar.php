<div class="overlay-sm"></div>
    <!-- Start Slidebar -->
    <aside class="slidebar">
        <div class="slidebar-brand">
            <div class="logo">
				<img src="assets/images/logo-white.svg" alt="logo" class="img-fluid" >
			</div>
        </div>
        <div class="slidebar-menu">
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a></li>

                <?php if ($_SESSION['user']['role'] === 'Admin' || $_SESSION['user']['role'] === 'Responsable de Stock'){ ?>
                <li><a href="categories.php"><i class="fas fa-layer-group"></i> <span>Categories</span></a></li>
                <?php }?>

                <li><a href="materiels.php"><i class="fas fa-boxes"></i> <span>Matériel</span></a></li>

                <?php if ($_SESSION['user']['role'] === 'Admin'){ ?>
                <li><a href="utilisateurs.php"><i class="fas fa-users"></i> 
                <span>Utilisateurs</span></a></li>
                <li><a href="services.php"><i class="fas fa-cogs"></i> <span>Services</span></a></li>
                <?php }?>

                <li><a href="demandes.php"><i class="fas fa-history"></i> <span>Demandes</span></a></li>
            </ul>
        </div>
    </aside>
    <!-- End Slidebar -->
    <main>
		<!-- Start Navigation Bar -->
		<header>
			<nav>
                <div class="top-navbar">
                    <div class="toggle-menu">
                        <i class="fas fa-bars"></i>
                        <span>Tableau de bord</span>
                    </div>
                    <div>
                        <div class="admin-profile  d-flex justify-content-between">
                            <div class="admin-info">
                                <h4><?php echo $_SESSION['user']['prenom'][0]. ". " . $_SESSION['user']['nom'] ; ?></h4>
                                <p class="text-primary"><?php echo $_SESSION['user']['role']; ?></p>
                            </div>
                            <a href="logout.php" class="btn btn-danger mx-3" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                                <form method="POST" id="logout-form" action="logout.php">
                                    
                                </form>
                        </div>
                        </div>
                    </div>
                </div>

                <div aria-label="breadcrumb" id="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    </ol>
                </div>
				
                
			</nav>
		</header>
		<!-- End Navigation Bar -->