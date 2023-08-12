<?php
session_start();
require_once "database/database.php";
if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin'){
// ---------------------- Aff info services ------------------

$db = Database::connect();
$req = $db->prepare("SELECT * FROM services;");
$req->execute();
$services = $req->fetchAll(PDO::FETCH_ASSOC);

// ----------------------- ajouter un services ------------------

if(isset($_POST['save']) && isset($_POST['libelle'])){
    $libelle = $_POST['libelle'];
    $insert = $db->prepare("INSERT INTO services(libelle) VALUES(?);");
    $insert->execute([$libelle]);
    header("location: services.php");
}

// ------------------------ modifier un categorie ---------------------

if(isset($_POST['edit']) && isset($_POST['libelle'])){
    $libelle = $_POST['libelle'];
    $serv_id = $_POST['serv_id'];
    $update = $db->prepare("UPDATE services SET libelle = ? WHERE id = ?;");
    $update->execute([$libelle,$serv_id]);
    header("location: services.php");
}

// ------------------------ Supprimer un categorie ---------------------

if(isset($_POST['delete']) && isset($_POST['serv_id'])){
    $serv_id = $_POST['serv_id'];
    $delete = $db->prepare("DELETE FROM services WHERE id = ?;");
    $delete->execute([$serv_id]);
    header("location: services.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<?php require_once "master/head.php"; ?>
<body>
<?php require_once "master/sidebar.php" ?>

        <div class="main-users">
            <div class="users card">
                <div class="table-header card-header">
                    <h3><i class="fas fa-cogs"></i> Services</h3>
                    <a href="#" uk-toggle="target: #modal-close-default"><i class="fas fa-plus"></i> Ajouter un service</a>

                    <div id="modal-close-default" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <button class="uk-modal-close-default" type="button" uk-close></button>
                            <h2 class="uk-modal-title">Ajouter un service</h2>
                            <form action="services.php" method="post">
                                <div class="inputs-group">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <label for="libelle" class="mb-1">Libellé <span class="required" title="required">*</span></label>
                                            <input class="uk-input" type="text" placeholder="Nom du service" id="libelle" name="libelle" required>
                                        </div>
                                    </div>
                                </div>
                                <p class="uk-text-right">
                                    <button class="btn btn-secondary uk-modal-close" type="button"><i class="fas fa-times"></i> Annuler</button>
                                    <button class="btn btn-primary" type="submit" name="save"><i class="fas fa-plus"></i> Ajouter</button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="uk-overflow-auto card-body">
                    <table class="uk-table uk-table-divider uk-table-hover" >
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Libellé</th>
                                <th  style="text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($services as $service){ ?>
                            <tr>
                                <td><?php echo $service['id']; ?></td>
                                <td><?php echo $service['libelle']; ?></td>
                                <td style="text-align:center;">
                                    <a href="#" uk-toggle="target: #modal-close-default-edit-<?php echo $service['id']; ?>"><i class="far fa-edit"></i></a>

                                    <div id="modal-close-default-edit-<?php echo $service['id']; ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Modifier un service</h2>
                                            <form action="#" method="post">
                                                <div class="inputs-group">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <label for="libelle" class="mb-1">Libellé <span class="required" title="required">*</span></label>
                                                            <input class="uk-input" type="text" placeholder="Nom du categorie" id="libelle" name="libelle" value="<?php echo $service['libelle']; ?>">
                                                            <input type="hidden" name="serv_id" value="<?php echo $service['id']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="uk-text-right">
                                                    <button class="btn btn-secondary uk-modal-close" type="button"><i class="fas fa-times"></i> Annuler</button>
                                                    <button class="btn btn-success" type="submit" name="edit"><i class="far fa-edit"></i> Modifier</button>
                                                </p>
                                            </form>
                                        </div>
                                    </div>
                                    <a href="#" uk-toggle="target: #modal-close-default-delete-<?php echo $service['id']; ?>"><i class="far fa-trash-alt"></i></a>

                                    <div id="modal-close-default-delete-<?php echo $service['id']; ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Supprimer un service</h2>
                                            <form action="services.php" method="POST">
                                                <div class="inputs-group">
                                                    <div class="row">
                                                        <div class="col">
                                                            <input type="hidden" name="serv_id" value="<?php echo $service['id']; ?>">
                                                            <p>Êtes-vous sûr Voulez-vous supprimer ce service ?</p>
                                                        </div>
                                                    </div>
                                                    <p class="uk-text-right">
                                                        <button class="btn btn-secondary uk-modal-close" type="button"><i class="fas fa-times"></i> Annuler</button>
                                                        <button class="btn btn-danger" type="submit" name="delete"><i class="far fa-trash-alt"></i> Supprimer</button>
                                                    </p>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php require_once "master/footer.php"; ?>
</body>
</html>
<?php }else{
    header("location: login.php");
} ?>