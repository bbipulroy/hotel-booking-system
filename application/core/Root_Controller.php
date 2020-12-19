<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Root_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();

        // json response
        header('Content-Type: application/json');

        if(!(isset($_SERVER['HTTP_DEVICE_ID']) && isset($_SERVER['HTTP_SECURITY_TOKEN']))) {
            exit(json_encode([
                'status' => false,
                'error_type' => 'invalid_credentials',
            ]));
        }

        $device_id = $_SERVER['HTTP_DEVICE_ID'];
        $security_token = $_SERVER['HTTP_SECURITY_TOKEN'];

        $time = time();
        
        $this->db->select('id, user_id, last_used_time')
            ->from('user_auth')
            ->where('device_id', $device_id)
            ->where('security_token', $security_token);
        $result = $this->db->get()->row_array();
        if(!$result) {
            exit(json_encode([
                'status' => false,
                'error_type' => 'wrong_credentials',
            ]));
        }

        // @TODO dynamic from admin panel or env file
        $auth_session_time = 3600;

        if($time - $result['last_used_time'] > $auth_session_time) {
            exit(json_encode([
                'status' => false,
                'error_type' => 'token_expired',
            ]));
        }

        $this->db->set('last_used_time', $time);
        $this->db->where('id', $result['id']);
        $this->db->update('user_auth');

        define('AUTH_USER_ID', $result['user_id']);
    }
}
