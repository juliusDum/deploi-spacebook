<?php


namespace App\Controller;

use App\Model\CommentManager;
use App\Model\ContactManager;
use App\Model\GalaxyManager;
use App\Model\PlanetManager;
use App\Model\PostManager;
use App\Model\ProfileManager;

class AdminController extends AbstractController
{

    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                if ($_POST['username'] == 'PaulTheAdmin' && $_POST['password'] == 'p86UFz2tL') {
                    $_SESSION['admin'] = true;
                    header('Location: /Admin/index');
                }
            }
        }
        return $this->twig->render('Admin/login.html.twig');
    }

    public function index()
    {
        if (empty($_SESSION['admin']) || $_SESSION['admin'] == false) {
            Header('Location:/Home/index');
        } else {
            return $this->twig->render('Admin/index.html.twig');
        }
    }

    public function posts()
    {
        if (empty($_SESSION['admin']) || $_SESSION['admin'] == false) {
            Header('Location:/Home/index');
        } else {
            $postManager = new PostManager();
            $posts = $postManager->selectAll();
            return $this->twig->render('Admin/posts.html.twig', ['posts' => $posts]);
        }
    }

    public function users()
    {
        if (empty($_SESSION['admin']) || $_SESSION['admin'] == false) {
            Header('Location:/Home/index');
        } else {
            $profileManager = new ProfileManager();
            $profiles = $profileManager->selectAll();
            return $this->twig->render('Admin/users.html.twig', ['profiles' => $profiles]);
        }
    }

    public function support()
    {
        if (empty($_SESSION['admin']) || $_SESSION['admin'] == false) {
            Header('Location:/Home/index');
        } else {
            $contactManager = new ContactManager();
            $messages = $contactManager->selectAll();
            return $this->twig->render('Admin/support.html.twig', ['messages' => $messages]);
        }
    }

    public function comments($id)
    {
        if (empty($_SESSION['admin']) || $_SESSION['admin'] == false) {
            Header('Location:/Home/index');
        } else {
            $commentManager = new CommentManager();
            $comments = $commentManager->selectComments($id);
            return $this->twig->render('Admin/comments.html.twig', ['comments' => $comments]);
        }
    }

    public function userProfile($id)
    {
        if (empty($_SESSION['admin']) || $_SESSION['admin'] == false) {
            Header('Location:/Home/index');
        } else {
            $profileManager = new ProfileManager();
            $profile = $profileManager->selectUserProfile($id);

            $postManager = new PostManager();
            $posts = $postManager->selectUserPosts($id);
            return $this->twig->render('Admin/user_profile.html.twig', [
                'profile' => $profile,
                'posts' => $posts,
            ]);
        }
    }

    public function deleteMessage($id)
    {
        $contactManager = new ContactManager();
        $contactManager->deleteMessageById($id);
        header("Location: /Admin/support");
    }

    public function deletePost($id)
    {
        $postManager = new PostManager();
        $postManager->delete($id);
        header("Location: /Admin/posts");
    }

    public function deleteUser($id)
    {
        $profileManager = new ProfileManager();
        $profileManager->deleteUserProfile($id);
        header("Location: /Admin/users");
    }

    public function deleteComment($id)
    {
        $commentManager = new CommentManager();
        $commentManager->delete($id);
        header("Location: /Admin/posts");
    }

    public function logout()
    {
        unset($_SESSION['admin']);
        session_destroy();
        header('Location:/Home/index');
    }
}
