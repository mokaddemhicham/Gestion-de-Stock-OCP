<?php 
session_start();
require_once "database/database.php";
if(isset($_SESSION['user'])){
    header("location: index.php");
} else{


if(isset($_POST['connexion']) && isset($_POST['email']) && isset($_POST['motdepasse'])){
    $email = $_POST['email'];
    $password = $_POST['motdepasse'];

    $db = Database::connect();
    $req = $db->prepare("SELECT users.*, roles.role FROM users INNER JOIN roles ON users.role = roles.id WHERE email = ? AND motdepasse = ?");
    $req->execute([$email,$password]);

    if($req->rowCount() > 0){
        $user = $req->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user']['id'] = $user['id'];
        $_SESSION['user']['nom'] = $user['nom'];
        $_SESSION['user']['prenom'] = $user['prenom'];
        $_SESSION['user']['email'] = $user['email'];
        $_SESSION['user']['role'] = $user['role'];
        header('location: index.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | OCP Group</title>
    <link rel="icon" href="assets/images/logoocp.png"  type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <div class="row text-center mb-5 d-flex justify-content-center">
                <img src="./assets/images/logoocp.png" alt="Logo OCP" style="width: 150px">
            </div>

            <div class="row">
                <div class="form-group col-12">
                    <input type="email" class="form-control" id="email" name="email" required>
                    <label for="email" class="label">E-mail Adress</label>
                </div>
                <div class="form-group col-12">
                    <input type="password" class="form-control" id="password" name="motdepasse" required>
                    <label for="password" class="label">Password</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mt-3" name="connexion">Se Connecter</button>
        </form>
    </div>
    <script type="text/javascript">
        var input = document.querySelectorAll(".form-group");

        input.forEach(element => {
            element.querySelector(".form-control").addEventListener('change', (e)=>{
                if(e.target.value == ''){
                    element.querySelector(".label").classList.remove("focus");
                }else{
                    element.querySelector(".label").classList.add("focus");
                }
            })
        });

    </script>
</body>
</html>
<?php }?>