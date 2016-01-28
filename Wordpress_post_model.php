<?php 
/**
 * Post_model
 *
 * @author      Carl Victor Fontanos
 * @authorurl   www.carlofontanos.com
 * @version     1.0
 *
 */
 /*
 get_post()
get_posts()
insert_post()
update_post()
delete_post()
get_post_meta()
add_post_meta()
update_post_meta()
delete_post_meta()
insert_post_category()
update_post_category()
add_term()
update_term()
delete_term()
is_term()
is_post()
 */
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function get_post($post_identifier_array = '') {
        
        if(!empty($post_identifier_array)){
        
            extract($post_identifier_array);
                
            if(!empty($post_name)){
                $where = array('post_name' => $post_name);
            } elseif($post_id) {
                $where = array('ID' => $post_id);
            } else {
                return false;
            }
            
            $post = $this->db->get_where('ci_posts', $where, 1, 0);
            
            if($post->num_rows() > 0){
                return $post->result()[0];
            } else {
                return false;
            }
        
        } else {
            return false;
        } 
        
    }
    
    /**
    Usage: 
    $args = array(
        'posts_per_page'   => 5,
        'post_author'      => 17,
        'category'         => 1,
        'category_name'    => 'uncategorized',
        'orderby'          => 'post_date',
        'order'            => 'ASC',
        'post_type'        => 'page',
        'post_status'      => 'publish'
    );
    $result = $post->get_posts($args));
    print_r($result);
    */
    public function get_posts($post_data = '') {
        
        $post_author = '';
        $term_id = '';
        $slug = '';
        $post_status = '';
        $post_type = '';    
        $orderby = '';
        $order = '';
        $limit = '';
    
        if(!empty($post_data)) {
        
            extract($post_data);
                
            if(!empty($post_author)) {
                $post_author = ' AND p.post_author = '.$post_author.' ';
            }
            if(!empty($category)) {
                $term_id = ' AND t.term_id = '.$category;
            }
            if(!empty($category_name)) {
                $slug = ' AND t.slug = "'.$category_name.'" ';
            }
            if(!empty($post_status)) {
                $post_status = ' AND p.post_status = "'.$post_status.'" ';
            }
            if(!empty($post_type)) {
                $post_type = ' AND p.post_type = "'.$post_type.'" ';
            }
            if(!empty($orderby)) {
                $orderby = ' ORDER BY '.$orderby.' ';
            }
            if(!empty($order)) {
                $order = ' '.$order.' ';
            }
            if(!empty($posts_per_page)) {
                $limit = ' LIMIT '.$posts_per_page.' ';
            }
        }
        
        $posts = $this->db->query('
            SELECT p.*, t.term_id AS category_id, t.slug AS category_name FROM ci_posts p
            LEFT JOIN ci_term_relationships tr ON tr.object_id = p.ID
            LEFT JOIN ci_terms t ON t.term_id = tr.term_taxonomy_id
            WHERE p.ID > 0 '.$post_author.$term_id.$slug.$post_status.$post_type.$orderby.$order.$limit.' 
        ');
        
        if($posts->num_rows() > 0){
            return $posts->result();
        } else {
            return false;
        }
        
    }
    
    /**
    Usage: 
    $args = array(
        'post_author'       =>  $current_user->ID,
        'post_content'      =>  'Some Description',
        'post_title'        =>  'Some Title',
        'post_excerpt'      =>  'Short Description',
        'post_type'         =>  'post',
        'post_category'     =>  1
    );
    
    $post_id = $post->insert_post($args);
    echo $post_id;
    */
    public function insert_post($post_data_array) {
        
        $current_user = current_user();
        
        $post_content = '';
        $post_title = '';
        $post_excerpt = '';
        $post_type = 'post';
        $post_category = 1;
        
        extract($post_data_array);
        
        if(!empty($post_author) && $this->user_model->is_user($post_author)){
            $author = $post_author;
        } elseif(is_user_logged_in()) {
            $author = $current_user->ID;
        } else {
            $author = $this->user_model->get_superadmin();
        }
        
        $post_data = array(
            'post_author'       =>  $author,
            'post_date'         =>  date("Y-m-d H:i:s", time()),
            'post_date_gmt'     =>  date("Y-m-d H:i:s", time()),
            'post_content'      =>  $post_content,
            'post_title'        =>  $post_title,
            'post_excerpt'      =>  $post_excerpt,
            'post_status'       =>  'publish',
            'post_type'         =>  $post_type,
            'post_name'         =>  url_title($post_title, 'dash', TRUE),   
            'post_modified'     =>  date("Y-m-d H:i:s", time()),    
            'post_modified_gmt' =>  date("Y-m-d H:i:s", time()),    
        );
        
        $cleaned_data = $this->security->xss_clean($post_data);
        
        $this->db->insert('ci_posts', $cleaned_data);
        
        $check_insert = $this->db->get_where('ci_posts', $cleaned_data, 1, 0);
        
        if($check_insert->num_rows() > 0){      
            
            $category = $post_category;
            
            $post = $check_insert->row();
            self::insert_post_category($post->ID, $category);
            
            return $post->ID;
            
        } else {
            return false;
            
        }
    }
    
    /**
    Usage: 
    $args = array(
        'ID'                =>  1008,
        'post_author'       =>  16,
        'post_content'      =>  'Some Description',
        'post_title'        =>  'Some Title',
        'post_excerpt'      =>  'Short Description',
        'post_type'         =>  'page',
        'post_category'     =>  1
    );
    
    $post_id = $post->update_post($args);
    echo $post_id;
    */
    public function update_post($post_data_array) {
        
        $current_user = current_user();
        
        extract($post_data_array);
        
        $post_data = '';
        
        if(!empty($ID) && self::is_post($ID)){
        
            if(!empty($post_author) && $this->user_model->is_user($post_author)) {
                $post_data['post_author'] = $post_author;
            } elseif (is_user_logged_in()) {
                $post_data['post_author'] = $current_user->ID;
            } else {
                $post_data['post_author'] = $this->user_model->get_superadmin();
            }
            if(!empty($post_content)) {
                $post_data['post_content'] = $post_content;
            }   
            if(!empty($post_title)) {
                $post_data['post_title'] = $post_title;
                $post_data['post_name'] = url_title($post_title, 'dash', TRUE);
            }
            if(!empty($post_excerpt)) {
                $post_data['post_excerpt'] = $post_excerpt;
            }
            if(!empty($post_status)) {
                $post_data['post_status'] = $post_status;
            }
            if(!empty($post_type)) {
                $post_data['post_type'] = $post_type;
            }
                
            $post_data['post_modified'] = date("Y-m-d H:i:s", time());  
            $post_data['post_modified_gmt'] = date("Y-m-d H:i:s", time());  
                
            $cleaned_data = $this->security->xss_clean($post_data);
            
            $this->db->where('ID', $ID);
            $this->db->update('ci_posts', $cleaned_data);
            
            $check_update = $this->db->get_where('ci_posts', $cleaned_data, 1, 0);
        
            if($check_update->num_rows() > 0){      
                
                $post = $check_update->row();
                
                if(!empty($post_category) && $post_category > 0 && self::is_term($post_category)) {
                    self::update_post_category($post->ID, $post_category);
                }
                
                return $post->ID;
                
            } else {
                return false;
                
            }
            
        } else {
            return false;
            
        }   
                
    }
    
    public function delete_post($post_id) {
    
        $this->db->delete('ci_posts', array('ID' => $post_id));
        $this->db->delete('ci_term_relationships', array('object_id' => $post_id));
        self::delete_post_meta($post_id);
        
        $delete_check = $this->db->query('
            SELECT * from ci_posts p 
            LEFT JOIN ci_postmeta pm ON p.ID = pm.post_id 
            LEFT JOIN ci_term_relationships tr ON p.ID = tr.object_id 
            WHERE p.ID = ?', array($post_id) );
        
        if($delete_check->num_rows() == 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function get_post_meta($post_id, $key = '', $single = TRUE) {
    
        $where = '';
        
        if($key){
            $where .= ' AND meta_key = "'.$key.'"';
        }
        
        $post_meta = $this->db->query('SELECT * FROM ci_postmeta WHERE post_id = ?'.$where, array($post_id) );
        
        if($single){
        
            $row = $post_meta->row();
            if($row){
                return $row->meta_value;
            } else {
                return '';
            }
                
        } else {
            return $post_meta->result();
        }
    }
    
    public function add_post_meta($post_id, $meta_key, $meta_value) {
        
        $post_metadata = array(
            'post_id'           =>  $post_id,
            'meta_key'          =>  $meta_key,
            'meta_value'        =>  $meta_value
        );
        
        $cleaned_data = $this->security->xss_clean($post_metadata);
        
        $this->db->insert('ci_postmeta', $cleaned_data);
        
        $add_meta_check = $this->db->get_where('ci_postmeta', array('post_id' => $post_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value));
        
        if($add_meta_check->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function update_post_meta($post_id, $meta_key, $meta_value) {
        
        $meta_exits = $this->db->get_where('ci_postmeta', array('post_id' => $post_id, 'meta_key' => $meta_key), 1, 0);
        
        if($meta_exits->num_rows() > 0){
        
            $post_metadata = array(
                'meta_value'        =>  $meta_value
            );
            
            $cleaned_data = $this->security->xss_clean($post_metadata);
            
            $this->db->where(array('post_id' => $post_id, 'meta_key' => $meta_key));
            $this->db->update('ci_postmeta', $cleaned_data);
            
            $update_meta_check = $this->db->get_where('ci_postmeta', array('post_id' => $post_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value), 1, 0);
            
            if($update_meta_check->num_rows() > 0){
                return true;
            } else {
                return false;
            }
            
        } else {
            self::add_post_meta($post_id, $meta_key, $meta_value);
            return true;
            
        }
    }
    
    public function delete_post_meta($post_id, $meta_key = '') {
    
        if(!empty($meta_key)){
            $post_metadata = array(
                'post_id'           =>  $post_id,
                'meta_key'          =>  $meta_key
            );
        } else {
            $post_metadata = array(
                'post_id'           =>  $post_id
            );
        }
        
        $cleaned_data = $this->security->xss_clean($post_metadata);
            
        $this->db->where($cleaned_data);
        $this->db->delete('ci_postmeta');

        $delete_meta_check = $this->db->get_where('ci_postmeta', $post_metadata, 1, 0);
            
        if($delete_meta_check->num_rows() == 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function insert_post_category($post_id, $term_id) {
        
        if(!empty($term_id) && !empty($post_id) && self::is_term($term_id) && self::is_post($post_id)){
            
            $data = array(
                'object_id'         =>  $post_id,
                'term_taxonomy_id'  =>  $term_id,
                'term_order'        =>  0
            );
            
            $cleaned_data = $this->security->xss_clean($data);
            
            $this->db->insert('ci_term_relationships', $cleaned_data);
            
            $check_insert = $this->db->get_where('ci_term_relationships', $cleaned_data, 1, 0);
        
            if($check_insert->num_rows() > 0){      
                        
                $term = $check_insert->row();

                return $term->term_taxonomy_id;
                
            } else {
                return false;
                
            }
            
        } else {
            return false;
        }
        
    }
    
    public function update_post_category($post_id, $term_id) {
        
        if(!empty($term_id) && !empty($post_id) && self::is_term($term_id) && self::is_post($post_id)){
            
            $data = array(
                'term_taxonomy_id'  =>  $term_id,
            );
            
            $cleaned_data = $this->security->xss_clean($data);
            
            $this->db->where('object_id', $post_id);
            $this->db->update('ci_term_relationships', $cleaned_data);
            
            $check_update = $this->db->get_where('ci_term_relationships', $cleaned_data, 1, 0);
        
            if($check_update->num_rows() > 0){      
                        
                $term = $check_update->row();

                return $term->term_taxonomy_id;
                
            } else {
                return false;
                
            }
        
        } else {
            return false;
            
        }
    }
    
    /**
    Usage: 
    $args = array(
        'name'          => 'Cateory Test',
        'slug'          => '',
        'description'   => 'A very beautiful category',
        'taxonomy'      => 'category',
        'parent'        => ''
    );
    $term_id = $post->add_term($args);
    echo $term_id;
    */
    public function add_term($term_data){
        
        $description = '';
        
        extract($term_data); 
        
        $term = '';
        
        if(!empty($name) && !empty($taxonomy)){
        
            if(!empty($name)){
                $term['name'] = $name;
            }
            if(!empty($slug)){
                $term['slug'] = $slug;
            } else {
                $term['slug'] = url_title($name, 'dash', TRUE);
            }
            
            $this->db->insert('ci_terms', $term);
        
            $get_term_id = $this->db->get_where('ci_terms', $term);
            
            if($get_term_id->num_rows > 0){
                $term = $get_term_id->row();
                
                $term_taxonomy = array(
                    'term_taxonomy_id'  => $term->term_id,
                    'term_id'           => $term->term_id,
                    'taxonomy'          => $taxonomy,
                    'parent'            => $parent ? $parent : 0,
                    'description'       => $description
                );
                
                $this->db->insert('ci_term_taxonomy', $term_taxonomy);
                
                $term_taxonomy_check = $this->db->get_where('ci_term_taxonomy', $term_taxonomy);
                
                if($term_taxonomy_check->num_rows > 0){
                    $term_taxonomy = $term_taxonomy_check->row();
                    return $term_taxonomy->term_taxonomy_id;
                    
                } else {
                    return false;
                }
                
            } else {
                return false;
            }
        }
    }
    
    /**
    Usage: 
    $post->update_term(1, array(
        'name'      => 'No Category',
        'slug'      => 'no-category',
        'taxonomy'  => 'category',
        'parent'    => 4
    ));
    */
    public function update_term($term_id, $term_array){
        
        if(!empty($term_id) && !empty($term_array)){
                            
            extract($term_array);
            
            if(!empty($taxonomy) || !empty($parent)) {
                
                $term_taxonomy = '';
                
                if(!empty($taxonomy)){
                    $term_taxonomy['taxonomy'] = $taxonomy;
                }
                if(isset($parent)){
                    $term_taxonomy['parent']  = $parent;
                }
                    
                $this->db->where('term_taxonomy_id', $term_id);
                $this->db->update('ci_term_taxonomy', $term_taxonomy);
            }   
                        
            if(!empty($name)) {
                
                $term = '';
                
                if(!empty($name)){
                    $term['name'] = $name;
                }
                if(!empty($slug)){
                    $term['slug'] = $slug;
                } else {
                    $term['slug'] = url_title($name, 'dash', TRUE);
                }
                    
                $this->db->where('term_id', $term_id);
                $this->db->update('ci_terms', $term);
            }
            
            return true;
                
        } else {
            return false;
        }
    }
    
    public function delete_term($term_id){
            
        # Fix all parents, set back to 0
        $get_cat_childrens = $this->db->get_where('ci_term_taxonomy', array('parent' => $term_id));
        
        if($get_cat_childrens->num_rows() > 0){
        
            foreach($get_cat_childrens->result() as $term_cat){
                $this->db->where('term_id', $term_cat->term_id);
                $this->db->update('ci_term_taxonomy', array('parent' => 0));
            }
        
        }   
                
        # Fix all post categories, set back to 1
        $get_post_childrens = $this->db->get_where('ci_term_relationships', array('term_taxonomy_id' => $term_id));
        
        if($get_post_childrens->num_rows() > 0){    
        
            foreach($get_post_childrens->result() as $term_post){
                $this->db->where('object_id', $term_post->object_id);
                $this->db->update('ci_term_relationships', array('term_taxonomy_id' => 1));
            }
        
        }
        
        $this->db->delete('ci_terms', array('term_id' => $term_id));
        $this->db->delete('ci_term_taxonomy', array('term_taxonomy_id' => $term_id));
        
        $this->db->select('*');
        $this->db->from('ci_terms');
        $this->db->join('ci_term_taxonomy', 'ci_term_taxonomy.term_taxonomy_id = ci_terms.term_id', 'left');
        $this->db->where('ci_terms.term_id', $term_id);
        $delete_check = $this->db->get();
        
        if($delete_check->num_rows() == 0){
            return true;
        } else {
            return false;
        }
        
    }
    
    public function is_term($term_id) {
    
        $check_term_id = $this->db->query('
            SELECT t.term_id FROM ci_terms t 
            LEFT JOIN  ci_term_taxonomy tt ON t.term_id = tt.term_taxonomy_id
            WHERE t.term_id = ?', array($term_id)
        );
        
        if($check_term_id->num_rows() > 0){
            return true;
        } else {
            return false;
        }
        
    }
    
    public function is_post($post_id) {
            
        $check_ID = $this->db->get_where('ci_posts', array('ID' => $post_id), 1, 0);
        
        if($check_ID->num_rows() > 0){
            return true;
        } else {
            return false;
        }
        
    }
    
}
