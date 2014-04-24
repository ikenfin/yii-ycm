<?php

/**
 * YcmModule
 * 
 * @uses CWebModule
 * @version 1.1.0
 * @copyright 2012-2013
 * @author Jani Mikkonen <janisto@php.net>
 * @license public domain
 */
class YcmModule extends CWebModule
{
	private $controller;
	private $_assetsUrl;
	private $_modelsList=array();

	protected $registerModels=array();
	protected $excludeModels=array();
	protected $attributesWidgets;
	public $username;
	public $password;
	public $uploadPath;
	public $uploadUrl;
	public $uploadCreate=false;
	public $redactorUpload=false;
	public $permissions=0774;
	public $analytics=array();

	public $bridge_interface = 'IYCMBridge';
	public $bridge_aliases = array();

	/**
	 * Load model.
	 *
	 * @param string $name Model name
	 * @param null|int $pk Primary key
	 * @throws CHttpException
	 * @return object Model
	 */
	public function loadModel($name,$pk=null)
	{
		$name=(string)$name;
		$model=new $name;
		if ($pk!==null) {
			$model=$model->findByPk((int)$pk);
			if ($model===null) {
				throw new CHttpException(500,Yii::t(
					'YcmModule.ycm',
					'Could not load model "{name}".',
					array('{name}'=>$name)
				));
			}
		}
		$model->attachBehavior('admin',array('class'=>$this->name.'.behaviors.FileBehavior'));
		return $model;
	}

	/**
	 * Init module.
	 */
	public function init()
	{
		// user can add bridge to custom widgets in config
		// here we connect default bridges:
		$this->bridge_aliases[] = $this->name.'.bridges';
	
		if ($this->uploadPath===null) {
			$path=Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.'uploads';
			$this->uploadPath=realpath($path);
			if ($this->uploadPath===false && $this->uploadCreate===true) {
				if (!mkdir($path,$this->permissions,true)) {
					throw new CHttpException(500,Yii::t(
						'YcmModule.ycm',
						'Could not create upload folder "{dir}".',
						array('{dir}'=>$path)
					));
				}
			}
		}
		if ($this->uploadUrl===null) {
			$this->uploadUrl=Yii::app()->request->baseUrl .'/uploads';
		}

		$this->setImport(array(
			$this->name.'.models.*',
			$this->name.'.components.*',
		));

		$this->configure(array(
			'preload'=>array('bootstrap'),
			'components'=>array(
				'bootstrap'=>array(
					'class'=>$this->name.'.extensions.bootstrap.components.Bootstrap',
					'responsiveCss'=>true,
				),
			),
		));
		$this->preloadComponents();

		Yii::app()->setComponents(array(
			'errorHandler'=>array(
				'errorAction'=>$this->name.'/default/error',
			),
			'user'=>array(
				'class'=>'CWebUser',
				'allowAutoLogin'=>true,
				'stateKeyPrefix'=>$this->name,
				'loginUrl'=>Yii::app()->createUrl($this->name.'/default/login'),
			),
		), true);
	}

	/**
	 * Get current controller
	 *
	 * @return controller object
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * Get a list of all models.
	 *
	 * @return array Model names
	 */
	public function getModelsList()
	{
		$models=$this->registerModels;

		if (!empty($models)) {
			foreach ($models as $model) {
				Yii::import($model);
				if (substr($model, -1)=='*') {
					// Get a list of all models inside a directory. Example: 'application.models.*'
					$files=CFileHelper::findFiles(Yii::getPathOfAlias($model),array('fileTypes'=>array('php')));
					if ($files) {
						foreach ($files as $file) {
							$modelName=str_replace('.php','',substr(strrchr($file,DIRECTORY_SEPARATOR),1));
							$this->addModel($modelName);
						}
					}
				} else {
					$modelName=substr(strrchr($model, "."),1);
					$this->addModel($modelName);
				}
			}
		}

		return array_unique($this->_modelsList);
	}

