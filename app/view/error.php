<?php include_once VIEW_HEADER; ?>
<section class="section">
  <div class="notification is-danger is-light has-text-centered box">
    <button class="delete"></button>
    <h3 class="title is-3"><?= $title; ?></h3>
    <h4 class="title is-4"><?= $message; ?></h4>
    <a href="javascript:history.go(-1)" class="button is-danger is-small is-rounded is-outlined">Regresar</a>
  </div>
</section>