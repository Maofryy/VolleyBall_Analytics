<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function register()
    {
        helper(['form']);
        return view('register');
    }

    public function forgotPassword()
    {
        return view('forgot-password');
    }

    public function connexion()
    {
        var_dump('allo');die;
        $session = session();
       
        $userModel = new \App\Models\Users();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        
        $data = $userModel->where('email', $email)->first();
        if ($data)
        {
            $pass = $data['password'];
            $authenticatePassword = password_verify($password, $pass);
            
            if($authenticatePassword)
            {
                $ses_data = [
                    'user_id' => $data['id'],
                    'firstname' => $data['firstname'],
                    'lastname' => $data['lastname'],
                    'pseudo' => $data['pseudo'],
                    'email' => $data['email'],
                    'isLoggedIn' => TRUE
                ];

                $session->set($ses_data);
                var_dump('test3');die;
                return redirect()->to(base_url() . '/public/Home');
            }
            else
            {
                var_dump('test');die;
                $session->setFlashdata('msg', 'Password is incorrect.');
                return redirect()->to(base_url() . '/public/Login');
            }

        }
        else
        {
            var_dump('test2');die;
            $session->setFlashdata('msg', 'Email does not exist.');
            return redirect()->to(base_url() . '/public/Login');
        }
    }

    public function createAccount()
    {
        helper(['form']);
        $rules = [
            'firstname'          => 'required|min_length[2]|max_length[50]',
            'lastname'          => 'required|min_length[2]|max_length[50]',
            'email'         => 'required|min_length[4]|max_length[100]|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[4]|max_length[50]',
            'confirmpassword'  => 'matches[password]'
        ];
          
        if ($this->validate($rules))
        {
            $userModel = new \App\Models\Users();

            $data = [
                'firstname'     => $this->request->getVar('firstname'),
                'lastname'     => $this->request->getVar('lastname'),
                'email'    => $this->request->getVar('email'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
            ];

            $userModel->save($data);

            return redirect()->to(base_url() . '/public/Login');
        } else {
            $data['validation'] = $this->validator;
            return view('login', $data);
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url() . '/public/Login');
    }
}
