<?php
$I = new FunctionalTester($scenario);
$I->wantTo('perform actions and see result');

// Login to wp-admin
$I->loginAsAdmin();

// Navigate to the Media Library
$I->amOnPage( '/wp-admin/post-new.php' );
