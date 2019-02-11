<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Copytube</title>
    <script src="../scripts/jquery-3.3.1.min.js"></script>
    <!-- accesses bootstrap css files that makes css files much easier to use -->
    <link rel="stylesheet" href="../links/bootstrap.min.css" crossorigin="anonymous">
    <!-- accesses bootstrap js files that makes js files much easier to use -->
    <script src="../scripts/bootstrap.min.js" crossorigin="anonymous"></script>
    <!-- NOTE: My files are placed after so they overite the files above if needed i.e. my css > their css styles -->
    <!-- links my style sheet (.css) so it can be used -->
    <link rel="stylesheet" href="register.css"/>
    <script src="register.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <!-- Login fields -->
        <div class="col-xs-12">
            <div class="register">
                <form method="POST" action="register-validate.php">
                    <fieldset>
                        <legend>Register</legend>
                        Username: <br>
                        <input id="register-username" type="text" name="username"><br>
                        <p id="incorrect-username"></p>
                        Email: <br>
                        <input id="register-email" type="email" name="email"><br>
                        <p id="incorrect"></p>
                        Password: <br>
                        <input id="register-password" type='password' name='password'>
                        <p id="incorrect-password"></p>
                        <input id="register-button" type="submit" name="submit" value="Submit">
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="col-xs-12>">
            <div class="back">
                <a href="#" id="go-back">Go Back</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>