	/**
	 * Add to the list of models.
	 *
	 * @param string $model Model name
	 */
	protected function addModel($model)
	{
		$model=(string)$model;
		if (!in_array($model,$this->excludeModels)) {
			$this->_modelsList[]=$model;
		}
	}

	/**
	 * Find and init bridge by name
	 * @throws CException
	 * @return Bridge object
	 */
	protected function findBridge($name) {
		$name .= 'Bridge';

		if($this->bridge_aliases !== null) {
			if(is_array($this->bridge_aliases)) {
				foreach($this->bridge_aliases as $alias) {
					// ex: application.bridges.GridBridge
					$alias .= '.'.$name;
					
					if(is_file(Yii::getPathOfAlias($alias).'.php')) {
						Yii::import($alias);
						// we need only classes that implements IYCMBridge...
						if(class_implements($name, $this->bridge_interface)) {
							return new $name($this);
						}
					}
				}
				throw new CException('Bridge '.$name.' not found!');
			}
			else throw new CException('bridge_aliases must be an array!');
		}
	}


	/**
	 * Create TbActiveForm widget.
	 *
	 * @param TbActiveForm $form
	 * @param object $model Model
	 * @param string $attribute Model attribute
	 */
	public function createWidget($form,$model,$attribute)
	{
		$lang=Yii::app()->language;
		if ($lang=='en_us') {
			$lang='en';
		}

		$widget_name = $this->getAttributeWidget($model,$attribute);
		
		$bridge = $this->findBridge(ucfirst($widget_name));

		$bridge->setForm($form)
			   ->setModel($model)
			   ->setAttribute($attribute);

		if($bridge !== null) {
			$bridge->renderWidget($widget_name);
		}

		return;
	}

	/**
	 * Get attribute file path.
	 *
	 * @param string $name Model name
	 * @param string $attribute Model attribute
	 * @return string Model attribute file path
	 */
	public function getAttributePath($name,$attribute)
	{
		return $this->uploadPath.DIRECTORY_SEPARATOR.strtolower($name).DIRECTORY_SEPARATOR.strtolower($attribute);
	}

	/**
	 * Get attribute file URL.
	 *
	 * @param string $name Model name
	 * @param string $attribute Model attribute
	 * @param string $file Filename
	 * @return string Model attribute file URL
	 */
	public function getAttributeUrl($name,$attribute,$file)
	{
		return $this->uploadUrl.'/'.strtolower($name).'/'.strtolower($attribute).'/'.$file;
	}

	/**
	 * Get attributes widget.
	 *
	 * @param object $model Model
	 * @param string $attribute Model attribute
	 * @return null|object
	 */
	public function getAttributeWidget($model,$attribute)
	{
		if ($this->attributesWidgets!==null) {
			if (isset($this->attributesWidgets->$attribute)) {
				return $this->attributesWidgets->$attribute;
			} else {
				$dbType=$model->tableSchema->columns[$attribute]->dbType;
				if ($dbType=='text') {
					return 'wysiwyg';
				} else {
					return 'textField';
				}
			}
		}

		$attributeWidgets=array();
		if (method_exists($model,'attributeWidgets')) {
			$attributeWidgets=$model->attributeWidgets();
		}

		$data=array();
		if (!empty($attributeWidgets)) {
			foreach ($attributeWidgets as $item) {
				if (isset($item[0]) && isset($item[1])) {
					$data[$item[0]]=$item[1];
					$data[$item[0].'Options']=$item;
				}
			}
		}

		$this->attributesWidgets=(object)$data;

		return $this->getAttributeWidget($model,$attribute);
	}

	/**
	 * Get an array of attribute choice values.
	 * The variable or method name needs ​​to be: attributeChoices.
	 *
	 * @param object $model Model
	 * @param string $attribute Model attribute
	 * @return array
	 */
	public function getAttributeChoices($model,$attribute)
	{
		$data=array();
		$choicesName=(string)$attribute.'Choices';
		if (method_exists($model,$choicesName) && is_array($model->$choicesName())) {
			$data=$model->$choicesName();
		} else if (isset($model->$choicesName) && is_array($model->$choicesName)) {
			$data=$model->$choicesName;
		}
		return $data;
	}

