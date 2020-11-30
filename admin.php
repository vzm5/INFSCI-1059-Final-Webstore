<?php
    //signin.php
    include 'connection.php';
    include 'headerAdmin.php';
    session_start();

    echo '<h3>Admin Sign In</h3>';

    if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true){
        echo 'You are already signed in, you can sign out if you want:';
        echo '<form method="post" action="">
                <input type="submit" value="Log Out" name="user_log_out"></form>';
        echo '<br>';
        if(isset($_POST['user_log_out'])){
            session_destroy();
            header('Location: index.php');
        }
    }else{ //not signed in
        //login form
        echo '<div class="container">
                <div class="row">
                <form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" class="form-control" value="' . $_POST['username'] . '"/>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="user_password" class="form-control"/>
                </div>
                <div class="form-group">
                    <input type="submit" value="Log In" name="login"/>
                </div>
                </form>
                </div>
            </div>
            ';




        if(isset($_POST['login'])){
            $errors = array(); //array to hold error messages

            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $user_password = mysqli_real_escape_string($conn, $_POST['user_password']);

            if(empty($username)){
                $errors[] = 'Please enter a username';
            }
            if(empty($user_password)){
                $errors[] = 'Please enter a password';
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
            }else{
                $user_password = md5($user_password);
                $query = "SELECT * FROM users WHERE username='$username' AND user_password='$user_password'";
                $result = mysqli_query($conn, $query);

                if(mysqli_num_rows($result)==1){
                    $_SESSION['signed_in'] = true;
                    while($row = mysqli_fetch_assoc($result)){
                        
                        $_SESSION['user_id']   = $row['user_id'];
                        $_SESSION['username']  = $row['username'];
                        $_SESSION['user_level'] = $row['user_level'];
                        
                    }
                    header('location: vieworders.php');//redirect

                }else{
                    echo mysqli_num_rows($results);
                    echo "Wrong username or password";
                }
          

            }


    
        }
    } 

include 'footer.php'
?>

