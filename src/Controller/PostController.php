<?php

namespace App\Controller;

use App\Model\PostManager;
use App\Model\ProfileManager;
use App\Model\CommentManager;

class PostController extends AbstractController
{
    public function showPosts($id)
    {
        $postManager = new PostManager();
        $posts = $postManager->selectUserPosts($id);
        return $this->twig->render('Posts/posts.html.twig', ['posts' => $posts, 'session' => $_SESSION]);
    }

    public function editPost($id)
    {
        $postManager = new PostManager();
        $post = $postManager->selectOneById($id);
        $userId = $_SESSION['id'];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post['title'] = $_POST['title'];
            $post['content'] = $_POST['content'];
            $post['img'] = $_POST['img'];
            $postManager->updatePost($post);
            header('Location:/post/showPosts/' . $userId);
        }
        return $this->twig->render('Posts/editpost.html.twig', ['post' => $post, 'session' => $_SESSION]);
    }

    public function addPost()
    {
        $userId = $_SESSION['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postManager = new PostManager();
            $post = [
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'img' => $_POST['img'],
                'user_id' => $userId,
                'score' => 0
            ];
            $postManager->insert($post);
            header('Location:/post/showPosts/' . $userId);
        }
        return $this->twig->render('Posts/addPost.html.twig', ['session' => $_SESSION]);
    }

    public function deletePost(int $id)
    {
        $userId = $_SESSION['id'];
        $postManager = new PostManager();
        $postManager->delete($id);
        header('Location:/post/showPosts/' . $userId);
    }

    public function showComments($id)
    {
        $postManager = new PostManager();
        $post = $postManager->selectOneById($id);
        $commentManager = new CommentManager();
        $comments = $commentManager->selectComments($id);
        return $this->twig->render('Posts/comments.html.twig', [
            'comments' => $comments,
            'session' => $_SESSION,
            'post' => $post
        ]);
    }

    public function addComment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postManager = new PostManager();
            $post = $postManager->selectOneById($id);
            $date = date('Y/m/d G:i:s');
            $commentManager = new CommentManager();
            $comment = [
                'content' => $_POST['content'],
                'user_id' => $post['user_id'],
                'current_date' => $date,
                'post_id' => $post['id']
            ];
            $commentManager->insertComment($comment);
            header('Location:/post/showComments/' . $post['id']);
        }
        return $this->twig->render('Posts/addComment.html.twig', ['session' => $_SESSION]);
    }

    public function editComment($id)
    {
        $commentManager = new CommentManager();
        $comment = $commentManager->selectOneById($id);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $comment['content'] = $_POST['content'];
            $commentManager->updateComment($comment);
            header('Location:/post/showComments/' . $comment['post_id']);
        }
        return $this->twig->render('Posts/editComment.html.twig', [
            'comment' => $comment,
            'session' => $_SESSION
        ]);
    }

    public function deleteComment(int $id)
    {
        $commentManager = new CommentManager();
        $comment = $commentManager->selectOneById($id);
        $commentManager = new CommentManager();
        $commentManager->deleteComment($id);
        header('Location:/post/showComments/' . $comment['post_id']);
    }

    public function like(int $id)
    {
        $postManager = new PostManager();
        $postManager->like($id);
        header('Location:/Home/index');
    }
}
