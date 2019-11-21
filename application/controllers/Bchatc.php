<?php

  class Bchatc extends CI_Controller{

    //CONSTRUCTURE CODE START
    public function __construct(){
      parent::__construct();

      $this->load->model('brequest_model');
      $this->load->model('buyer_model');

      $this->load->library('form_validation');

      //prevent user if not logged in into the system
      if($this->session->userdata('logged_in') != true ){
        redirect(base_url(), '');
      }

      //prevent freelancer to access the buyer profile pages
      if($this->session->userdata('user_type') == 'freelancer'){
        redirect(base_url() . "profilec/view_profile");
      }
    }
    //CONSTRUCTURE CODE END

    //OPEN CHAT CODE START
    function chat($recipient_id = null){
      $recipient_id = $this->uri->segment(3);
      $row['recipient_id'] = $recipient_id;
      $this->load->view('buyer/includes/buyer_VPheader');
      $this->load->view('buyer/includes/buyer_serviceBar');
      $this->load->view('buyer/bchat', $row);
      $this->load->view('buyer/includes/buyer_footer');
      $this->load->view('buyer/includes/buyer_end');
    }
    //OPEN CHAT CODE END

    //SEND MESSAGE CODE START
    function sendMessage(){
      //echo '<embed loop="false" src="' . base_url('sentmessage.mp3') .'" hidden="false" autoplay="true" >';
      $msg['sender_id']    = $this->session->userdata('buyer_id');
      $msg['recipient_id'] = $this->input->post('recipient_id');
      $msg['message']      = $this->input->post('message1');
      $msg['status']      = "unread";
      $this->db->insert('messages', $msg);
      redirect(base_url(). "bchatc/chat/" . $msg['recipient_id']);
    }
    //SEND MESSAGE CODE END

    //VIEW MESSAGES USING AJAX CODE START
    function view_messages($recipient_id = null){
      if($recipient_id){
        $usql = 'UPDATE messages SET status = "read"
        WHERE sender_id =' . $recipient_id .
        ' AND recipient_id =' . $this->session->userdata('buyer_id');
        $this->db->query($usql);
        $messagesql = 'SELECT * FROM messages
        WHERE sender_id =' . $this->session->userdata('buyer_id') .
        ' AND recipient_id =' . $recipient_id .
        ' OR sender_id =' . $recipient_id .
        ' AND recipient_id =' . $this->session->userdata('buyer_id') .
        ' ORDER BY date ASC' ;
        $messagedata = $this->db->query($messagesql)->result_array();
        if($messagedata){
          foreach($messagedata as $row){
                //This User Messages CODE START
                if($this->session->userdata('buyer_id') == $row['sender_id']){
                  $message      = $row['message'];
                  $date         = $row['date'];
                  $upsql = 'SELECT p.picture, u.username FROM userprofile as p, users as u WHERE u.user_id =' . $row['sender_id'] . ' AND p.user_id=' . $row['sender_id'];
                  $updata = $this->db->query($upsql)->result_array();
                  if($updata){
                    foreach($updata as $row1){
                      $ppicture			= $row1['picture'];
                      $uusername     = $row1['username'];
                  echo  '<li>
                          <div class="rightside-left-chat">
                            <span><small class="float-right">'. $date .'</small> </span>
                            <span><b class = "text-success">Me </b></span>
                            <p>'. $message .'</p>
                          </div>
                        </li>';
                  }
                }
              }
                //This User Messages CODE END

                //OTHER User Messages CODE START
                elseif($recipient_id == $row['sender_id']){
                  $message      = $row['message'];
                  $date         = $row['date'];
                  $upsql = 'SELECT p.picture, u.username FROM userprofile as p, users as u WHERE u.user_id =' . $row['sender_id'] . ' AND p.user_id=' . $row['sender_id'];
                  $updata = $this->db->query($upsql)->result_array();
                  if($updata){
                    foreach($updata as $row1){
                      $ppicture			= $row1['picture'];
                      $uusername     = $row1['username'];
                      echo '<li>
                              <div class="rightside-right-chat">
                                <span> <small>'. $date .'</small></i></span>
                                <span><b class = "text-info float-right">'. $uusername .'</b></span>
                                <p>'. $message . '</p>
                              </div>
                            </li>';
                      }
                    }
                  }
                //OTHER User Messages CODE END
              }
          }
        }
      }
    //VIEW MESSAGES USING AJAX CODE END
}
?>
