<?php

use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;


use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext
{

	private $wp_users;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
	 *
	 * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct($user = 'admin', $password = 'password')
    {
    	//var_dump($user);

    	$this->wp_users = array(
    		'user' 		=> $user,
			'password' 	=> $password
		);
    }

	/**
	 * Authenticates a user.
	 *
	 * @Given /^I am logged in as "([^"]*)" with the password "([^"]*)"$/
	 */
	public function iAmLoggedInAsWithThePassword($username, $passwd) {

		// Go to the Login page.
		 $this->getSession()->visit( 'http://ci.wordpress.dev/wp-login.php' );

		// Log in
		$element = $this->getSession()->getPage();

		//var_dump($element);

		if (empty($element)) {
			throw new Exception('Page not found');
		}

		$element->fillField('user_login', $username);

		$element->fillField('user_pass', $passwd);

		$submit = $element->findButton('wp-submit');

		if (empty($submit)) {
			throw new Exception('No submit button at ' . $this->getSession()->getCurrentUrl());
		}

		$submit->click();

		$link = $this->getSession()->getPage()->findLink("Dashboard");

		if (empty($link)) {
			throw new Exception('Login failed at ' . $this->getSession()->getCurrentUrl());
		}

		return;
	}

	/**
	 * Authenticates a user with password from configuration.
	 *
	 * @Given /^I am logged in as "([^"]*)"$/
	 */
	public function iAmLoggedInAs($username)
	{
		$this->iAmLoggedInAsWithThePassword($username, $this->wp_users['password']);
	}

	/**
	 * @Given I follow :arg1
	 *
	 * @var string $arg1
	 */
	public function iFollow($arg1 = '/wp-admin/post-new.php')
	{
		echo 'I FOLLOW';
		var_dump($arg1);

		$this->getSession()->visit( $this->locatePath($arg1) );

		$this->getSession()->getPage();
	}

	/**
	 * @Given I press :arg1
	 */
	public function iPress($arg1)
	{
		echo 'I PRESS';
		throw new PendingException();
	}

	/**
	 * @Given I fill in :arg1 with :arg2
	 */
	public function iFillInWith($arg1, $arg2)
	{
		throw new PendingException();
	}

	/**
	 * @Given I wait for the message to appear
	 */
	public function iWaitForTheMessageToAppear()
	{
		echo 'I iWaitForTheMessageToAppear';
		throw new PendingException();
	}

	/**
	 * @Then I should see :arg1
	 */
	public function iShouldSee($arg1)
	{
		echo 'I iShouldSee';
		throw new PendingException();
	}

}
