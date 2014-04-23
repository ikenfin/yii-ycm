# yii-ycm with bridges #

- [Original module](http://janisto.github.com/yii-ycm/)
- [Examples](http://janisto.github.com/yii-ycm/)

# Bridges #

YCM is perfect module, but i had some difficulties, when i tried make some integration with third-party widgets. (Originally all widgets provided by yii-ycm were hardcoded in main class of module)

I tried to do something with that, and as result i created "bridge api" for widgets. 

`See bridges folder.`

## Bridge using examples: ##

For example we want to use [jqueryte](http://www.yiiframework.com/extension/ejqueryte) extension for our model attribute:

1) create JqueryBridge.php in ext.jqueryte.ycm_bridge folder:
```php
<?php

    class JqueryteBridge extends AbstractYCMBridge {

		public function render() {

			$model = $this->getModel();
			$attribute = $this->getAttribute();

			$this->getController()->widget('application.widgets.jqueryte.Jqueryte', [
				'model' => $model,
				'attribute' => $attribute
			]);

		}

	}
```
2) Add bridge alias to module config (config/main.php):
```php
'modules' => array(
    'ycm' => array(
        ...
        'bridge_aliases' => array(
       		'application.widgets.jqueryte.ycm_bridge',
        )
    )
)
```
3) Setup attribute in attributeWidgets method (in model) :
```php
array('node_text','jqueryte')
```

4) PROFIT!