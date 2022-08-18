<?php

namespace Vigilant\Notification;

abstract class Base
{
	/**
	 * @var array $config Notification config
	  */
	protected array $config = [];

	/**
	 * Set notification config
	 * 
	 * @param array $config
	 */
	public function config(array $config): void
	{
		$this->config = $config;
	}

	/**
	 * Send notification
	 */
	abstract public function send();
}
