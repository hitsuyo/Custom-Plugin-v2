<?php
/*
 * Add my new menu to the Admin Control Panel
 */

// Example
/*
function wp_first_shortcode(){
echo "Hello, This is your another shortcode!";
}
add_shortcode('first', 'wp_first_shortcode');

function form_creation(){
  ?>
  <form>
    First name: <input type="text" name="firstname"><br>
    Last name: <input type="text" name="lastname"><br>
   	Message: <textarea name="message"> Enter text here...</textarea>
 	</form>
 	<?php
 }
 add_shortcode('test', 'form_creation');
 */
 //Example - end

/* ---------------------------------------------------------------------------------------------------------------------*/
// Hook the 'admin_menu' action hook, run the function named 'mdp_Add_My_Admin_Link()'
    add_action( 'admin_menu', 'mcfp_dn_Add_My_Admin_Link' );
 
// Add a new top level menu link to the ACP
function mcfp_dn_Add_My_Admin_Link()
{
  // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null );
  add_menu_page(
        'My First Page', // Title of the page
        'Mytheme Contact Form Plugin - Donut', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'includes/mcfp-dn-first-acp-page.php' // The 'slug' - file to display when clicking the link
    );
  // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' ); // demo
}


// has_cap was called with an argument… since version 2.0.0! ... --> set $cabablity = "manage_options" --> done

function mcfp_dn_Add_My_Admin_Actions()
{
	//Add to Settings menu

	     // add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' );
	// add_options_page("Mytheme Contact Form Plugin Options", "Mytheme Contact Form Plugin", "manage_options", "Mytheme Contact Form Plugin", "mcptp_plugin_options");


       // or/and Add to Tools menu
  // add_management_page("Mytheme CPT Plugin Options", "Mytheme CPT Plugin", "manage_options", "Mytheme CPT Plugin", "mcptp_plugin_options");


}
// add_action('admin_menu', 'mcfp_Add_My_Admin_Actions');

function mcfp_dn_plugin_options() {
  // User Interface

}

$response = "";

//function to generate response
function my_contact_form_generate_response($type, $reminder){

    global $response;

    if($type == "success") $response = "<div class='success'>{".$reminder."}</div>";
    else $response = "<div class='error'>{".$reminder."}</div>";

}



function html_form_code_dn() {
?>
<div id="respond">
<!--   <php
    echo $response; 
  ?> -->
  <form action="<?php the_permalink(); ?>" method="post">
    <p><label id="name-label" for="cf-name">Name: <span>*</span> <br><input type="text" name="cf-name" ></label></p>
    <p><label id="email-label" for="cf-email">Email: <span>*</span> <br><input type="text" name="cf-email" ></label></p>
    <p><label id="message-label" for="cf-message">Message: <span>*</span> <br><textarea type="text" name="cf-message"></textarea></label></p>
    <!-- <p><label for="message_human">Human Verification: <span>*</span> <br><input type="text" style="width: 60px;" name="message_human"> + 3 = 5</label></p> -->
    <input type="hidden" name="cf-submitted" value="1">
    <p><input type="submit"></p>
  </form>
</div>

<style>
  #respond {width: 60%; margin: 0 auto;}
  #name-label, #email-label, #message-label {font-size: 1.5rem;}
</style>
<?php

  validate_deliver_mail_dn();

}


function validate_deliver_mail_dn()
{
    //response messages
    $not_human       = "Human verification incorrect.";
    $missing_content = "Please supply all information.";
    $email_invalid   = "Email Address Invalid.";
    // $message_unsent  = "Message was not sent. Try Again.";
    // $message_sent    = "Thanks! Your message has been sent."; 
    // $message_sent    = "Thanks for contacting me, expect a response soon.";
     
    //user posted variables

    // $human = $_POST['message_human'];

      // sanitize form values
    if(!isset($_POST['cf-name']) || !isset($_POST['cf-email'])) {}
    else
    {
      if(isset($_POST['cf-name']) && isset($_POST['cf-email'])) { 
        $name    = sanitize_text_field( $_POST['cf-name'] );
        $email    = sanitize_text_field( $_POST['cf-email'] ); 
        // $headers = "From: $name <$email>" . "\r\n";
        $headers = 'From: '.$name.' <'.$email.'> \r\n';
      }
    }

    if(!isset($_POST['cf-subject'])) {}
    else
    {
      if(isset($_POST['cf-subject'])) { $subject    = sanitize_text_field( $_POST['cf-subject'] ); }
    }

    if(!isset($_POST['cf-message'])) {}
    else
    {
      if(isset($_POST['cf-message'])) { $message    = sanitize_text_field( $_POST['cf-message'] ); }
    }

    // get the blog administrator's email address
    $to = get_option( 'admin_email' ); // good with Yahoo mail

    $subject = "Someone sent a message from ".get_bloginfo('name');

  // if(isset( $_POST['cf-submitted'])){

    if(!isset($_POST['cf-email'])) {}
    else
    {
      if(isset($_POST['cf-email'])) { 
          $email    = sanitize_text_field( $_POST['cf-email'] );
          //validate email
          if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            my_contact_form_generate_response("error", $email_invalid);
          else //email is valid
          {
            //validate presence of name and message
              if(empty($name) || empty($message)){
                  my_contact_form_generate_response("error", $missing_content);
                  echo '<p style="margin: 0 auto; width: 60%;">lack of input</p>';
              }
              else
              {            
                  //send email
                  $all_content = 'Sent from: ['.$name.'] < '.$email.' >'."\n\n";
                  $all_content .= 'Message: '."\n".$message."\n\n";
                  $all_content .= '---'."\n";
                  $all_content .= 'Contact Form of website: '.get_bloginfo('url');
                  // $all_content .= "";
                     // If email has been process for sending, display a success message
                  // if ( wp_mail( $to, $subject, $message, $headers ) ) {
                  if ( wp_mail( $to, $subject, $all_content, $headers ) ) {
                      echo '<div>';
                      echo '<p id="thank">Thanks for contacting me, expect a response soon.</p>';
                      echo '</div>';

                      echo '<style>';
                      echo 'p#thank{margin: 0 auto; width: 60%;}';
                      echo '</style>';

                  } else {
                      echo '<div class="container">';
                      echo 'An unexpected error occurred';
                      echo '</div>';
                  }
              }   
          }
      }
    }
          
  // }
}


function cf_shortcode_dn() {
    ob_start();
    // validate_dn();
    html_form_code_dn();

    return ob_get_clean();
}
add_shortcode( 'sitepoint_contact_form_dn', 'cf_shortcode_dn' );




// [sitepoint_contact_form_dn]

 // Reduce the likelihood of  your messages being flagged as spam.
// add_action( 'init', 'fix_my_email_return_path' );
// function fix_my_email_return_path( $phpmailer ) {
//     $phpmailer->Sender = $phpmailer->From;
// }


// ----------------------------------

