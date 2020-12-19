<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Listing extends Root_Controller {
	public function index() {
		$query = '
			SELECT b.id, b.arrival, b.checkout, c.first_name, c.last_name, r.room_number, (
				SELECT SUM(amount) total_paid
				FROM payments
				WHERE booking_id = b.id AND status = 1
			) total_paid
			FROM bookings b
			INNER JOIN customers c ON c.id = b.customer_id
			INNER JOIN rooms r ON r.id = b.room_id
			WHERE b.status = 1
			ORDER BY b.id ASC
		';

		$query = $this->db->query($query);
		$results = $query->result_array();
		if(!$results) {
			exit('[]');
		}

		echo json_encode($results);
	}
}
