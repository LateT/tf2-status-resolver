<?php
session_start();
?>
<html>
<link rel="stylesheet" href="tyyli.css">
<div id="props">bad PHP ripoff of <a href="http://tf2-status.000webhostapp.com/">maza's tf2 status resolver</a>
<div id="inputform">
<form action="parse.php" method="POST">
  <textarea rows="30" cols="150" name="status" placeholder="Paste status here"></textarea>
<br><input type="submit">
</form>
</div>
