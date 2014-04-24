<?php

	/**
	 * AbstractYCMBridge
	 * 
	 * @implements IYCMBridge
	 * @author Tominov Sergey <ikenfin@gmail.com>
	 * @license public domain
	 */
	abstract class AbstractYCMBridge implements IYCMBridge {

		protected $_context,
				  $_form;

		public $model,
			   $attribute;

		/**
		 * Class constructor
		 *
		 * set current context
		 */
		public function __construct($context = null) {
			if(null == $context) throw CException('Context cannot be null!');

			$this->_context = $context;
		}

		// setters

		/**
		 * set current form object
		 */
		public function setForm($form) {
			$this->_form = $form;
			return $this;
		}
		/**
		 * set current model
		 */
		public function setModel($model) {
			$this->model = $model;
			return $this;
		}
		/**
		 * set current model attribute
		 */
		public function setAttribute($attribute) {
			$this->attribute = $attribute;
			return $this;
		}
		
		// getters

		/**
		 * @return current form object
		 */
		public function getForm() {
			return $this->_form;
		}
		/**
		 * @return current context (YiiYcmModule object)
		 */
		public function getContext() {
			return $this->_context;
		}
		/**
		 * @return current model object
		 */
		public function getModel() {
			return $this->model;
		}
		/**
		 * @return string current attribute
		 */
		public function getAttribute() {
			return $this->attribute;
		}
		/**
		 * @return current controller object
		 */
		public function getController() {
			return $this->getContext()->getController();
		}


		/**
		 * widget render logic
		 */
		public function render() {
			// user code
		}

		/**
		 * render wrapper
		 */
		public function renderWidget($name) {
			$this->beforeRenderWidget($name);
			$this->render();
			$this->afterRenderWidget($name);
		}

		// triggers:
		protected function beforeRenderWidget($name) {}
		protected function afterRenderWidget($name) {}

	}