<?php
$I = new WebGuy($scenario);
$I->wantTo('ensure homepage work');
$I->amOnPage('/');
$I->see('Home page', 'title');
