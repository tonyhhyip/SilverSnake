<?php
if (!define("HttpSession")) {
  define("HttpSession", 1);

  interface HttpSession {
      public function getAttribute($name);
      public function getAttributeNames();
      public function getCreationTime();
      public function getId();
      public function getMaxInactiveInterval();
      public function isNew();
      public function removeAttribute($name);
      public function setAttribute($name, $value);
      public function setMaxInactiveInterval($interval);
  }
}
?>