<?php
session_start();
require_once "database/database.php";
	if(isset($_SESSION['user'])){
    $db = Database::connect();
	$reqProd = $db->prepare("SELECT COUNT(id) as nbrProd FROM materiels;");
	$reqProd->execute();
	$nbrProd = $reqProd->fetch(PDO::FETCH_ASSOC);

	$reqDemandes = $db->prepare("SELECT COUNT(id) as nbrDemandes FROM demandes;");
	$reqDemandes->execute();
	$nbrDemandes = $reqDemandes->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<?php require_once "master/head.php"; ?>
<body>
    <?php require_once "master/sidebar.php" ?>

        <!-- info TB total sur demande et matériel -->
		<?php if ($_SESSION['user']['role'] === 'Admin' || $_SESSION['user']['role'] === 'Responsable de Stock'){ ?>
		<section class="data-analysis">
			<div class="box">
				<div class="data">
					<h1 class="quantity"><?php echo $nbrProd['nbrProd']; ?></h1>
					<p>Total du matériel</p>
				</div>
				
			</div>
			<div class="box">
				<div class="data">
					<h1 class="quantity"><?php echo $nbrDemandes['nbrDemandes']; ?></h1>
					<p>Total des demandes</p>
				</div>
			
			</div>
		</section>
		<?php } ?>
		<!--fin TB  -->
		
        <section class="traffic-analysis">
			
			<h1 class="welcome"> Heureux de vous voir ici STOCK-OCP-SI!</h1>
			
        </section>


    </main>
    <?php require_once "master/footer.php"; ?>
</body>
</html>
<?php }else{
	header("location: login.php");
} ?>