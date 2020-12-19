<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking extends Root_Controller {
	public function index() {
		echo json_encode([ 'status' => false ]);
	}

	public function add() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('room_id', 'Room ID', 'required');
		$this->form_validation->set_rules('arrival', 'Arrival Time', 'required');
		$this->form_validation->set_rules('checkout', 'Checkout Time', 'required');
		$this->form_validation->set_rules('customer_id', 'Customer ID', 'required');

		if($this->form_validation->run() === false) {
			$errors = $this->form_validation->error_array();
            echo json_encode([
            	'status' => false,
            	'errors' => $errors,
			]);
			exit;
        }
		
		$form_data = [
			'room_id' => $this->input->post('room_id'),
			'arrival' => strtotime($this->input->post('arrival')),
			'checkout' => strtotime($this->input->post('checkout')),
			'customer_id' => $this->input->post('customer_id'),
			'book_time' => time(),
			'is_paid' => 0,
			'status' => 1,
		];

		// @TODO if need to check current_date_time for arrival and/or checkout

		$this->db->select('price')
			->from('rooms')
			->where('id', $form_data['room_id'])
			->where('status', 1)
			->where('locked', 0);
		$result = $this->db->get()->row_array();
		if(!$result) {
			exit(json_encode([
				'status' => false,
				'error_type' => 'room_unavailable',
			]));
		}

		$room_price = $result['price'];

		$this->db->select('id')
			->from('customers')
			->where('id', $form_data['customer_id'])
			->where('status', 1);
		$result = $this->db->get()->row_array();
		if(!$result) {
			exit(json_encode([ 'status' => false ]));
		}

		// checking for only one room book for one customer
		$this->db->select('COUNT(id) total_booked')
			->from('bookings')
			->where('status', 1)
			->where('customer_id', $form_data['customer_id']);
		$result_total_booked = $this->db->get()->row_array();
		if($result_total_booked['total_booked'] > 0) {
			exit(json_encode([ 'status' => false, 'error_type' => 'one_customer_can_book_one_room_only' ]));
		}

		$pay_amount = $this->input->post('pay_amount');
		if($pay_amount) {
			if(ceil($pay_amount) >= floor($room_price)) {
				$form_data['is_paid'] = 1;
			}
		}

		$this->db->trans_start();

		$this->db->insert('bookings', $form_data);
		$booking_id = $this->db->insert_id();

		$this->db->set('locked', 1);
		$this->db->where('id', $form_data['room_id']);
		$this->db->update('rooms');

		if($pay_amount) {
			$payment_data = [
				'booking_id' => $booking_id,
				'customer_id' => $form_data['customer_id'],
				'amount' => $pay_amount,
				'date' => time(),
				'status' => 1,
			];
			$this->db->insert('payments', $payment_data);
		}

		$this->db->trans_complete();

		if($this->db->trans_status()) {
			$this->db->trans_commit();
			echo json_encode([ 'status' => true ]);
		} 
		else {
			$this->db->trans_rollback();
			echo json_encode([ 'status' => false ]);
		}
	}

	public function edit() {
		$this->load->library('form_validation');

		$form_data = [];
		$this->form_validation->set_rules('booking_id', 'Booking ID', 'required');

		if(isset($_POST['room_id'])) {
			$this->form_validation->set_rules('room_id', 'Room ID', 'required');
			$form_data['room_id'] = $this->input->post('room_id');
		}

		if(isset($_POST['arrival'])) {
			// @TODO if need to check current_date_time
			$this->form_validation->set_rules('arrival', 'Arrival Time', 'required');
			$form_data['arrival'] = strtotime($this->input->post('arrival'));
		}

		if(isset($_POST['checkout'])) {
			// @TODO if need to check current_date_time
			$this->form_validation->set_rules('checkout', 'Checkout Time', 'required');
			$form_data['checkout'] = strtotime($this->input->post('checkout'));
		}

		if($this->form_validation->run() === true) {
			if(count($form_data) === 0) {
				exit(json_encode([ 'status' => true ]));
			}

			$booking_id = $this->input->post('booking_id');

			$this->db->select('SUM(amount) total_amount')
				->from('payments')
				->where('booking_id', $booking_id)
				->where('status', 1);
			$result = $this->db->get()->row_array();
			$paid_amount = $result['total_amount'];

			$this->db->select('room_id')
				->from('bookings')
				->where('id', $booking_id);
			$result = $this->db->get()->row_array();
			$room_id_previous = $result['room_id'];

			$this->db->trans_begin();

			if($room_id_previous != $form_data['room_id']) {
				$this->db->select('price')
					->from('rooms')
					->where('locked', 0)
					->where('status', 1)
					->where('id', $form_data['room_id']);
				$result = $this->db->get()->row_array();

				if(!$result) {
					$this->db->trans_rollback();
					exit(json_encode([
						'status' => false,
						'error_type' => 'room_unavailable',
					]));
				}

				if($paid_amount > 0) {
					$form_data['is_paid'] = ceil($paid_amount) >= floor($result['price']) ? 1 : 0;
				}
			}

			$this->db->where('id', $booking_id);
			$status = $this->db->update('bookings', $form_data);
			if(!$status) {
				$this->db->trans_rollback();
				exit(json_encode([ 'status' => false ]));
			}

			$lock_current_room = [ 'locked' => 1 ];
			$this->db->where('id', $form_data['room_id']);
			$this->db->update('rooms', $lock_current_room);

			if($room_id_previous != $form_data['room_id']) {
				$this->db->set('locked', 0);
				$this->db->where('id', $room_id_previous);
				$this->db->update('rooms');
			}

			if($this->db->trans_status()) {
				$this->db->trans_commit();
				echo json_encode([ 'status' => true ]);
			}
			else {
				$this->db->trans_rollback();
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

		$this->form_validation->set_rules('booking_id', 'Booking ID', 'required');

		if($this->form_validation->run() === true) {
			$booking_id = $this->input->post('booking_id');

			$this->db->select('room_id')
				->from('bookings')
				->where('status', 1)
				->where('id', $booking_id);
			$result = $this->db->get()->row_array();
			if(!$result) {
				exit(json_encode([ 'status' => false ]));
			}

			$this->db->trans_start();

			$this->db->set('locked', 0);
			$this->db->where('id', $result['room_id']);
			$this->db->update('rooms');

			$this->db->where('id', $booking_id);
			$status = $this->db->update('bookings', [ 'status' => 0 ]);

			$this->db->where('booking_id', $booking_id);
			$this->db->update('payments', [ 'status' => 0 ]);

			$this->db->trans_complete();
			if($this->db->trans_status()) {
				$this->db->trans_commit();
				echo json_encode([ 'status' => true ]);
			}
			else {
				$this->db->trans_rollback();
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
