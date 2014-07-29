<?php
require_once 'core/init.php';

//DB::getInstance()->delete('users', array('id', '=', 1));


if(Session::exists('home'))
{
    echo '<p>' . Session::flash('home') . '</p>';
}

//echo Session::get(Config::get('session/session_name'));

$user = new User();
if($user->isLoggedIn())
{
?>
    <p>Hello <a href="#"><?php echo escape($user->data()->username) ?></a> !</p>

    <ul>
        <li><a href="logout.php">Log Out</a></li>
        <li><a href="update.php">Update Details</a></li>
        
    </ul>
<?php
} else {
    echo '<p>You need to <a href="login.php">login</a> or <a href="register.php">register</a>.</p>';
}

?>