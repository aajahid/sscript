<?php
 
class Form
{
   var $values = array();  //Holds submitted form field values
   var $errors = array();  //Holds submitted form error messages
   var $num_errors;   //The number of errors in submitted form

   /* Class constructor */
   function Form(){
      /**
       * Get form value and error arrays, used when there
       * is an error with a user-submitted form.
       */
      if(isset($_SESSION['value_array']) && isset($_SESSION['error_array'])){
         $this->values = $_SESSION['value_array'];
         $this->errors = $_SESSION['error_array'];
         $this->num_errors = count($this->errors);

         unset($_SESSION['value_array']);
         unset($_SESSION['error_array']);
      }
      else{
         $this->num_errors = 0;
      }
   }

   /**
    * setValue - Records the value typed into the given
    * form field by the user.
    */
   function setValue($field, $value){
      $this->values[$field] = $value;
   }

   /**
    * setError - Records new form error given the form
    * field name and the error message attached to it.
    */
   function setError($field, $errmsg){
      $this->errors[$field] = $errmsg;
      $this->num_errors = count($this->errors);
   }

   /**
    * value - Returns the value attached to the given
    * field, if none exists, the empty string is returned.
    */
   function value($field){
      if(array_key_exists($field,$this->values)){
         return stripslashes($this->values[$field]);
      }else{
         return "";
      }
   }

   /**
    * error - Returns the error message attached to the
    * given field, if none exists, the empty string is returned.
    */
   function error($field, $class = 'error'){
      if(array_key_exists($field,$this->errors)){
         return '<div class="'.$class.'">'.$this->errors[$field].'</div>';
      }else{
         return "";
      }
   }



    function status($field){
        if(array_key_exists($field,$this->errors))
        {
            return 'error';
        }
        else
        {
            return "";
        }
    }




   /* getErrorArray - Returns the array of error messages */
   function getErrorArray(){
      return $this->errors;
   }
   
   function return_msg_to($location)
    {
        $_SESSION['error_array'] = $this->getErrorArray();
        $_SESSION['value_array'] = $_POST;
        redirect( $location );
    }
   
};
 
?>
