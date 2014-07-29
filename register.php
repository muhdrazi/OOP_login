<?php
 require_once 'core/init.php';
 
 //var_dump(Token::check(Input::get('token')));
 
 
 if(Input::exists())
 {
     // how do we get data from the input class. The get method doesn't stand for GET or POST.
     //echo Input::get('username');
     
     if(Token::check(Input::get('token'))) {
         
         //echo 'I have been run <br />';
         
     $validate = new Validate();
     $validation = $validate->check($_POST, array(
         'username'     => array(
             'required' => true,
             'min'      => 2,
             'max'      => 20,
             'unique'   => 'users'
         ),
         'password' => array(
             'required' => true,
             'min'      => 6,
         ),
         'password_again' => array(
             'required' => true,
             'matches'  => 'password'
         ),
         'name'     => array(
             'required' => true,
             'min'      => 2,
             'max'      => 50
         )
     ));
     
     if($validation->passed())
     {
         // register user
         $user = new User();
         
         $salt = Hash::salt(32);

         try {
         
             $user->create(array(
                 'username' => Input::get('username'),
                 'password' => Hash::make(Input::get('password'), $salt),
                 'salt'     => $salt,
                 'name'     => Input::get('name'),
                 'joined'   => date('Y-m-d H:i:s'),
                 'group'    => 1
             ));
             
             Session::flash('home','You have been registered and you can now log in');
             Redirect::to('index.php');
             
         } catch (Exception $e) {
             die($e->getMessage());
         } 
         
         
      }
     else
     {
         // output errors
         foreach($validation->errors() as $error)
         {
             echo $error."<br />";
         }
     }
     }
     
     /*
      * Reqeust side forgery, this else statement will show the user that it is a request side forgery.
     else {
         echo 'request side forgery';
     }
      * 
      */
 }
 ?>

<form action="" method="POST" autocomplete="off">
    <div class="field">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="" autocomplete="off">
    </div>
    <div class="field">
        <label for="password">Choose a Password</label>
        <input type="password" name="password" id="password">
    </div>
    <div class="field">
        <label for="password_again">Repeat Password</label>
        <input type="password" name="password_again" id="password_again">
    </div>
    <div class="field">
        <label for="name">Name</label>
        <input type="text" name="name" id="name">
    </div>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    <input type="submit" value="Register">
</form>