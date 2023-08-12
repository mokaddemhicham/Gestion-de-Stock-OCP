<?php
session_start();
require_once "database/database.php";
if(isset($_SESSION['user'])){
    $message = ""; // init
    $db = Database::connect();
    $req = $db->prepare("SELECT materiels.*, categories.libelle as cat_libelle FROM materiels INNER JOIN categories ON materiels.categorie = categories.id ORDER BY materiels.id;");
    $req->execute();
    $products  = $req->fetchAll(PDO::FETCH_ASSOC);

    $reqCat = $db->prepare("SELECT * FROM categories;");
    $reqCat->execute();
    $categories = $reqCat->fetchAll(PDO::FETCH_ASSOC);

        // ----------------------- Insertion  ---------------------

        if(isset($_POST['add']) && isset($_POST['nom']) && isset($_POST['categorie']) && isset($_POST['quantity']) && isset($_POST['model']) && isset($_POST['ns'])){
            $productName = $_POST['nom'];
            $model = $_POST['model'];
            $quantity = $_POST['quantity'];
            $category = $_POST['categorie'];
            $ns = $_POST['ns'];
            $insert = $db->prepare("INSERT INTO materiels(libelle,numero_serie,quantite,model,categorie) VALUES(?,?,?,?,?);");
            $insert->execute([$productName,$ns,$quantity,$model,$category]);
            header("location: materiels.php");
        }
        // ------------------------ modifer categorie ---------------------

        if(isset($_POST['edit']) && isset($_POST['nom']) && isset($_POST['categorie']) && isset($_POST['quantity']) && isset($_POST['model']) && isset($_POST['ns']) && isset($_POST['product_id'])){
            $productName = $_POST['nom'];
            $model = $_POST['model'];
            $quantity = $_POST['quantity'];
            $category = $_POST['categorie'];
            $ns = $_POST['ns'];
            $product_id = $_POST['product_id'];

            $update = $db->prepare("UPDATE materiels SET libelle = ? , numero_serie = ?, quantite = ?, model = ?, categorie = ? WHERE id = ?;");
            $update->execute([$productName,$ns,$quantity,$model,$category,$product_id]);
            header("location: materiels.php");
        }

        // ------------------------ Supprimer categorie ---------------------

        if(isset($_POST['delete']) && isset($_POST['product_id'])){
            $product_id = $_POST['product_id'];
            $delete = $db->prepare("DELETE FROM materiels WHERE id = ?;");
            $delete->execute([$product_id]);
            header("location: materiels.php");
        }

        // ------------------------ Demander un materiel ---------------------

        if(isset($_POST['demander']) && isset($_POST['observation']) && isset($_POST['product_id'])){
            $observation = $_POST['observation'];
            $product_id = $_POST['product_id'];
            $emp = $_SESSION['user']['id'];
            foreach($products as $product){
                if($product['id'] === $_POST['product_id']){
                    if(((int)$product['quantite']) != 0){
                        $reqAddDemande = $db->prepare("INSERT INTO demandes (etat_demande,date_demande, observation,materiel, employe) VALUES (?,?,?,?,?)");
                        $reqAddDemande->execute([1,date("Y-m-d"),$observation,$product_id,$emp]);
                        header("location: demandes.php");
                    }else{
                        $message = "ce materiel n'est pas en stock actuellement!";
                    }
                }
            }
        }

?>
<!DOCTYPE html>
<html lang="en">
<?php require_once "master/head.php"; ?>
<body>
<?php require_once "master/sidebar.php" ?>


        <div class="main-users">
            <?php if($message){ ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>
            <div class="users card">
                <div class="table-header card-header">
                    <h3><i class="fa fa-boxes"></i> Equipements</h3>
                    <?php if($_SESSION['user']['role'] == 'Admin' || $_SESSION['user']['role'] == 'Responsable de Stock'){ ?>
                    <a href="#" uk-toggle="target: #modal-close-default-add"><i class="fas fa-plus"></i> Ajouter un Matériel</a>

                    <div id="modal-close-default-add" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <button class="uk-modal-close-default" type="button" uk-close></button>
                            <h2 class="uk-modal-title">Ajouter un Matériel</h2>
                            <form action="#" method="POST">
                                <div class="inputs-group">
                                    <div class="row mb-2">
                                        <div class="col">
                                            <label for="nom" class="mb-1">Nom de Matériel <span class="required" title="required">*</span></label>
                                            <input class="uk-input " type="text" placeholder="Nom de Matériel" id="nom" name="nom" required>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label for="ns" class="mb-1">Numéro de Serie <span class="required" title="required">*</span></label>
                                            <input class="uk-input" type="text"  placeholder="Numéro de Serie" id="ns" name="ns" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="quantite" class="mb-1">Quantité <span class="required" title="required">*</span></label>
                                            <input class="uk-input " type="number" placeholder="Quantité" id="quantite" name="quantity" required>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6 uk-form-controls">
                                        <label for="categorie" class="mb-1">Categorie <span class="required" title="required">*</span></label>
                                                <select class="uk-select" id="categorie" name="categorie">
                                                    <option value="">Selectionner une categorie</option>
                                                <?php foreach($categories as $categorie){ ?>
                                                    <option value="<?php echo $categorie['id']; ?>"><?php echo $categorie['libelle']; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        <div class="col-md-6">
                                            <label for="model" class="mb-1">Model <span class="required" title="required">*</span></label>
                                            <input class="uk-input" type="text" placeholder="Model" id="model" name="model" required>
                                        </div>
                                    </div>
                                </div>
                                <p class="uk-text-right">
                                    <button class="btn btn-secondary uk-modal-close" type="button"><i class="fas fa-times"></i> Annuler</button>
                                    <button class="btn btn-primary" type="submit" name="add"><i class="fas fa-plus"></i> Ajouter</button>
                                </p>
                            </form>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="uk-overflow-auto card-body">
                    <table class="uk-table uk-table-divider" >
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom de Matériel</th>
                                <th>Numéro série</th>
                                <th>Quantité</th>
                                <th>Model</th>
                                <th>Categorie</th>
                                <th>Actions</th>    
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($products as $product){
                            ?>
                            <tr>
                                    <td><?php echo $product["id"]; ?></td>
                                    <td><?php echo $product["libelle"]; ?></td>
                                    <td><?php echo $product["numero_serie"]; ?></td>
                                    <td><?php echo $product["quantite"]; ?></td>
                                    <td><?php echo $product["model"]; ?></td>
                                    <td><?php echo $product["cat_libelle"]; ?></td>
                                    <td>
                                        <?php if($_SESSION['user']['role'] == 'Admin' || $_SESSION['user']['role'] == 'Responsable de Stock'){?>

                                        <a href="#" uk-toggle="target: #modal-close-default-view-<?php echo $product["id"]; ?>"><i class="far fa-eye"></i></a>

                                        <div id="modal-close-default-view-<?php echo $product["id"]; ?>" uk-modal>
                                            <div class="uk-modal-dialog uk-modal-body">
                                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                                <h2 class="uk-modal-title">Information</h2>
                                                <form>
                                                    <div class="inputs-group">
                                                        <div class="row mb-2">
                                                            <div class="col">
                                                                <label for="nom" class="mb-1">Nom du matériel</label>
                                                                <input class="uk-input " type="text" placeholder="Nom de Matériel" id="nom" name="nom" disabled value="<?php echo $product["libelle"]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <label for="ns" class="mb-1">Numéro de Série</label>
                                                                <input class="uk-input" type="text" placeholder="Numéro de Série" id="ns" name="ns" disabled value="<?php echo $product["numero_serie"]; ?>">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="quantite" class="mb-1">Quantité</label>
                                                                <input class="uk-input " type="number" placeholder="Quantité" id="quantite" name="quantity" disabled value="<?php echo $product["quantite"]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <label for="categorie" class="mb-1">Categorie</label>
                                                                <input class="uk-input" type="text" placeholder="Categorie" id="categorie" value="<?php echo $product['cat_libelle']; ?>" disabled>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="model" class="mb-1">Model</label>
                                                                <input class="uk-input" type="text" placeholder="Model" id="model" name="model" disabled value="<?php echo $product["model"]; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <a href="#" uk-toggle="target: #modal-close-default-edit-<?php echo $product["id"]; ?>"><i class="far fa-edit"></i></a>

                                        <div id="modal-close-default-edit-<?php echo $product["id"]; ?>" uk-modal>
                                            <div class="uk-modal-dialog uk-modal-body">
                                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                                <h2 class="uk-modal-title">Modifier un Matériel</h2>
                                                <form action="#" method="post">
                                                    <div class="inputs-group">
                                                            <div class="row mb-2">
                                                                <div class="col">
                                                                    <label for="nom" class="mb-1">Nom du  Matériel <span class="required" title="required">*</span></label>
                                                                    <input class="uk-input " type="text" placeholder="Nom du Matériel" id="nom" name="nom" value="<?php echo $product["libelle"]; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label for="ns" class="mb-1">Numéro de Série <span class="required" title="required">*</span></label>
                                                                    <input class="uk-input" type="text" placeholder="Numéro de Série" id="ns" name="ns" value="<?php echo $product["numero_serie"]; ?>">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="quantite" class="mb-1">Quantité <span class="required" title="required">*</span></label>
                                                                    <input class="uk-input " type="number" placeholder="Quantité" id="quantite" name="quantity" value="<?php echo $product["quantite"]; ?>">
                                                                </div>
                                                             </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <label for="categorie" class="mb-1">Categorie <span class="required" title="required">*</span></label>
                                                                    <div class="uk-form-controls">
                                                                    <select class="uk-select" id="role" name="categorie" >
                                                                        <?php foreach($categories as $categorie){ 
                                                                            if($categorie['id'] === $product['categorie']){
                                                                            ?>
                                                                            <option value="<?php echo $categorie['id']; ?>" selected><?php echo $categorie['libelle']; ?></option>
                                                                        <?php }else{?>
                                                                            <option value="<?php echo $categorie['id']; ?>"><?php echo $categorie['libelle']; ?></option>
                                                                            <?php }} ?>
                                                                    </select>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="model" class="mb-1">Model <span class="required" title="required">*</span></label>
                                                                    <input class="uk-input" type="text" placeholder="Model" id="model" name="model" value="<?php echo $product["model"]; ?>">
                                                                </div>
                                                                <input type="hidden" name="product_id" value="<?php echo $product["id"]; ?>">
                                                            </div>
                                                        </div>
                                                    <p class="uk-text-right">
                                                        <button class="btn btn-secondary uk-modal-close" type="button"><i class="fas fa-times"></i> Annuler</button>
                                                        <button class="btn btn-success" type="submit" name="edit"><i class="far fa-edit"></i> Modifier</button>
                                                    </p>
                                                </form>
                                            </div>
                                        </div>
                                        <a href="#" uk-toggle="target: #modal-close-default-delete-<?php echo $product["id"]; ?>"><i class="far fa-trash-alt"></i></a>

                                        <div id="modal-close-default-delete-<?php echo $product["id"]; ?>" uk-modal>
                                            <div class="uk-modal-dialog uk-modal-body">
                                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                                <h2 class="uk-modal-title">Supprimer un Produit</h2>
                                                <form action="#" method="post">
                                                    <div class="inputs-group">
                                                        <div class="row">
                                                            <div class="col">
                                                                <p>Êtes-vous sûr Voulez-vous supprimer ce Produit ?</p>
                                                                <input type="hidden" name="product_id" value="<?php echo $product["id"]; ?>">
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
                                        <?php }else{ ?>
                                            <a href="#" uk-toggle="target: #modal-close-default-demande-<?php echo $product['id']; ?>"><i class="far fa-edit"></i></a>

                                            <div id="modal-close-default-demande-<?php echo $product['id']; ?>" uk-modal>
                                                <div class="uk-modal-dialog uk-modal-body">
                                                    <button class="uk-modal-close-default" type="button" uk-close></button>
                                                    <h2 class="uk-modal-title">Demander <?php echo $product['libelle']; ?></h2>
                                                    <form action="#" method="post">
                                                        <div class="inputs-group">
                                                            <div class="row mb-2">
                                                                <div class="col-12">
                                                                    <label for="observation" class="mb-1">Observation <span class="required" title="required">*</span></label>
                                                                    <textarea name="observation" class="uk-input" style="min-height: 100px" id="observation" cols="30" rows="10" placeholder="Pourquoi vous voulez ce matériel ?" required></textarea>
                                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="uk-text-right">
                                                            <button class="btn btn-secondary uk-modal-close" type="button"><i class="fas fa-times"></i> Annuler</button>
                                                            <button class="btn btn-success" type="submit" name="demander"><i class="fas fa-paper-plane"></i> Demander</button>
                                                        </p>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php } ?>
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
<?php 
    }else{
        header('location: login.php');
    }
 ?>