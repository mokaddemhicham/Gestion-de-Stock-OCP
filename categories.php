<?php
session_start();
require_once "database/database.php";
if(isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'Admin' || $_SESSION['user']['role'] === 'Responsable de Stock')){
// ---------------------- récuper info categories ------------------

$db = Database::connect();
$req = $db->prepare("SELECT * FROM categories;");
$req->execute();
$categories = $req->fetchAll(PDO::FETCH_ASSOC);

// ----------------------- ajouter une categorie ------------------

if(isset($_POST['save']) && isset($_POST['libelle'])){
    $libelle = $_POST['libelle'];
    $insert = $db->prepare("INSERT INTO categories(libelle) VALUES(?);");
    $insert->execute([$libelle]);
    header("location: categories.php");
}

// ------------------------ Modifer une categorie ---------------------

if(isset($_POST['edit']) && isset($_POST['libelle'])){
    $libelle = $_POST['libelle'];
    $cat_id = $_POST['cat_id'];
    $update = $db->prepare("UPDATE categories SET libelle = ? WHERE id = ?;");
    $update->execute([$libelle,$cat_id]);
    header("location: categories.php");
}

// ------------------------ Supprimer une categorie ---------------------

if(isset($_POST['delete']) && isset($_POST['cat_id'])){
    $cat_id = $_POST['cat_id'];
    $delete = $db->prepare("DELETE FROM categories WHERE id = ?;");
    $delete->execute([$cat_id]);
    header("location: categories.php");
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
                    <h3><i class="fas fa-layer-group"></i> Categories</h3>
                    <a href="#" uk-toggle="target: #modal-close-default"><i class="fas fa-plus"></i> Ajouter une categorie</a>

                    <div id="modal-close-default" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <button class="uk-modal-close-default" type="button" uk-close></button>
                            <h2 class="uk-modal-title">Ajouter une categorie</h2>
                            <form action="categories.php" method="post">
                                <div class="inputs-group">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <label for="libelle" class="mb-1">Libellé <span class="required" title="required">*</span></label>
                                            <input class="uk-input" type="text" placeholder="Nom du categorie" id="libelle" name="libelle" required>
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
                            <?php foreach($categories as $category){ ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo $category['libelle']; ?></td>
                                <td style="text-align:center;">
                                    <a href="#" uk-toggle="target: #modal-close-default-edit-<?php echo $category['id']; ?>"><i class="far fa-edit"></i></a>

                                    <div id="modal-close-default-edit-<?php echo $category['id']; ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Modifier une categorie</h2>
                                            <form action="#" method="post">
                                                <div class="inputs-group">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <label for="libelle" class="mb-1">Libellé <span class="required" title="required">*</span></label>
                                                            <input class="uk-input" type="text" placeholder="Nom du categorie" id="libelle" name="libelle" value="<?php echo $category['libelle']; ?>">
                                                            <input type="hidden" name="cat_id" value="<?php echo $category['id']; ?>">
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
                                    <a href="#" uk-toggle="target: #modal-close-default-delete-<?php echo $category['id']; ?>"><i class="far fa-trash-alt"></i></a>

                                    <div id="modal-close-default-delete-<?php echo $category['id']; ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Supprimer une categorie</h2>
                                            <form action="categories.php" method="POST">
                                                <div class="inputs-group">
                                                    <div class="row">
                                                        <div class="col">
                                                            <input type="hidden" name="cat_id" value="<?php echo $category['id']; ?>">
                                                            <p>Êtes-vous sûr Voulez-vous supprimer cette categorie ?</p>
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
    header("location: materiels.php");
} ?>