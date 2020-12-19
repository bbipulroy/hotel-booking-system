<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    public function index() {
        echo json_encode([ 'status' => false ]);
    }

    public function add() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('user_name', 'User Name', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if($this->form_validation->run() === true) {
			$form_data = [
				'user_name' => $this->input->post('user_name'),
				'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
				'status' => 1,
            ];

			$status = $this->db->insert('users', $form_data);
			if($status) {
				echo json_encode([
					'status' => true,
					'insert_id' => $this->db->insert_id(),
				]);
			}
			else {
				echo json_encode([ 'status' => false ]);
			}
        }
        else {
        	$errors = $this->form_validation->error_array();
            echo json_encode([
            	'status' => false,
            	'errors' => $errors,
            ]);
        }
    }
    
    public function login() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('user_name', 'User Name', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if($this->form_validation->run() === true) {
			$user_name = $this->input->post('user_name');
            $password = $this->input->post('password');
            
            $this->db->select('id, password')
                ->from('users')
                ->where('user_name', $user_name)
                ->where('status', 1);
            $result = $this->db->get()->row_array();

            if(!$result) {
                exit(json_encode([ 'status' => false ]));
            }

            if(password_verify($password, $result['password'])) {
                $time = time();

                $data = [
                    'user_id' => $result['id'],
                    'device_id' => uniqid(),
                    'security_token' => $this->generate_random_string(),
                    'created_time' => $time,
                    'last_used_time' => $time,
                ];
                $this->db->insert('user_auth', $data);
                echo json_encode([
                    'status' => true,
                    'device_id' => $data['device_id'],
                    'security_token' => $data['security_token'],
                ]);
            }
            else {
                echo json_encode([ 'status' => false ]);
            }
        }
        else {
        	$errors = $this->form_validation->error_array();
            echo json_encode([
            	'status' => false,
            	'errors' => $errors,
            ]);
        }
    }

    private function generate_random_string($length = 64) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $random_string = '';
        for($i = 0; $i < $length; ++$i) {
            $random_string .= $characters[rand(0, 61)]; // $characters length - 1
        }

        return $random_string;
    }
}