	/**
	 * Get attribute options.
	 *
	 * @param string $attribute Model attribute
	 * @param array $options Model attribute form options
	 * @param bool $recursive Merge option arrays recursively
	 * @return array
	 */
	public function getAttributeOptions($attribute,$options=array(),$recursive=false)
	{
		$optionsName=(string)$attribute.'Options';
		if (isset($this->attributesWidgets->$optionsName)) {
			$attributeOptions=array_slice($this->attributesWidgets->$optionsName,2);
			if (empty($options)) {
				return $attributeOptions;
			} else {
				if (empty($attributeOptions)) {
					return $options;
				} else {
					if ($recursive===true) {
						return array_merge_recursive($options,$attributeOptions);
					} else {
						return array_merge($options,$attributeOptions);
					}
				}
			}
		} else {
			if (empty($options)) {
				return array();
			} else {
				return $options;
			}
		}
	}

	/**
	 * Get model's administrative name.
	 *
	 * @param mixed $model
	 * @return string
	 */
	public function getAdminName($model)
	{
		if (is_string($model)) {
			$model=new $model;
		}
		if (!isset($model->adminNames)) {
			return get_class($model);
		} else {
			return $model->adminNames[0];
		}
	}

	/**
	 * Get model's singular name.
	 *
	 * @param mixed $model
	 * @return string
	 */
	public function getSingularName($model)
	{
		if (is_string($model)) {
			$model=new $model;
		}
		if (!isset($model->adminNames)) {
			return strtolower(get_class($model));
		} else {
			return $model->adminNames[1];
		}
	}

	/**
	 * Get model's plural name.
	 *
	 * @param mixed $model
	 * @return string
	 */
	public function getPluralName($model)
	{
		if (is_string($model)) {
			$model=new $model;
		}
		if (!isset($model->adminNames)) {
			return strtolower(get_class($model));
		} else {
			return $model->adminNames[2];
		}
	}

	/**
	 * Download Excel?
	 *
	 * @param mixed $model
	 * @return bool
	 */
	public function getDownloadExcel($model)
	{
		if (is_string($model)) {
			$model=new $model;
		}
		if (isset($model->downloadExcel)) {
			return $model->downloadExcel;
		} else {
			return false;
		}
	}

	/**
	 * Download MS CSV?
	 *
	 * @param mixed $model
	 * @return bool
	 */
	public function getDownloadMsCsv($model)
	{
		if (is_string($model)) {
			$model=new $model;
		}
		if (isset($model->downloadMsCsv)) {
			return $model->downloadMsCsv;
		} else {
			return false;
		}
	}

	/**
	 * Download CSV?
	 *
	 * @param mixed $model
	 * @return bool
	 */
	public function getDownloadCsv($model)
	{
		if (is_string($model)) {
			$model=new $model;
		}
		if (isset($model->downloadCsv)) {
			return $model->downloadCsv;
		} else {
			return false;
		}
	}

	/**
	 * @return string the base URL that contains all published asset files of the module.
	 */
	public function getAssetsUrl()
	{
		if ($this->_assetsUrl===null) {
			$this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias($this->name.'.assets'));
		}
		return $this->_assetsUrl;
	}

	/**
	 * @param string $value the base URL that contains all published asset files of the module.
	 */
	public function setAssetsUrl($value)
	{
		$this->_assetsUrl=$value;
	}

	/**
	 * @param CController $controller
	 * @param CAction $action
	 * @return bool
	 */
	public function beforeControllerAction($controller,$action)
	{
		if (parent::beforeControllerAction($controller,$action)) {
			// this method is called before any module controller action is performed
			$this->controller=$controller;
			$route=$controller->id.'/'.$action->id;
			$publicPages=array(
				'default/login',
				'default/error',
			);
			if ($this->password!==false && Yii::app()->user->isGuest && !in_array($route,$publicPages)) {
				Yii::app()->user->loginRequired();
			} else {
				return true;
			}
		}
		return false;
	}
}