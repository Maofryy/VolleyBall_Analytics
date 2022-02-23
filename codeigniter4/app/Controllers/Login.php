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
                    'user_id' => $data['user_id'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'pseudo' => $data['pseudo'],
                    'email' => $data['email'],
                    'isLoggedIn' => TRUE
                ];

                $session->set($ses_data);
                return redirect()->to(base_url() . '/public/Home');
            }
            else
            {
                $session->setFlashdata('msg', 'Password is incorrect.');
                return redirect()->to(base_url() . '/public/Login');
            }

        }
        else
        {
            $session->setFlashdata('msg', 'Email does not exist.');var_dump('test1');die;
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
                'first_name'     => $this->request->getVar('firstname'),
                'last_name'     => $this->request->getVar('lastname'),
                'email'    => $this->request->getVar('email'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
            ];

            $userModel->save($data);
            $this->sendValidationMail($this->request->getVar('email'));
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

    private function sendValidationMail($to)
    { 
        $to = $this->request->getVar('mailTo');
        $subject = 'subject';
        $message = 'message';
        
        $email = \Config\Services::email();
        $email->setTo("ferry.v@hotmail.fr");
        $email->setFrom("noreply.vb.stats@gmail.com", 'Confirm Registration');
        
        $email->setSubject($subject);
        $email->setMessage($message);
        if ($email->send()) 
		{
            echo 'Email successfully sent';die;
        } 
		else 
		{
            $data = $email->printDebugger(['headers']);
            print_r($data);die;
        }
    }
}
