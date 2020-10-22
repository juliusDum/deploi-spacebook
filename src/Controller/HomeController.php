<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\ContactManager;
use App\Model\FriendManager;
use App\Model\GalaxyManager;
use App\Model\PlanetManager;
use App\Model\PostManager;
use App\Model\ProfileManager;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class HomeController extends AbstractController
{

    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (empty($_SESSION['login']) || $_SESSION['login'] == false) {
            return $this->twig->render('Home/index.html.twig', [
                'session' => $_SESSION
            ]);
        } else {
            $friendManager = new FriendManager();
            $postManager = new PostManager();
            $friends = $friendManager->selectFriend($_SESSION['id']);
            $post = [];

            foreach ($friends as $friend) {
                $user = $postManager->selectUserPosts(intval($friend['friend_id']));
                array_push($post, $user);
            }
            return $this->twig->render('Home/index.html.twig', [
                'session' => $_SESSION,
                'post' => $post
            ]);
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $profileManager = new ProfileManager();
                $user = [
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                ];
                //FONCTION POUR CHECKER SI LE USER EXISTE
                $result = $profileManager->checkUserProfile($user);
                if (isset($result['id'])) {
                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $result['id'];
                    header('Location:/Profile/index');
                } else {
                    //MESSAGE D'ERREUR SI USER NON EXISTANT
                    $error['wrong_login'] = 'Email or Password incorrect !';
                    return $this->twig->render('Home/login.html.twig', [
                        'error_user' => $error['wrong_login'],
                    ]);
                }
            } else {
                //MESSAGE D'ERREUR SI CHAMPS VIDES
                $error['login_empty'] = 'Email or Password Missing !';
                return $this->twig->render('Home/login.html.twig', [
                    'error_empty' => $error['login_empty'],
                ]);
            }
        }
        return $this->twig->render('Home/login.html.twig');
    }

    public function contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['email']) && !empty($_POST['subject']) && !empty($_POST['message'])) {
                $contactManager = new ContactManager();
                $contact = [
                    'email' => $_POST['email'],
                    'subject' => $_POST['subject'],
                    'message' => $_POST['message'],
                ];
                $_SESSION["emailcontact"] = $_POST["email"];
                $_SESSION["subjectcontact"] = $_POST["subject"];
                $contactManager->insert($contact);

                header('Location:/Home/sendMessage');
            } else {
                // MESSAGES D'ERREURS SI FORMULAIRE VIDE
                $errors = [
                    'form' => '* Fields are missing *'
                ];
                return $this->twig->render('Home/contactform.html.twig', [
                    'errors' => $errors
                ]);
            }
        }
        return $this->twig->render('Home/contactform.html.twig');
    }

    public function sendMessage()
    {
        $send = [
            'email' => $_SESSION['emailcontact'],
            'subject' => $_SESSION['subjectcontact'],
            ];
        // ENVOI DU MAIL
        $mail = new PHPMailer();

        //SMTP Settings
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "paulfromendor@gmail.com";
        $mail->Password = 'odfcaljxymrfyjdc';
        $mail->Port = 465; //587
        $mail->SMTPSecure = "ssl"; //tls

        //Email Settings
        $mail->CharSet = "UTF-8";
        $mail->isHTML(true);
        $mail->setFrom("paulfromendor@gmail.com", "Paul the Admin");
        $mail->addAddress($_SESSION['emailcontact']);
        $mail->addEmbeddedImage('assets/images/working.jpg', "working", "working.jpg");
        $mail->Subject = "Contact Us";
        $mail->Body = "<h1>Your demand is under review</h1><br>
        <img src='cid:working' alt='working'>
        <p>You sent us a message using this email : ". $_SESSION['emailcontact'] ."</p>
        <p>The subject of your message is : ".$_SESSION['subjectcontact'] ."</p>
        <p>A member of our team is looking at it and will contact you soon</p>
        <p><small>The Space Book Team</small></p>";

        if ($mail->send()) {
            $status = "success";
            $response = "Email is sent!";
            //echo $response;
        } else {
            $status = "failed";
            $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
            //echo $response;
        }

        return $this->twig->render('Home/sendmessage.html.twig', [
        'send' => $send,
        ]);
    }

    public function signUp()
    {
        $galaxyManager = new GalaxyManager();
        $galaxys = $galaxyManager->selectUserGalaxy();
        $planetManager = new PlanetManager();
        $planets = $planetManager->selectUserPlanet();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['pseudo'])
                && !empty($_POST['date_of_birth']) && !empty($_POST['planet_id']) && !empty($_POST['password'])
                && !empty($_POST['email'])) {
                $profileManager = new ProfileManager();
                $profile = [
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'pseudo' => $_POST['pseudo'],
                    'date_of_birth' => $_POST['date_of_birth'],
                    'planet_id' => $_POST['planet_id'],
                    'password' => $_POST['password'],
                    'email' => $_POST['email']
                ];
                $profileManager->createUserProfile($profile);

                // PARTI POUR LE MESSAGE DE REMERCIEMENT APRES INSCRIPTION
                $_SESSION['user_email'] = $_POST['email'];
                $_SESSION['user_firstname'] = $_POST['firstname'];
                $_SESSION['user_lastname'] = $_POST['lastname'];
                $_SESSION['user_pseudo'] = $_POST['pseudo'];

                header('Location:/Home/thanks');
            } else {
                $errors = [
                    'form' => '* Fields are missing *'
                ];
                return $this->twig->render('Home/sign_up.html.twig', [
                    'errors' => $errors,
                    'galaxys' => $galaxys,
                    'planets' => $planets
                ]);
            }
        }
        return $this->twig->render('Home/sign_up.html.twig', [
            'galaxys' => $galaxys,
            'planets' => $planets,
            'session' => $_SESSION
        ]);
    }

    public function thanks()
    {
        $thanks = [
            'email' => $_SESSION['user_email'],
            'firstname' => $_SESSION['user_firstname'],
            'lastname' => $_SESSION['user_lastname'],
            'pseudo' => $_SESSION['user_pseudo']
        ];
        // ENVOI DU MAIL
        $mail = new PHPMailer();

        //SMTP Settings
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "paulfromendor@gmail.com";
        $mail->Password = 'odfcaljxymrfyjdc';
        $mail->Port = 465; //587
        $mail->SMTPSecure = "ssl"; //tls

        //Email Settings
        $mail->CharSet = "UTF-8";
        $mail->isHTML(true);
        $mail->setFrom("paulfromendor@gmail.com", "Paul the Admin");
        $mail->addAddress($_SESSION['user_email']);
        $mail->addEmbeddedImage('assets/images/grgroup190627977.jpg', "logo", "grgroup190627977.jpg");
        $mail->Subject = "Welcome to Space Book";
        $mail->Body = "<h1>You created an account !</h1><br>
        <img src='cid:logo' alt='logo'>
        <p>Your pseudo is : ". $_SESSION['user_pseudo'] ."</p>
        <p>Your firstname and lastname are : ". $_SESSION['user_firstname'] ." ". $_SESSION['user_lastname'] ."</p>
        <p>Your mail adress is : ". $_SESSION['user_email'] ."</p>
        <p><small>The Space Book Team</small></p>";

        if ($mail->send()) {
            $status = "success";
            $response = "Email is sent!";
            //echo $response;
        } else {
            $status = "failed";
            $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
            //echo $response;
        }
        return $this->twig->render('Home/thanks.html.twig', [
            'thanks' => $thanks
        ]);
    }

    public function logout()
    {
        unset($_SESSION['login']);
        unset($_SESSION['id']);
        session_destroy();
        header('Location:/Home/index');
    }
}
