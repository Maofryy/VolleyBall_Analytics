<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>VB - Stats</title>

    <!-- Custom fonts for this template-->
    <link href="../../template/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../template/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Créer un compte!</h1>
                            </div>
                            <form id="form-user" action="<?php echo base_url(); ?>/public/Login/createAccount" method="post" class="user">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" name="firstname"
                                            placeholder="Prénom">
                                    </div>
                                    <div></div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" name="lastname"
                                            placeholder="Nom">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="email" name="email"
                                        placeholder="Adresse email">
                                        <div id="invalid-mail" class="invalid-feedback ml2">
                                       Adresse email invalide
                                        </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user"
                                            id="pseudo" placeholder="Pseudo">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user"
                                            id="licence" placeholder="Numéro de licence">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user" name="password"
                                            id="password" placeholder="Mot de passe">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user" name="confirmpassword"
                                            id="exampleRepeatPassword" placeholder="Répéter le mot de passe">
                                    </div>
                                </div>
                                <button id="connexion" type="submit" class="btn btn-primary btn-user btn-block">
                                    Créer un compte
                                </button>
                                <hr>
                                <a href="javascript:;" class="btn btn-google btn-user btn-block">
                                    <i class="fab fa-google fa-fw"></i> Connexion avec Google
                                </a>
                                <a href="javascript:;" class="btn btn-facebook btn-user btn-block">
                                    <i class="fab fa-facebook-f fa-fw"></i> Connexion avec Facebook
                                </a>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="forgot-password">Mot de passe oublié?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="Connexion">Vous avez déjà un compte? Connexion!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../../template/vendor/jquery/jquery.min.js"></script>

    <script type="text/javascript">
        $( document ).ready(function() {
           $('#connexion').on('click', function()
           {
                // On vérifie l'email
                var email = $('#email').val();
                var re = /\S+@\S+\.\S+/;
                var validEmail =  re.test(email);
               if (validEmail === false)
               {
                    $('#invalid-mail').show();
               }
               return;

               // On vérifie si le pseudo est unique
               // @todo

               // On vérifie le mot de passe celon des critères
               // @todo ou pas

               $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(),
                success: function(data)
                {
                alert(data);
                }
            });
           });
        });    
    </script>
</body>

</html>