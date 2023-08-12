<?php
session_start();

require_once "database/database.php";
if(isset($_SESSION['user'])){
    $db = Database::connect();
    if($_SESSION['user']['role'] === 'Employe'){
        $demandesReq = $db->prepare("SELECT d.id, d.date_demande as date_demande, d.observation as observation, u.id as user_id, u.nom as nom_emp, u.prenom as prenom_emp, serv.libelle as service , ed.etat as etat, ed.id as etat_id, mat.id as mat_id , mat.libelle as mat_libelle, mat.quantite as mat_quantite FROM demandes as d INNER JOIN etat_demandes as ed ON d.etat_demande = ed.id INNER JOIN materiels as mat ON d.materiel = mat.id INNER JOIN users as u ON d.employe = u.id INNER JOIN services as serv ON u.service_emp = serv.id WHERE u.id = ? ORDER BY d.id;");
        $demandesReq->execute([$_SESSION['user']['id']]);
    }else{
        $demandesReq = $db->prepare("SELECT d.id, d.date_demande as date_demande, d.observation as observation, u.id as user_id, u.nom as nom_emp, u.prenom as prenom_emp, serv.libelle as service , ed.etat as etat, ed.id as etat_id, mat.id as mat_id , mat.libelle as mat_libelle, mat.quantite as mat_quantite FROM demandes as d INNER JOIN etat_demandes as ed ON d.etat_demande = ed.id INNER JOIN materiels as mat ON d.materiel = mat.id INNER JOIN users as u ON d.employe = u.id INNER JOIN services as serv ON u.service_emp = serv.id ORDER BY d.id;");
        $demandesReq->execute();
    }
    
    $demandes = $demandesReq->fetchAll(PDO::FETCH_ASSOC);

    // ---------------------- récupére info du services ------------------

    $reqEtats = $db->prepare("SELECT * FROM etat_demandes;");
    $reqEtats->execute();
    $etats = $reqEtats->fetchAll(PDO::FETCH_ASSOC);

        // ------------------------ Etat demande(traiter) Demande ---------------------

        if(isset($_POST['edit']) && isset($_POST['etat_demande']) && isset($_POST['demande_id']) && isset($_POST['product_id']) && isset($_POST['mat_quantite'])){
            $etat_demande = $_POST['etat_demande'];
            $demande_id = $_POST['demande_id'];
            $product_id = $_POST['product_id'];
            $quantity = ((int)$_POST['mat_quantite']);
            
            $update = $db->prepare("UPDATE demandes SET etat_demande = ? WHERE id = ?;");
            $update->execute([$etat_demande ,$demande_id]);

            if((int)$etat_demande === 2){ // 2 ==> Valider
                $quantity--; // Decrimantation par 1
            } 

            $updateMat = $db->prepare("UPDATE materiels SET quantite = ? WHERE id = ?;");
            $updateMat->execute([$quantity, $product_id]);
            header("location: demandes.php");
        }

        // ------------------------ Supprimer Demande ---------------------

        if(isset($_POST['delete']) && isset($_POST['demande_id'])){
            $demande_id = $_POST['demande_id'];
            $delete = $db->prepare("DELETE FROM demandes WHERE id = ?;");
            $delete->execute([$demande_id]);
            header("location: demandes.php");
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
                    <h3><i class="fa fa-users"></i> Demandes</h3>
                </div>
                <div class="uk-overflow-auto card-body">
                    <table class="uk-table uk-table-divider" >
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Employe</th>
                                <th>Service</th>
                                <th>Matériel</th>
                                <th>Observation</th>
                                <th>Date de demande</th>
                                <th>Etat de demande</th>
                                <?php if($_SESSION['user']['role'] == 'Admin' || $_SESSION['user']['role'] == 'Responsable de Stock'){
                                    echo '<th>Actions</th>'; 
                                }?>    
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($demandes as $demande){
                                ?>
                                <tr>
                                    <td><?php echo $demande["id"]; ?></td>
                                    <td><?php echo $demande["nom_emp"] . " " . $demande["prenom_emp"]; ?></td>
                                    <td><?php echo $demande["service"]; ?></td>
                                    <td><?php echo $demande["mat_libelle"]; ?></td>
                                    <td><?php echo $demande["observation"]; ?></td>
                                    <td><?php echo date("d F, Y", strtotime($demande["date_demande"])) ?></td>
                                    <td class="text-center"><?php 
                                        if((int)$demande['etat_id'] === 1){
                                            echo '<span class="uk-label uk-label-warning">'. $demande["etat"] .'</span>';
                                        }else if((int)$demande['etat_id'] === 2){
                                            echo '<span class="uk-label uk-label-success">'. $demande["etat"] .'</span>';
                                        }else{
                                            echo '<span class="uk-label uk-label-danger">'. $demande["etat"] .'</span>';
                                        }
                                    ?></td>
                                    <?php if($_SESSION['user']['role'] == 'Admin' || $_SESSION['user']['role'] == 'Responsable de Stock'){
                                        ?> 

                                    <td>
                                        <?php  if((int)$demande['etat_id'] !== 2){ ?>
                                        <a href="#" uk-toggle="target: #modal-close-default-edit-<?php echo $demande["id"]; ?>"><i class="far fa-edit"></i></a>

                                        <div id="modal-close-default-edit-<?php echo $demande["id"]; ?>" uk-modal>
                                            <div class="uk-modal-dialog uk-modal-body">
                                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                                <h2 class="uk-modal-title">Traiter la demande</h2>
                                                <form action="#" method="post">
                                                    <div class="inputs-group">
                                                        <div class="row mb-2">
                                                            <div class="col">
                                                                <label for="etat_demande" class="uk-form-label mb-1">Etat du demande <span class="required" title="required">*</span></label>
                                                                <div class="uk-form-controls">
                                                                    <select class="uk-select" id="etat_demande" name="etat_demande" >
                                                                        <?php foreach($etats as $etat){ 
                                                                            if($etat['id'] === $demande['etat_id']){
                                                                            ?>
                                                                            <option value="<?php echo $etat['id']; ?>" selected><?php echo $etat['etat']; ?></option>
                                                                        <?php }else{?>
                                                                            <option value="<?php echo $etat['id']; ?>"><?php echo $etat['etat']; ?></option>
                                                                            <?php }} ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="demande_id" value="<?php echo $demande["id"]; ?>">
                                                            <input type="hidden" name="product_id" value="<?php echo $demande["mat_id"]; ?>">
                                                            <input type="hidden" name="mat_quantite" value="<?php echo $demande["mat_quantite"]; ?>">
                                                        </div>
                                                    </div>
                                                    <p class="uk-text-right">
                                                        <button class="btn btn-secondary uk-modal-close" type="button"><i class="fas fa-times"></i> Annuler</button>
                                                        <button class="btn btn-success" type="submit" name="edit"><i class="far fa-edit"></i> Traiter</button>
                                                    </p>
                                                </form>
                                            </div>
                                        </div>

                                        <a href="#" uk-toggle="target: #modal-close-default-delete-<?php echo $demande["id"]; ?>"><i class="far fa-trash-alt"></i></a>

                                        <div id="modal-close-default-delete-<?php echo $demande["id"]; ?>" uk-modal>
                                            <div class="uk-modal-dialog uk-modal-body">
                                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                                <h2 class="uk-modal-title">Supprimer la demande</h2>
                                                <form action="#" method="post">
                                                    <div class="inputs-group">
                                                        <div class="row">
                                                            <div class="col">
                                                                <p>Êtes-vous sûr Voulez-vous supprimer cette demande ?</p>
                                                                <input type="hidden" name="demande_id" value="<?php echo $demande["id"]; ?>">
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
                                            <a href="#" class="uk-disabled" ><i class="far fa-edit" style="color: #ccc"></i></a>

                                        <a href="#" uk-toggle="target: #modal-close-default-delete-<?php echo $demande["id"]; ?>"><i class="far fa-trash-alt"></i></a>

                                        <div id="modal-close-default-delete-<?php echo $demande["id"]; ?>" uk-modal>
                                            <div class="uk-modal-dialog uk-modal-body">
                                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                                <h2 class="uk-modal-title">Supprimer la demande</h2>
                                                <form action="#" method="post">
                                                    <div class="inputs-group">
                                                        <div class="row">
                                                            <div class="col">
                                                                <p>Êtes-vous sûr Voulez-vous supprimer cette demande ?</p>
                                                                <input type="hidden" name="demande_id" value="<?php echo $demande["id"]; ?>">
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
                                        <?php } ?>
                                    </td>
                                    <?php } ?>
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
    header("location: login.php");
}
?>