<?php
if ( ! class_exists( 'Like_it' ) ) :

    class Like_it {
        private $options;
        
        public function __construct(){
            
            if ( is_admin() ){
                add_action( 'admin_menu', array($this, 'like_it_add_admin_menu') );
                add_action( 'admin_init', array($this, 'like_it_settings_init') );
            }

            add_action('wp_enqueue_scripts', array($this, 'like_it_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'like_it_css'), 11, 1 );

            $this->initShortcodes();

            $this->ajaxFunctions();
            
            $this->contentAfterBefore();

        }
        
        /**
         * Initialize Shortcodes
         * Shortcodelari Baslatir
         */
        private function initShortcodes() {
            add_shortcode('like_it',array($this, 'shortcode_like_it'));
            add_shortcode('like_it_posts',array($this, 'shortcode_like_it_posts'));
            add_shortcode('like_it_tags',array($this, 'shortcode_like_it_tags'));
        }
        /**
         * Initialize ajaxFunctions
         * Ajax Fonksiyonunu Baslatir
         */        
        private function ajaxFunctions() {
            add_action('wp_ajax_like_it', array($this, 'ajax'));
            add_action('wp_ajax_nopriv_like_it', array($this, 'ajax'));
        }
        /**
         * The like/dislike buttons becomes visible at the front-end
         * Like/Dislike butonu on tarafta gorunur hale gelir
         */        
        private function contentAfterBefore() {
         add_filter( 'the_content', array($this, 'like_it_filter_the_content') );
        }
        public function like_it_add_admin_menu() {
         add_options_page(__('Configuration Like it now', 'like-it'), __('Like it now', 'like-it'), 'manage_options', 'like_it_options', array($this, 'like_it_options_page') );
        }
        /**
         * Scripts and styles loads on admin
         * Admin bolumu scriptleri ve stilleri yuklenir
         */    
        public function like_it_css() {

                $plugins_url = plugin_dir_url( __FILE__ );
                
                wp_enqueue_style( 'likeicon', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');

                wp_register_script('like-admin', $plugins_url . '/templates/like-admin.js', array('jquery'));
                wp_enqueue_script('like-admin');

        }
        /**
         * Scripts and styles loads on front
         * Site bolumune/on tarafa scriptler ve stiller yuklenir
         */            
        public function like_it_scripts() {
            
                $plugins_url = plugin_dir_url( __FILE__ );

                wp_enqueue_style( 'likeicon', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');                wp_enqueue_style( 'like-it.css', $plugins_url . '/templates/like-it.css' );
                
                wp_register_script('like', $plugins_url . 'templates/like.js', array('jquery'));
                
                wp_enqueue_script('like');
                
                wp_enqueue_style( 'like-it.css', $plugins_url . '/templates/like-it.css' );
                

                wp_localize_script('like', 'like_it', array(
                        'url'   => admin_url('admin-ajax.php'),
                        'nonce' => wp_create_nonce('like-it'),
                    )
                );

        }
        /**
         * Which function is initialize on constructor
         * Constructor da baslatilan ayar fonksiyonu
         */ 
        public function like_it_settings_init() {
            register_setting('like_it_now', 'like_it_options');
                        
            // add your settings section
            add_settings_section(
                'like_it_settings', 
                'Button location', 
                array($this, 'callback_render_section'), 
                'like_it_group'
            );
            
            
            add_settings_field(
                'show_like_it_before',
                'Before Content', 
                array($this, 'callback_render_before'),
                'like_it_group',
                'like_it_settings'
            );
            
            add_settings_field(
                'show_like_it_after',
                'After Content', 
                array($this, 'callback_render_after'),
                'like_it_group',
                'like_it_settings'
            );



        }
        public function callback_render_section() {
            echo '<p>Select which areas of content you wish to display.</p>';
        } 
        /**
         * Button settings callback function
         * Buton yeri secenegi ayarlari callback fonksiyonu
         */   
        public function callback_render_before() {
        $options = get_option( 'like_it_options' );
        $checked = "";
        if (isset($options["show_like_it_before"]) && $options["show_like_it_before"] == true) {
            $checked = " checked";
        }   
            echo '<input type="checkbox" id="show_like_it_before" name="like_it_options[show_like_it_before]" value="true" '.$checked.'/></input>';
             
        }
        public function callback_render_after() {
            $options = get_option( 'like_it_options' );
            $checked = "";
            if (isset($options["show_like_it_after"]) && $options["show_like_it_after"] == true) {
                $checked = " checked";
            }
            echo '<input type="checkbox" id="show_like_it_after" name="like_it_options[show_like_it_after]" value="true" '.$checked.'/></input>';
        }
 
        

        /**
         * Options page form submit
         * Se�enek sayfasi form kayit
         */   
        public function like_it_options_page() {
            if(!current_user_can('manage_options'))
            {
                    wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            // Render the settings template
            include(sprintf("%s/inc/settings.php", dirname(__FILE__)));

        }
        
        /**
         * Shortcode generate for like buton
         * Bu fonksiyon like butonu icin shortcode uretir
         */ 
        public function shortcode_like_it($atts, $content = null) {
  
            $id_post = get_the_ID();
            $like_hide = get_post_meta( $id_post, 'like_it_hide', true );
            if(isset($like_hide) && $like_hide == 1) {
                return;
            }
            $like    = get_post_meta( $id_post, 'like_it_like', true );

            if(empty($like) || !is_numeric($like)) {
                $like = 0;
            }
            $like_user_id = get_post_meta( $id_post, 'like_user_id', true );

            $like_user_id = unserialize($like_user_id);
            $class = '';
            if(!empty($like_user_id)) {
                if ( is_user_logged_in() ) {
                    // Current user is logged in,
                    // so let's get current user info
                    $current_user = wp_get_current_user();
                    // User ID
                    $user_id = $current_user->ID;
                }else{
                    $user_id = NULL;
                }
                if(in_array($user_id, $like_user_id)) {
                    $class = ' liked';
                }
            }
            if ( is_user_logged_in() ){
              return '<div class="like'.$class.'"><i id="post_'.$id_post.'" onclick="myFunction(this)" class="fa fa-thumbs-up LikeCheck"></i><span for="post_'.$id_post.'" class="LoveCount">'.$like.'</span></div><!--/like-->';                
                
            }else{
              return '<div class="like'.$class.'">Total <i for="post_'.$id_post.'" class="fa fa-heart"></i>'.$like.'</div><!--/like-->';
                
            } 

        }
        /**
         * Shortcode generate for lists of posts(Last 10 posts) with likes numbers
         * Bu fonksiyon son 10 yazinin begenileri ile birlikte shortcodeunu uretir
         */
        public function shortcode_like_it_posts(){
          $args = array('posts_per_page' => 10);
          $posts = get_posts($args);
          $like_hide = array();
          $like = array();
          $alldata = array();
            for($i =0; $i<count($posts); $i++){
                $like_hide[$i] = get_post_meta( $posts[$i]->ID, 'like_it_hide', true ); 
                  if(isset($like_hide[$i]) && $like_hide[$i] == 1) {
                    return;
                }
                $like[$i]    = get_post_meta( $posts[$i]->ID, 'like_it_like', true );

                if(empty($like[$i]) || !is_numeric($like[$i])) {
                    $like[$i] = 0;
                }
                $alldata[] = '<label for="post_'.$posts[$i]->ID.'">-'.$posts[$i]->post_title.'</label>(Total <span class="LoveCount">'.$like[$i].'</span><i for="post_'.$posts[$i]->ID.'"class="fa fa-heart"></i> )<!--/like-->';

                
              }
              $alldatastring = implode('</br>',$alldata);
            
              return $alldatastring;

        }
   
        /**
         * Shortcode generate for lists of tags(Last 10 tags) with total likes numbers
         * Bu fonksiyon son 10 etiketin toplam begenileri ile birlikte shortcodeunu uretir
         */    
        public function shortcode_like_it_tags(){
         $args = array('posts_per_page' => 10);
         $posttags = get_tags($args);
         $like_hide = array();
         $like = array();
         $alldata = array();
         $postspecific = array();
          if ($posttags) {
              foreach($posttags as $tag) {
                $the_query = new WP_Query( 'tag_id='.$tag->term_id );
                if ( $the_query->have_posts() ) {
                    $alldata['name'][] = '<a href="'.get_tag_link($tag->term_id).'">'.$tag->name.'</a>';
                    while ( $the_query->have_posts() ) {
                        $the_query->the_post();
                        $like_hide[$i] = get_post_meta( get_the_ID(), 'like_it_hide', true ); 
                          if(isset($like_hide[$i]) && $like_hide[$i] == 1) {
                            return;
                        }
                        $like[$i]    = get_post_meta( get_the_ID(), 'like_it_like', true );

                        if(empty($like[$i]) || !is_numeric($like[$i])) {
                            $like[$i] = 0;
                        }
                        $alldata['likes'][$tag->term_id] .= $like[$i];
                    }
                } else {
                    
                }
                /* Restore original Post Data */
                wp_reset_postdata();   
              }
            }  
            $numbers = [];

            $numericvalues = implode(',',$alldata['likes']);

            $explodedvalues = explode(',', $numericvalues);

            $i=0; foreach($explodedvalues as $explode){

                $numbers[$i] = str_split($explode);

                $alldata['likesnumber']['numbers'][] = array_sum($numbers[$i]);

            $i++;}
                        
            function map_Spanish($n, $m)
                    
                {
                return('-<span>'.$n.'( Total '.$m.' <i class="fa fa-heart"></i>)</span>');
                }    
                
            arsort($alldata['likesnumber']['numbers']);
            
            $dataend = [];
            
            $dataend['numbers'] = $alldata['likesnumber']['numbers'];
            
            $dataend['numbers']['names'] = $alldata['name'];
            
            arsort($dataend['numbers']['names']);

            $c = array_map("map_Spanish", $dataend['numbers']['names'], $alldata['likesnumber']['numbers']);


            $endvalue = implode('<br>',$c);
            
            return $endvalue;

        }
        /**
         * Ajax function to save likes/dislikes (according to user log-in)
         * Like ve dislikleri kaydeden ajax fonksiyonu(kullanici girisine gore)
         */ 
        public function ajax() {
            if ( !is_user_logged_in() )         
                die ( 'Run Away !');

            $nonce = $_POST['nonce'];

            if ( ! wp_verify_nonce( $nonce, 'like-it' ) )
                die ( 'Run Away !');


            $id_post = $_POST['post'];

            $id_post = str_replace('post_', '', $id_post);


            $like    = get_post_meta( $id_post, 'like_it_like', true );

            if(empty($like) || !is_numeric($like)) {
                $like = 0;
                add_post_meta($id_post, 'like_it_like', $like, true);
            }

            $like_user_id = get_post_meta( $id_post, 'like_user_id', true );
            if(empty($like_user_id)) {
                add_post_meta($id_post, 'like_user_id', '', true);
            }
            if ( is_user_logged_in() ) {
                // Current user is logged in,
                // so let's get current user info
                $current_user = wp_get_current_user();
                // User ID
                $user_id = $current_user->ID;
            }else{
                $user_id = NULL;
            }

            $message = array();

            if(empty($like_user_id)) {

                $like_user_id = array($user_id);

                $like = $like + 1;
                update_post_meta($id_post, 'like_it_like', $like);

                update_post_meta($id_post, 'like_user_id', serialize($like_user_id));
            } else {
                $like_user_id = unserialize($like_user_id);
                if(in_array($user_id, $like_user_id)) {

                    if (($key = array_search($user_id, $like_user_id)) !== false) {
                        unset($like_user_id[$key]);
                    }
                    $like = $like - 1;
                    update_post_meta($id_post, 'like_it_like', $like);
                    update_post_meta($id_post, 'like_user_id', serialize($like_user_id));
                } else {
                    $like_user_id[] = $user_id;

                    $like = $like + 1;
                    update_post_meta($id_post, 'like_it_like', $like);
                    update_post_meta($id_post, 'like_user_id', serialize($like_user_id));
                }
            }
            $message['likes'] = $like;

            echo json_encode($message);
            die();

        }            
        /**
         * In this way, the content setting on the setting page comes to the desired location
         * Bu sayede ayar sayfas�nda yap�lan icerik ayari istenilen yere gelir
         */        
        public function like_it_filter_the_content($content) {

            if ( 'post' != get_post_type()) return $content;
            
            $options = get_option( 'like_it_options' );
            if (isset($options["show_like_it_before"]) && $options["show_like_it_before"] == true) {
                $content = do_shortcode('[like_it]') . $content;
            }
            if (isset($options["show_like_it_after"]) && $options["show_like_it_after"] == true) {
                $content = $content . do_shortcode('[like_it]');
            }

            return $content;

        }


    }
endif;

