<?php

	/**
	 * IYCMBridge interface
	 *
	 * define required methods
	 */
	interface IYCMBridge {

		public function render();
		public function renderWidget($name);

	}