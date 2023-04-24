Yii2 Multilevel Nav hover
=========================
Yii2 Multilevel Nav hover

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist alex290/yii2-multinav "*"
```

or add

```
"alex290/yii2-multinav": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :



    echo Nav::widget([
            'items' => [
                [
                    'label' => 'Home',
                    'url' => ['site/index'],
                    'linkOptions' => [],
                ],
                [
                    'label' => 'Dropdown',
                     'items' => [
                         [
                             'label' => 'Dropdown',
                             'items' => [
                                 ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
                                 ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
                             ],
                         ],
                         ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
                         ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
                     ],
                ],
            ],
            'options' => [
                'class' =>'nav-pills', // set this to nav-tabs to get tab-styled navigation
                'backgroundColor' => '#222', // Цвет фона меню
                'backgroundColorHover' => '#3d3d3d', // Цвет пункта меню при наведении
                'backgroundColorActive' => '#3d3d3d', // Цвет пункта меню активного
                'color' => '#fff', // Цвет текста меню
                'colorActive' => '#fff', // Цвет текста меню активного
                'colorHover' => '#fff', // Цвет текста меню при наведении
            ],
        ]);