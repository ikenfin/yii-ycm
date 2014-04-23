# yii-ycm with bridges #

- [Original module](http://janisto.github.com/yii-ycm/)
- [Examples](http://janisto.github.com/yii-ycm/)

# Bridges #

YCM is perfect module, but i had some difficulties, when i tried make some integration with third-party widgets. (Originally all widgets provided by yii-ycm were hardcoded in main class of module)

I tried to do something with that, and as result i created "bridge api" for widgets. 

`See bridges folder.`


## Methods available in bridge class ##
`$this->getContext()` - returns yii-ycm instance
`$this->getModel()` - returns model
`$this->getAttribute()` - returns model attribute
`$this->getController()` - returns controller
`$this->getForm()` - returns form


## Bridge using examples: ##

For example we want to use [jqueryte](http://www.yiiframework.com/extension/ejqueryte) extension for our model attribute:

1) create JqueryteBridge.php in ext.jqueryte.ycm_bridge folder:
```php
<?php

    class JqueryteBridge extends AbstractYCMBridge {

		public function render() {

			$model = $this->getModel();
			$attribute = $this->getAttribute();

			// get widget options
			$options = $this->getContext()->getAttributeOptions($attribute, [
				'model' => $model,
				'attribute' => $attribute
			])

			// render widget
			$this->getController()->widget('ext.jqueryte.Jqueryte', $options);

		}

	}
```
2) Add bridge alias to module config (config/main.php):
```php
'modules' => array(
    'ycm' => array(
        ...
        'bridge_aliases' => array(
       		'ext.jqueryte.ycm_bridge',
        )
    )
)
```
3) Setup attribute in attributeWidgets method (in model) :
```php
array('node_text','jqueryte')
```

4) PROFIT!

