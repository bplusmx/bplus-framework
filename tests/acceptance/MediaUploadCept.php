<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('perform actions and see result');

// Login to wp-admin
$I->loginAsAdmin();

// Navigate to the Media Library
$I->amOnPage( '/wp-admin/media-new.php' );

$I->waitForText( 'Subir nuevo medio' );

// Add new file
$I->attachFile( 'input[type="file"]', 'hem.jpg' );

// Wait for upload
$I->waitForElement( '.edit-attachment', 20 );
$I->seeElement( '.edit-attachment' );
$I->click( '.edit-attachment' );

// Navigate to the Edit Media window
$I->executeInSelenium( function ( \Facebook\WebDriver\Remote\RemoteWebDriver $webdriver ) {
    $handles     = $webdriver->getWindowHandles();
    $last_window = end( $handles );
    $webdriver->switchTo()->window( $last_window );
} );

$I->waitForText( 'Editar medios' );
