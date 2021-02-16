<?php

declare(strict_types=1);

namespace app\controllers;

use app\classes\Flash;
use app\models\User;

class Login extends Base
{
    public function index($request, $response)
    {

        $flashes = Flash::getAll();
        $nameView = $this->nameView(__CLASS__, __FUNCTION__);
        return $this->getTwig()->render(
            $response,
            $this->setView($nameView),
            [
                'sistema' => 'teste',
                'title' => 'Login',
                'flashes' => $flashes,
                'link_acesso' => url("access"),
                'link_forgot' => url("recuperar")
            ]
        );
    }

    public function forgot($request, $response)
    {

        $nameView = $this->nameView(__CLASS__, __FUNCTION__);
        return $this->getTwig()->render(
            $response,
            $this->setView($nameView),
            [
                'sistema' => 'teste',
                'title' => 'Recuperar senha',
                'link_recovery' => url("renovar"),
                'link_login' => url("login"),
            ]
        );
    }

    public function access($request, $response)
    {
        //true = campos preenchidos
        //false = campo obrigatório vazio
        $error = required(['email', 'senha']);

        if ($error) {
            $message = $this->flashMessage('Campo obrigatório não informado', 'danger');
            Flash::set('backend', $message);
            return redirect($response, 'login');
        }

        //FIXME
        // Obter post de forma segura
        //Validando formato de email
        if (!is_email($_POST['email'])) {
            $message = $this->flashMessage('Email informado inválido', 'danger');
            Flash::set('backend', $message);
            return redirect($response, 'login');
        }

        $user = new User();

        $result = $user->emailExist($_POST['email']);
        $verifiedPass = password_verify($_POST['senha'], (empty($result) ? '' : $result->pass));

        var_dump($result, $verifiedPass, $_POST['senha']);
        if (!$result || !$verifiedPass) {

            $message = $this->flashMessage('Login inválido', 'danger');
            Flash::set('backend', $message);
            return redirect($response, 'login');
        } else {
            // Login efetuado
            return redirect($response, 'admin');
        }
    }
}