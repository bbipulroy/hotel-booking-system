<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends Root_Controller {
	public function index() {
		echo json_encode([ 'status' => false ]);
	}

	public function add() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('booking_id', 'Booking ID', 'required');
		$this->form_validation->set_rules('amount', 'Amount', 'required');

		if($this->form_validation->run() === true) {
			$form_data = [
				'booking_id' => $this->input->post('booking_id'),
				'amount' => $this->input->post('amount'),
				'date' => time(),
				'status' => 1,
			];

			$this->db->select('b.customer_id, b.is_paid, r.price')
				->from('bookings b')
				->join('rooms r', 'r.id = b.room_id', 'inner')
				->where('b.status', 1)
				->where('b.id', $form_data['booking_id']);
			$result = $this->db->get()->row_array();
			if(!$result) {
				exit(json_encode([ 'status' => false ]));
			}

			$room_price = $result['price'];
			$form_data['customer_id'] = $result['customer_id'];
			if($result['is_paid'] == 1) {
				exit(json_encode([ 'status' => false, 'error_type' => 'already_paid' ]));
			}

			$this->db->trans_begin();

			$this->db->select('SUM(amount) total_paid')
				->from('payments')
				->where('status', 1)
				->where('booking_id', $form_data['booking_id']);
			$result = $this->db->get()->row_array();

			$is_paid = false;
			if(ceil($result['total_paid'] + $form_data['amount']) >= floor($room_price)) {
				$this->db->set('is_paid', 1);
				$this->db->where('id', $form_data['booking_id']);
				$this->db->update('bookings');

				$is_paid = true;
			}
			else {
				// extra checking, if not need then comment else codes just
				$this->db->trans_rollback();
				exit(json_encode([ 'status' => false, 'error_type' => 'not_due_on_checkout_time' ]));
			}

			$this->db->insert('payments', $form_data);
			$insert_id = $this->db->insert_id();

			if($this->db->trans_status()) {
				$this->db->trans_commit();
				echo json_encode([
					'status' => true,
					'insert_id' => $insert_id,
					'is_paid' => $is_paid,
				]);
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

	public function edit() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('payment_id', 'Payment ID', 'required');
		$this->form_validation->set_rules('amount', 'Amount', 'required');

		if($this->form_validation->run() === true) {
			$payment_id = $this->input->post('payment_id');
			$amount = $this->input->post('amount');

			$this->db->select('p.booking_id, p.amount, r.price')
				->from('payments p')
				->join('bookings b', 'b.id = p.booking_id', 'inner')
				->join('rooms r', 'r.id = b.room_id', 'inner')
				->where('p.status', 1)
				->where('p.id', $payment_id);
			$result = $this->db->get()->row_array();
			if(!$result) {
				exit(json_encode([ 'status' => false ]));
			}

			$booking_id = $result['booking_id'];
			$previous_amount = $result['amount'];
			$room_price = $result['price'];

			if($previous_amount == $amount) {
				exit(json_encode([ 'status' => true ]));
			}

			$this->db->trans_start();

			$this->db->set('amount', $amount);
			$this->db->where('id', $payment_id);
			$this->db->update('payments');

			$this->db->select('SUM(amount) total_paid')
				->from('payments')
				->where('status', 1)
				->where('booking_id', $booking_id);
			$result = $this->db->get()->row_array();

			$is_paid = ceil($result['total_paid']) >= floor($room_price);

			$this->db->set('is_paid', $is_paid ? 1 : 0);
			$this->db->where('id', $booking_id);
			$this->db->update('bookings');

			$this->db->trans_complete();

			if($this->db->trans_status()) {
				$this->db->trans_commit();
				echo json_encode([
					'status' => true,
					'is_paid' => $is_paid,
				]);
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

		$this->form_validation->set_rules('payment_id', 'Payment ID', 'required');

		if($this->form_validation->run() === true) {
			$payment_id = $this->input->post('payment_id');

			$this->db->select('p.booking_id, r.price')
				->from('payments p')
				->join('bookings b', 'b.id = p.booking_id', 'inner')
				->join('rooms r', 'r.id = b.room_id', 'inner')
				->where('p.status', 1)
				->where('p.id', $payment_id);
			$result = $this->db->get()->row_array();
			if(!$result) {
				exit(json_encode([ 'status' => false ]));
			}

			$booking_id = $result['booking_id'];
			$room_price = $result['price'];

			$this->db->trans_start();

			$this->db->set('status', 0);
			$this->db->where('id', $payment_id);
			$this->db->update('payments');

			$this->db->select('SUM(amount) total_paid')
				->from('payments')
				->where('status', 1)
				->where('booking_id', $booking_id);
			$result = $this->db->get()->row_array();

			$is_paid = ceil($result['total_paid']) >= floor($room_price);

			$this->db->set('is_paid', $is_paid ? 1 : 0);
			$this->db->where('id', $booking_id);
			$this->db->update('bookings');

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
