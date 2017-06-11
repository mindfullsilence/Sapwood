<?php


namespace Sapwood\Library;

 function sapwood_add_field_group($fieldgroup = array()) {
   add_action('acf/init', function() use ($fieldgroup) {
     acf_add_local_field_group($fieldgroup);
   });
 }

 function sapwood_add_field($field = array(), $group = '') {
   if(!empty($group) && !isset($field['parent'])) {
     $field['parent'] = $group;
   }

   add_action('acf/init', function() use ($field) {
     acf_add_local_field($field);
   });
 }
