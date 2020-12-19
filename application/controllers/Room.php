<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Room extends Root_Controller {
	public function index() {
		echo json_encode([ 'status' => false ]);
	}

	public function add() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('room_number', 'Room Number', 'required');
		$this->form_validation->set_rules('price', 'Price', 'required');
		$this->form_validation->set_rules('max_persons', 'Max Persons', 'required');
		$this->form_validation->set_rules('room_type', 'Room Type', 'required');

		if($this->form_validation->run() === true) {
			$form_data = [
				'room_number' => $this->input->post('room_number'),
				'price' => $this->input->post('price'),
				'max_persons' => $this->input->post('max_persons'),
				'room_type' => $this->input->post('room_type'),
				'locked' => 0,
				'status' => 1,
			];

			// @TODO check if need to check uniqe room_no
			$status = $this->db->insert('rooms', $form_data);
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
		$this->form_validation->set_rules('room_id', 'Room ID', 'required');

		if(isset($_POST['room_number'])) {
			// @TODO check if need to check uniqe room_no
			$this->form_validation->set_rules('room_number', 'Room Number', 'required');
			$form_data['room_number'] = $this->input->post('room_number');
		}

		if(isset($_POST['price'])) {
			$this->form_validation->set_rules('price', 'Price', 'required');
			$form_data['price'] = $this->input->post('price');
		}

		if(isset($_POST['max_persons'])) {
			$this->form_validation->set_rules('max_persons', 'Max Persons', 'required');
			$form_data['max_persons'] = $this->input->post('max_persons');
		}

		if(isset($_POST['room_type'])) {
			$this->form_validation->set_rules('room_type', 'Room Type', 'required');
			$form_data['room_type'] = $this->input->post('room_type');
		}
		
		if($this->form_validation->run() === true) {
			if(count($form_data) === 0) {
				exit(json_encode([ 'status' => true ]));
			}

			$this->db->where('id', $this->input->post('room_id'));
			$status = $this->db->update('rooms', $form_data);
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

		$this->form_validation->set_rules('room_id', 'Room ID', 'required');

		if($this->form_validation->run() === true) {
			$this->db->where('id', $this->input->post('room_id'));
			$status = $this->db->update('rooms', [ 'status' => 0 ]);

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
