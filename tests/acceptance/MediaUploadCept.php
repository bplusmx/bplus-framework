<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('perform actions and see result');

// Login to wp-admin
$I->loginAsAdmin();

// Navigate to the Media Library
$I->amOnPage( '/wp-admin/media-new.php' );
