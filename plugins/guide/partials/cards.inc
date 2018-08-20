<?php
$card = new UUP\Web\Component\Container\Card();

$card->title = "Title";
$card->text = "Use cards for displaying an image with some text and an action button";
$card->image = "../assets/img/nature-bird-flying-red.jpeg";

$card->button->event->onclick = "alert('You just clicked me!')";
$card->button->props->hover->back = "teal";
$card->button->text = "Click";

?>

<div class="w3-row">
    <div class="w3-third"><?php $card->render() ?></div>
    <div class="w3-third"><?php $card->render() ?></div>
    <div class="w3-third"><?php $card->render() ?></div>
</div>
