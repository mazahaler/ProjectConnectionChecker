# ProjectConnectionChecker

The extension allows to **check** for compliance secrets.json file and secrets in the project.
The extension also allows to **check** db connections and mailing.

Requirements:
-------------


- PHP 7.4. and higher;
- Yii 2.0. and higher.



Installation:
-------------


The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mazahaler/project-connection-checker
```

or add

```
"mazahaler/project-connection-checker": "*"
```

to the require section of your composer.json.



Usage:
------

```php
<?php

use mazahaler\ProjectConnectionChecker\ProjectConnectionChecker;

/**
* Check secrets, db connections and mailing
 * @param 1: Root path of the project
 * @param 2: Path to secrets.json
 * @param 2: \yii\swiftmailer\Mailer class
 * @param 3: Array of db connections in format: ['Your connection title(used for error output)' => [Instance of \yii\db\Connection | \yii\mongodb\Connection]]
 */
ProjectConnectionChecker::checkAll(\Yii::getAlias('@app'), \Yii::getAlias('@app') . '/secrets/secrets.json', \Yii::$app->mailer, ['mysql' => [\Yii::$app->db], 'mongodb' => [\Yii::$app->mongodb]]);

// OR check it separately:

ProjectConnectionChecker::checkSecrets(\Yii::getAlias('@app'), \Yii::getAlias('@app') . '/secrets/secrets.json');

ProjectConnectionChecker::checkMailing(\Yii::$app->mailer);

ProjectConnectionChecker::checkConnections(['mysql' => [\Yii::$app->db], 'mongodb' => [\Yii::$app->mongodb]]);

```
