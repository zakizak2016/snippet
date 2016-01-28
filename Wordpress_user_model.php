<?php 
/**
 * User_model
 *
 * @author      Carl Victor Fontanos
 * @authorurl   www.carlofontanos.com
 * @version     1.0
 *
 */
 
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function login($user_email, $user_pass) {
        
        $query_password = $this->db->get_where('ci_users', array('user_email' => $user_email), 1, 0);
        $user = $query_password->row();
        
        if($this->passwordhash->CheckPassword($user_pass, $user->user_pass)) {
            
            $newdata = array(
                'ID'            => $user->ID,
                'logged_in'     => TRUE
            );
            
            $this->session->set_userdata($newdata);
            
            return true;        
        }
        return false;
    }
    
    
    public function add_user($username, $email, $password) {
    
        $user_data = array(
            'user_login'        =>  $username,
            'user_email'        =>  $email,
            'user_pass'         =>  $this->passwordhash->HashPassword($password),
            'user_registered'   =>  date("Y-m-d H:i:s", time()),
            'user_nicename'     =>  url_title($email, 'dash', TRUE),    
        );
        
        $cleaned_data = $this->security->xss_clean($user_data);
        
        $this->db->insert('ci_users', $user_data);
        
        $this->db->select('ID');
        $user_check = $this->db->get_where('ci_users', array('user_email' => $email), 1, 0);
        
        if($user_check->num_rows() > 0){
            $user = $user_check->row();
            return $user->ID;
        } else {
            return false;
        }
            
    }
    
    public function add_user_meta($user_id, $meta_key, $meta_value) {
    
        $user_metadata = array(
            'user_id'           =>  $user_id,
            'meta_key'          =>  $meta_key,
            'meta_value'        =>  $meta_value
        );
        
        $cleaned_data = $this->security->xss_clean($user_metadata);
        
        $this->db->insert('ci_usermeta', $cleaned_data);
        
        $add_meta_check = $this->db->get_where('ci_usermeta', array('user_id' => $user_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value), 1, 0);
        
        if($add_meta_check->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function update_user_meta($user_id, $meta_key, $meta_value) {
        
        $meta_exits = $this->db->get_where('ci_usermeta', array('user_id' => $user_id, 'meta_key' => $meta_key), 1, 0);
        
        if($meta_exits->num_rows() > 0){
        
            $user_metadata = array(
                'meta_value'        =>  $meta_value
            );
            
            $cleaned_data = $this->security->xss_clean($user_metadata);
            
            $this->db->where(array('user_id' => $user_id, 'meta_key' => $meta_key));
            $this->db->update('ci_usermeta', $cleaned_data);
            
            $update_meta_check = $this->db->get_where('ci_usermeta', array('user_id' => $user_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value), 1, 0);
            
            if($update_meta_check->num_rows() > 0){
                return true;
            } else {
                return false;
            }
            
        } else {
            self::add_user_meta($user_id, $meta_key, $meta_value);
            return true;
            
        }
        
    }
    
    public function delete_user_meta($user_id, $meta_key = '') {
        
        if(!empty($meta_key)){
            $user_metadata = array(
                'user_id'           =>  $user_id,
                'meta_key'          =>  $meta_key
            );
        } else {
            $user_metadata = array(
                'user_id'           =>  $user_id
            );
        }
        
        $cleaned_data = $this->security->xss_clean($user_metadata);
            
        $this->db->where($cleaned_data);
        $this->db->delete('ci_usermeta');

        $delete_meta_check = $this->db->get_where('ci_usermeta', $user_metadata, 1, 0);
            
        if($delete_meta_check->num_rows() == 0){
            return true;
        } else {
            return false;
        }       
        
    }
    
    public function get_user_meta($user_id, $key = '', $single = TRUE) {
        
        $where = '';
        
        if($key){
            $where .= ' AND meta_key = "'.$key.'"';
        }
        
        $user_meta = $this->db->query('SELECT * FROM ci_usermeta WHERE user_id = ?'.$where, array($user_id) );
        
        if($single){
        
            $row = $user_meta->row();
            if($row){
                return $row->meta_value;
            } else {
                return '';
            }
                
        } else {
            return $user_meta->result();
        }
        
        
    }
    
    public function update_user($userdata_array) {
        
        extract($userdata_array); 
        $data = '';
        
        if(!empty($user_login)){
            $data['user_login'] = $user_login;
        }
        if(!empty($user_email)){
            $data['user_email'] = $user_email; 
            $data['user_nicename'] = url_title($user_email, 'dash', TRUE);
        }
        if(!empty($user_pass)){
            $data['user_pass'] = $this->passwordhash->HashPassword($user_pass);
        }
                
        $cleaned_data = $this->security->xss_clean($data);
        
        $this->db->update('ci_users', $cleaned_data);
        
    }
    
    public function delete_user($user_id) {
        
        $this->db->delete('ci_users', array('ID' => $user_id));
        self::delete_user_meta($user_id);
        
        $delete_check = $this->db->query('SELECT * from ci_users u LEFT JOIN ci_usermeta um ON u.ID = um.user_id WHERE u.ID = ?', array($user_id) );
        
        if($delete_check->num_rows() == 0){
            return true;
        } else {
            return false;
        }
    }
    
}
