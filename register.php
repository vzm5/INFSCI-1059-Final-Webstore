<!--Go back in and make this sticky and laid out better maybe errors on the same page-->

<?php
    include 'connection.php';
    include 'header.php';

    echo '<h3>Sign up!</h3>';

    $namePattern = "/^[a-zA-Z\'\s-]+$/";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $first_name = $last_name = $user_password = $username = $password_confirm = $email = "";
        $errors = array(); //array to hold error messages

        //check first name
        if(isset($_POST['first_name'])){
            $first_name = test_input($_POST["first_name"]); //sanitize

            //validate
            if(empty($first_name)){
                $errors[] = "Please enter a name";
            }
            if(!preg_match($namePattern, $first_name)){
                $errors[] = "First name is empty or invalid, please enter your name";
            }
            if(strlen($first_name)>30){
                $errors[] = "First name must be under 30 characters";
            } 
        } 
        //check last name
        if(isset($_POST['last_name'])){
            $last_name = test_input($_POST["last_name"]); //sanitize

            //validate
            if(empty($last_name)){
                $errors[] = "Please enter a last name";
            }
            if(!preg_match($namePattern, $last_name)){
                $errors[] = "Last name is empty or invalid, please enter your name";
            }
            if(strlen($last_name)>30){
                $errors[] = "Last name must be under 30 characters";
            } 
        }

        //check email: is email
        if(isset($_POST['email'])){
            $email = test_input($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }
        }
        
        
        //check username: letter and digits only, under 30 chars, unique
        if(isset($_POST['username'])){
            $username = test_input($_POST['username']);//sanitize

            if(!ctype_alnum($_POST['username'])){
                $errors[] = 'The username can only contain letters and digits.';
            }
            if(strlen($_POST['username']) > 30){
                $errors[] = 'The username cannot be longer than 30 characters.';
            }

            //test for uniqueness
            $username = mysqli_real_escape_string($conn, $username);
            $user_check_sql = "SELECT * FROM final_customer WHERE user_name='$username' LIMIT 1";
            $result = mysqli_query($conn, $user_check_sql);
            $user = mysqli_fetch_assoc($result);

            if($user){//if user already exists
                if($user['username'] === $username){
                    $errors[] = 'This user already exists';
                }
            }
        }

        //check password: both must match
        $user_password = $_POST['user_password'];

        if(isset($_POST['user_password']) && isset($_POST['password_confirm'])){
            if(empty($_POST['user_password'])){
                $errors[] = 'Please enter and confirm password';
            }
            if($_POST['user_password'] != $_POST['password_confirm']){
                $errors[] = 'The two passwords must match';
            }

        }

        //handle user errors
        if(!empty($errors)){
            echo 'There are errors in your sign up form..';
            echo '<ul>';
            foreach($errors as $key => $value) 
            {
                echo '<li>' . $value . '</li>'; 
            }
            echo '</ul>';
        }else{ //no errors, handle form
            //sanitize and put into db
            $first_name = mysqli_real_escape_string($conn, $first_name);
            $last_name = mysqli_real_escape_string($conn, $last_name);
            $user_password = md5($user_password);
            $email = mysqli_real_escape_string($conn, $email);

            $sql = "INSERT INTO final_customer(first_name, last_name, user_name, user_password, user_email) VALUES ('$first_name', '$last_name', '$username', '$user_password', '$email')";

            if ($conn->query($sql) === TRUE) {
                echo "Thank you for registering, you can now <a href=signin.php>sign in </a>";

            } else {
                echo "Something went wrong";
                echo "Error: " . $sql . "<br>" . $conn->error;
                            echo $user_password;

            }
        }
        
    }

    //Function to clean user input
    function test_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="<?php echo $first_name;?>"/>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="<?php echo $last_name;?>"/>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $email;?>"/>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $username;?>"/>
                    </div>
                    <div class="form-group">
                        <label for="user_password">Password</label>
                        <input type="password" class="form-control" name="user_password"/>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirm"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Sign Up"/>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
    include 'footer.php';
?>