<?php
/**
 * Woocommerce_Mailchimp_Auto_Signup.
 *
 * @since   0.0.1
 * @package Woocommerce_Mailchimp_Auto_Signup
 */
class Woocommerce_Mailchimp_Auto_Signup_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.1
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'Woocommerce_Mailchimp_Auto_Signup') );
	}

	/**
	 * Test that our main helper function is an instance of our class.
	 *
	 * @since  0.0.1
	 */
	function test_get_instance() {
		$this->assertInstanceOf(  'Woocommerce_Mailchimp_Auto_Signup', woocommerce_mailchimp_auto_signup() );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  0.0.1
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
