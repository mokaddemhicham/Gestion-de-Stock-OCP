<?php
session_start();

require_once "database/database.php";
if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin'){

    $db = Database::connect();
    $usersReq = $db->prepare("SELECT users.*, roles.role, services.id as serv_id, services.libelle as service FROM roles INNER JOIN users ON users.role = roles.id INNER JOIN services ON users.service_emp = services.id ORDER BY users.id;");
    $usersReq->execute();
    $users = $usersReq->fetchAll(PDO::FETCH_ASSOC);
    // ---------------------------- récupérer les Roles ------------------------------------

    $rolesReq = $db->prepare("SELECT * FROM roles");
    $rolesReq->execute();
    $roles = $rolesReq->fetchAll(PDO::FETCH_ASSOC);

     // ---------------------------- récupérer les Services ------------------------------------

     $servicesReq = $db->prepare("SELECT * FROM services");
     $servicesReq->execute();
     $services = $servicesReq->fetchAll(PDO::FETCH_ASSOC);

        // ----------------------- ajouter user---------------------

        if(isset($_POST['add']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['motdepasse']) && isset($_POST['role']) && isset($_POST['service'])){
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $motdepasse = $_POST['motdepasse'];
            $role = $_POST['role'];
            $service = $_POST['service'];
            $insert = $db->prepare("INSERT INTO users(nom,prenom,email,motdepasse,role, service_emp) VALUES(?,?,?,?,?,?);");
            $insert->execute([$nom,$prenom,$email,$motdepasse,$role,$service]);
            header("location: utilisateurs.php");
        }
        // ------------------------ modifier user ---------------------

        if(isset($_POST['edit']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['motdepasse']) && isset($_POST['role']) && isset($_POST['service']) && isset($_POST['user_id'])){
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $motdepasse = $_POST['motdepasse'];
            $role = $_POST['role'];
            $user_id = $_POST['user_id'];
            $service = $_POST['service'];
            $update = $db->prepare("UPDATE users SET nom = ? , prenom = ?, email = ?, motdepasse = ?, role = ?, service_emp = ? WHERE id = ?;");
            $update->execute([$nom,$prenom,$email,$motdepasse, $role ,$service,$user_id]);
            header("location: utilisateurs.php");
        }

        // ------------------------ Supprimer user ---------------------

        if(isset($_POST['delete']) && isset($_POST['user_id'])){
            $user_id = $_POST['user_id'];
            $delete = $db->prepare("DELETE FROM users WHERE id = ?;");
            $delete->execute([$user_id]);
            header("location: utilisateurs.php");
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
                    <h3><i class="fa fa-users"></i> Utilisateurs</h3>
                    <a href="#" uk-toggle="target: #modal-close-default-add"><i class="fas fa-user-plus"></i> Ajouter un Utilisateur</a>

                    <div id="modal-close-default-add" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <button class="uk-modal-close-default" type="button" uk-close></button>
                            <h2 class="uk-modal-title">Ajouter un Utilisateur</h2>
                            <form action="#" method="POST">
                                <div class="inputs-group">
                                    
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label for="nom" class="mb-1">Nom <span class="required" title="required">*</span></label>
                                            <input class="uk-input " type="text" placeholder="Nom" id="nom" name="nom" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="prenom" class="mb-1">Prenom <span class="required" title="required">*</span></label>
                                            <input class="uk-input " type="text" placeholder="Prenom" id="prenom" name="prenom" required>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label for="email" class="mb-1">E-mail <span class="required" title="required">*</span></label>
                                            <input class="uk-input " type="email" placeholder="E-mail" id="email" name="email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="motdepasse" class="mb-1">Mot de Passe <span class="required" title="required">*</span></label>
                                            <input class="uk-input " type="password" placeholder="Mot de Passe" id="motdepasse" name="motdepasse" required>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label for="role" class="uk-form-label mb-1">Role <span class="required" title="required">*</span></label>
                                            <div class="uk-form-controls">
                                                <select class="uk-select" id="role" name="role">
                                                    <option value="">Selectionner un role</option>
                                                <?php foreach($roles as $role){ ?>
                                                    <option value="<?php echo $role['id']; ?>"><?php echo $role['role']; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="service" class="uk-form-label mb-1">Service <span class="required" title="required">*</span></label>
                                            <div class="uk-form-controls">
                                                <select class="uk-select" id="service" name="service">
                                                    <option value="">Selectionner un service</option>
                                                <?php foreach($services as $service){ ?>
                                                    <option value="<?php echo $service['id']; ?>"><?php echo $service['libelle']; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
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
                </div>
                <div class="uk-overflow-auto card-body">
                    <table class="uk-table uk-table-divider" >
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Nom</th>
                                <th>Prenom</th>
                                <th>E-mail</th>
                                <th>Mot de Passe</th>
                                <th>Role</th>
                                <th>Service</th>    
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($users as $user){
                                ?>
                                <tr>
                                    <td><?php echo $user["id"]; ?></td>
                                    <td><?php echo $user["nom"]; ?></td>
                                    <td><?php echo $user["prenom"]; ?></td>
                                    <td><?php echo $user["email"]; ?></td>
                                    <td><?php echo $user["motdepasse"]; ?></td>
                                    <td><?php echo $user["role"]; ?></td>
                                    <td><?php echo $user["service"]; ?></td>
                                    <td>
                                        <a href="#" uk-toggle="target: #modal-close-default-view-<?php echo $user["id"]; ?>"><i class="fas fa-user-alt"></i></a>

                                        <div id="modal-close-default-view-<?php echo $user["id"]; ?>" uk-modal>
                                            <div class="uk-modal-dialog uk-modal-body">
                                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                                <h2 class="uk-modal-title">Information</h2>
                                                <form>
                                                <div class="inputs-group">
                                                    <div class="row mb-2">
                                                        <div class="col-md-6">
                                                            <label for="nom" class="mb-1">Nom <span class="required" title="required">*</span></label>
                                                            <input class="uk-input " type="text" placeholder="Nom" id="nom" name="nom" disabled value="<?php echo $user['nom'] ?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="prenom" class="mb-1">Prenom <span class="required" title="required">*</span></label>
                                                            <input class="uk-input " type="text" placeholder="Prenom" id="prenom" name="prenom"  disabled value="<?php echo $user['prenom'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-6">
                                                            <label for="email" class="mb-1">E-mail <span class="required" title="required">*</span></label>
                                                            <input class="uk-input " type="email" placeholder="E-mail" id="email" name="email"  disabled value="<?php echo $user['email'] ?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="motdepasse" class="mb-1">Mot de Passe <span class="required" title="required">*</span></label>
                                                            <input class="uk-input " type="password" placeholder="Mot de Passe" id="motdepasse" name="motdepasse"  disabled value="<?php echo $user['motdepasse'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-6">
                                                            <label for="role" class="uk-form-label mb-1">Role <span class="required" title="required">*</span></label>
                                                            <div class="uk-form-controls">
                                                                <select class="uk-select" id="role" disabled>
                                                                    <option value="<?php echo $user['role'] ?>"><?php echo $user['role'] ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="service" class="uk-form-label mb-1">Service <span class="required" title="required">*</span></label>
                                                            <div class="uk-form-controls">
                                                                <select class="uk-select" id="service" disabled>
                                                                    <option value="<?php echo $user['serv_id'] ?>"><?php echo $user['service'] ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                        <a href="#" uk-toggle="target: #modal-close-default-edit-<?php echo $user["id"]; ?>"><i class="fas fa-user-edit"></i></a>

                                        <div id="modal-close-default-edit-<?php echo $user["id"]; ?>" uk-modal>
                                            <div class="uk-modal-dialog uk-modal-body">
                                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                                <h2 class="uk-modal-title">Modifier un Utilisateur</h2>
                                                <form action="#" method="post">
                                                    <div class="inputs-group">
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <label for="nom" class="mb-1">Nom <span class="required" title="required">*</span></label>
                                                                <input class="uk-input " type="text" placeholder="Nom" id="nom" name="nom" value="<?php echo $user['nom'] ?>">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="prenom" class="mb-1">Prenom <span class="required" title="required">*</span></label>
                                                                <input class="uk-input " type="text" placeholder="Prenom" id="prenom" name="prenom"  value="<?php echo $user['prenom'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <label for="email" class="mb-1">E-mail <span class="required" title="required">*</span></label>
                                                                <input class="uk-input " type="email" placeholder="E-mail" id="email" name="email"   value="<?php echo $user['email'] ?>">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="motdepasse" class="mb-1">Mot de Passe <span class="required" title="required">*</span></label>
                                                                <input class="uk-input " type="password" placeholder="Mot de Passe" id="motdepasse" name="motdepasse"   value="<?php echo $user['motdepasse'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <label for="role" class="uk-form-label mb-1">Role <span class="required" title="required">*</span></label>
                                                                <div class="uk-form-controls">
                                                                    <select class="uk-select" id="role" name="role" >
                                                                        <?php foreach($roles as $role){ 
                                                                            if($role['role'] === $user['role']){
                                                                            ?>
                                                                            <option value="<?php echo $role['id']; ?>" selected><?php echo $role['role']; ?></option>
                                                                        <?php }else{?>
                                                                            <option value="<?php echo $role['id']; ?>"><?php echo $role['role']; ?></option>
                                                                            <?php }} ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="service" class="uk-form-label mb-1">Service <span class="required" title="required">*</span></label>
                                                                <div class="uk-form-controls">
                                                                    <select class="uk-select" id="service" name="service" >
                                                                        <?php foreach($services as $service){ 
                                                                            if($service['id'] === $user['serv_id']){
                                                                            ?>
                                                                            <option value="<?php echo $service['id']; ?>" selected><?php echo $service['libelle']; ?></option>
                                                                        <?php }else{?>
                                                                            <option value="<?php echo $service['id']; ?>"><?php echo $service['libelle']; ?></option>
                                                                            <?php }} ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
                                                        </div>
                                                    </div>
                                                    <p class="uk-text-right">
                                                        <button class="btn btn-secondary uk-modal-close" type="button"><i class="fas fa-times"></i> Annuler</button>
                                                        <button class="btn btn-success" type="submit" name="edit"><i class="far fa-edit"></i> Modifier</button>
                                                    </p>
                                                </form>
                                            </div>
                                        </div>
                                        <a href="#" uk-toggle="target: #modal-close-default-delete-<?php echo $user["id"]; ?>"><i class="fas fa-user-minus"></i></a>

                                        <div id="modal-close-default-delete-<?php echo $user["id"]; ?>" uk-modal>
                                            <div class="uk-modal-dialog uk-modal-body">
                                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                                <h2 class="uk-modal-title">Supprimer un Utilisateur</h2>
                                                <form action="#" method="post">
                                                    <div class="inputs-group">
                                                        <div class="row">
                                                            <div class="col">
                                                                <p>Êtes-vous sûr Voulez-vous supprimer ce Utilisateur ?</p>
                                                                <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
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
<?php 
}else{
    header("location: index.php");
}
?>