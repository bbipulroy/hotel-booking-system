<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {
	public function index() {
		echo json_encode([ 'status' => false ]);
	}

	public function add() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('first_name', 'First Name', 'required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required');
		$this->form_validation->set_rules('phone', 'Phone', 'required');

		if($this->form_validation->run() === true) {
			$email = $this->input->post('email');
			if($email === null || ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
  				exit(json_encode([
  					'status' =>  false,
  					'errors' => [
  						'email' => 'The email is not valid.',
  					],
  				]));
			}

			$form_data = [
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'phone' => $this->input->post('phone'),
				'email' => $email,
				'registered_at' => time(),
				'status' => 1,
			];

			// @TODO check if need to check uniqe email and/or phone

			$status = $this->db->insert('customers', $form_data);
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

	public function edit() {
		$this->load->library('form_validation');

		$form_data = [];
		$this->form_validation->set_rules('customer_id', 'Customer ID', 'required');

		if(isset($_POST['first_name'])) {
			$this->form_validation->set_rules('first_name', 'First Name', 'required');
			$form_data['first_name'] = $this->input->post('first_name');
		}

		if(isset($_POST['last_name'])) {
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');
			$form_data['last_name'] = $this->input->post('last_name');
		}

		if(isset($_POST['phone'])) {
			// @TODO check if need to check uniqe phone
			$this->form_validation->set_rules('phone', 'Phone', 'required');
			$form_data['phone'] = $this->input->post('phone');
		}
		
		if($this->form_validation->run() === true) {
			$email = $this->input->post('email');
			if($email != '') {
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					exit(json_encode([
	  					'status' =>  false,
	  					'errors' => [
	  						'email' => 'The email is not valid.',
	  					],
	  				]));
				}

				// @TODO check if need to check uniqe email
				$form_data['email'] = $email;
			}

			if($email === '') {
				$form_data['email'] = $email;
			}

			if(count($form_data) === 0) {
				exit(json_encode([ 'status' => true ]));
			}

			$this->db->where('id', $this->input->post('customer_id'));
			$status = $this->db->update('customers', $form_data);
			if($status) {
				echo json_encode([ 'status' => true ]);
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

	public function cdelete() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('customer_id', 'Customer ID', 'required');

		if($this->form_validation->run() === true) {
			$this->db->where('id', $this->input->post('customer_id'));
			$status = $this->db->update('customers', [ 'status' => 0 ]);

			if($status) {
				echo json_encode([ 'status' => true ]);
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
}
