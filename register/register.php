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
    <!-- NOTE: My files are placed after so they overwrite the files above if needed i.e. my css > their css styles -->
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
                <form method="post">
                    <fieldset>
                        <legend>Register</legend>
                        <p id="register-success"></p>
                        Username: <br>
                        * <input id="register-username" class="register-fields" type="text" name="username"><br>
                        <p id="incorrect-username" class="incorrect-errors"></p>
                        Email: <br>
                        * <input id="register-email" class="register-fields" type="email" name="email"><br>
                        <p id="incorrect-email" class="incorrect-errors"></p>
                        Password: <br>
                        * <input id="register-password" class="register-fields" type='password' name='password'>
                        <p id="incorrect-password" class="incorrect-errors"></p>
                        <button id="register-button" type="button" name="submit" value="Submit" onclick="return validateInput()">Submit</button>
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="col-xs-12>">
            <div class="back">
                <a href="#" id="go-back">Go Back</a>
            </div>
        </div>
        <div class="col-xs-4">
            <p>Email Validation:</p>
            <p>Trim, null, empty</p>
            <p>Filtered through email, removes tags, checks if it exists already in db</p>
        </div>
        <div class="col-xs-4">
            <p>Username Validation:</p>
            <p>Name is > than Max Length, trimmed = 0, equals null or empty</p>
            <p>Needs and only allows letters and spaces, removes tags, checks if exists in db</p>
        </div>
        <div class="col-cs-4"></div>
        <p>Password Validation:</p>
        <p>Trim, null, empty, less than 8 length, make sure has number & lower+uppercse letters</p>
        <p>Pass cannot be same or contain username, removes tags</p>
    </div>
</div>
</body>
</html>